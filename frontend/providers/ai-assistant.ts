/**
 * AI Assistant class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { App } from '../../../providers/app';
import { DeploymentID } from './types/gpt-deployment-id';
import { Injectable } from "@angular/core";
import { Interaction } from './interaction';

const { OpenAIClient, AzureKeyCredential } = require("@azure/openai");

@Injectable()

export class AIAssistant {
	/**
	 * API key for the Azure OpenAI service
	 *
	 * @var string
	 */
	protected readonly AZURE_API_KEY: string = "<insert azure api key here>";

	/**
	 * Deployment ID of Embedding resource
	 *
	 * @var string
	 */
	protected readonly EMBEDDING_DEPLOYMENT_ID = "<Embedding deployment ID>";

	/**
	 * API endpoint for the Azure OpenAI service
	 *
	 * @var string
	 */
	protected readonly ENDPOINT: string = "<Azure Endpoint>";

	/**
	 * Route for retrieving search document elements
	 *
	 * @var string
	 */
	protected readonly GET_SEARCH_DOC_ROUTE: string = "aichat/document/elements/get/{guid}";

	/**
	 * Route for generating Azure OpenAI embeddings
	 *
	 * @var string
	 */
	protected readonly GENERATE_EMBEDDING_ROUTE: string = "<Azure Endpoint>" + "/openai/deployments/" + this.EMBEDDING_DEPLOYMENT_ID + "/embeddings?api-version=2023-05-15";

	/**
	 * Identifier of primary search document stored in the database.
	 *
	 * @var string
	 */
	protected readonly SEARCH_DOCUMENT_GUID: string = "98470f82-a6c6-47ba-1cc9-2baa1f4bf36b";

	/**
	 * Current Deployment ID
	 *
	 * @var DeploymentID
	 */
	protected gpt_deployment_id: DeploymentID = null;

	/**
	 * Array of search document elements that contain text and embeddings of each element
	 *
	 * @var SearchDocumentElement[]
	 */
	protected search_document: SearchDocumentElement[] = [ ];

	/**
	 * Constructor for the object
	 *
	 * @param app
	 * @param deployment_id
	 *
	 * @return void
	 */
	constructor(
		protected app: App,
		protected deployment_id: DeploymentID
	) {
		this.gpt_deployment_id = deployment_id
	}

	/**
	 * Calculate the 'distance' or similarity between two vectors. Range between 0 and 1 with a
	 * Return value of 1 meaning the vectors are exactly the same.
	 *
	 * @param vector_a		first vector to compare
	 * @param vector_b		second vector to compare
	 *
	 * @return number
	 */
	calculateCosineSimilarity(vector_a: number[], vector_b: number[]): number {
		var similarity = require('compute-cosine-similarity');
		return similarity(vector_a, vector_b);
	}

	/**
	 * Generate embedding for the inputed text
	 *
	 * @param text		text string to embed
	 *
	 * @return Promise<any>
	 */
	async generateEmbedding(text: string): Promise<any> {
		var data = await this.app.requests.createRequest("POST", this.GENERATE_EMBEDDING_ROUTE,
			{ input: text },
			{ 'Content-Type': 'application/json', 'api-key': this.AZURE_API_KEY });

		var embedding = JSON.parse(data.data).data[0].embedding;
		return embedding;
	}

	/**
	 * Handles generating a response back from a GPT with text (user_input) as input
	 * TODO change return type of Promise<[string, object]> (requires linter update)
	 *
	 * user_input, assistant_input, system_instructions
	 *
	 * @param user_input		User conversation input
	 * @param system_instructions	Instructions for system response generation
	 * @param use_index_search	Whether or not to use index search feature for customized GPT results
	 *
	 * @return Promise<any>
	 */
	async generateResponse(user_input: string, system_instructions: string, use_index_search: boolean = true): Promise<any> {
		var ai_message = "";

		var document_config_statement = "";
		if (use_index_search) {
			var document_element = await this.indexSearch(user_input);
			document_config_statement = "Use the following information to help inform your response: '" + document_element.text + "'";
		}

		var history = [];
		history.push({ role: "system", content: system_instructions + "\n" + document_config_statement });
		history.push({ role: "user", content: user_input });

		const debug_history: string[] = history.map((element) => { return element.role + ": " + element.content + "\n" });
		console.log("[ai-assistant.ts] history: ", debug_history);

		try {
			const client = new OpenAIClient(this.ENDPOINT, new AzureKeyCredential(this.AZURE_API_KEY));

			const response = await client.getChatCompletions(this.gpt_deployment_id, history);
			console.log("[ai-assistant.ts] generateResponse response: ", response);

			for (const choice of response.choices) {
				ai_message += choice.message.content;
			}

			// Replace any single newline with two new lines to make response more readable
			ai_message = ai_message.replace(/(^|[^\n])\n(?!\n)/g, "$1\n\n");

			return [ai_message, response];

		} catch (error) {
			console.log("[ai-assistant.ts] Azure Error: ", error);

			if (error.code == "429") {
				// Handle exceeded rate limit error
				var previous_deployment_id = this.gpt_deployment_id;
				this.setDeploymentID("MyMI-gpt35");
				var response_data = await this.generateResponse(user_input, system_instructions, use_index_search);
				this.setDeploymentID(previous_deployment_id);
				return response_data;
			}
		}
	}

	/**
	 * Retrieve system info used to generate ai model
	 *
	 * @return object
	 */
	getSystemInfo(): object {
		return {
			api_key: this.AZURE_API_KEY,
			deployment_id: this.gpt_deployment_id,
			endpoint: this.ENDPOINT
		}
	}

	/**
	 * Search for relevant document snippets in the Search Document
	 *
	 * @param user_query		Query to search for
	 *
	 * @return Promise<SearchDocumentElement>
	 */
	async indexSearch(user_query: string): Promise<SearchDocumentElement> {
		if (this.search_document.length == 0) {
			await this.loadSearchDocument();
		}

		var user_query_embedding = await this.generateEmbedding(user_query);

		// For each document snippet, compare embedding to the user_query_embedding to determine which has the most relevance
		var highest_similarity = 0;
		var highest_similarity_index;
		for (var index in this.search_document) {
			var current_element = this.search_document[index];

			var similarity = this.calculateCosineSimilarity(user_query_embedding, current_element.embedding);

			if (similarity > highest_similarity) {
				highest_similarity = similarity;
				highest_similarity_index = index;
			}
		}

		return this.search_document[highest_similarity_index];
	}

	/**
	 * Load search document
	 *
	 * @return Promise<void>
	 */
	async loadSearchDocument(): Promise<void> {
		var route = this.GET_SEARCH_DOC_ROUTE.replace("{guid}", this.SEARCH_DOCUMENT_GUID);

		try {
			var response = await this.app.requests.createGetApiRequest(route);
		} catch (error) {
			console.log("[ai-assistant.ts] Error retrieving search document", error);
		}

		var response_data = JSON.parse(response.data);

		for (var index in response_data.order) {
			var element_guid = response_data.order[index];

			var search_document_element: SearchDocumentElement = response_data.elements[element_guid];
			search_document_element.embedding = JSON.parse(response_data.elements[element_guid].embedding);
			this.search_document.push(search_document_element);
		}
	}

	/**
	 * Set GPT deployment ID to 3.5 or 4
	 *
	 * @param deployment_id		Deployment ID string to assign
	 *
	 * @return void
	 */
	setDeploymentID(deployment_id: DeploymentID): void {
		this.gpt_deployment_id = deployment_id;
	}
}