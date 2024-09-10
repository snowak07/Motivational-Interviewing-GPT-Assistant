/**
 * Component and page for handling Conversation page
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AIAssistant } from './providers/ai-assistant';
import { AlertController, IonicPage, LoadingController, NavController, NavParams, ToastController } from 'ionic-angular';
import { App } from '../../providers/app';
import { AppController } from '../../providers/core/app-controller';
import { Client } from './providers/client';
import { Component } from '@angular/core';
import { DeploymentID } from './providers/types/gpt-deployment-id';
import { Interaction } from './providers/interaction';
import { InteractionCollection } from './providers/interaction-collection';
import { MITechnique } from './providers/mi-technique';
import { Prompt } from './providers/prompt';

@IonicPage({
	name: "assistant/conversation",
	segment: "assistant/conversation/",
	defaultHistory: ["home"]
})

@Component({
	selector: 'page-assistant-conversation',
	templateUrl: 'conversation-page.html'
})

export class AssistantConversationPage extends AppController {
	/**
	 * AI assistant object. Handles serving up messages from a GPT
	 *
	 * @var AIAssistant
	 */
	protected ai_assistant: AIAssistant = null;

	/**
	 * Mock client currently being interacted with
	 *
	 * @var Client
	 */
	protected client: Client = null;

	/**
	 * Prompt the user is interacting with
	 *
	 * @var Prompt
	 */
	protected prompt: Prompt = null;

	/**
	 * Interaction collection for managing interaction data
	 *
	 * @var InteractionCollection
	 */
	protected interaction_collection: InteractionCollection = null;

	/**
	 * Interaction waiting for a response from the ai assistant
	 *
	 * @var Interaction
	 */
	protected interaction: Interaction = null;

	/**
	 * Technique being practiced
	 *
	 * @var MITechnique
	 */
	protected mi_technique: MITechnique = null;

	/**
	 * User input textarea value
	 *
	 * @var string
	 */
	protected user_input: string = "";

	/**
	 * Constructor for the page
	 *
	 * @param alert_controller
	 * @param app
	 * @param loading_controller
	 * @param nav_controller
	 * @param nav_params
	 * @param toast_controller
	 *
	 * @return void
	 */
	constructor(
		protected alert_controller: AlertController,
		protected app: App,
		protected loading_controller: LoadingController,
		protected nav_controller: NavController,
		protected nav_params: NavParams,
		protected toast_controller: ToastController
	) {
		super(app, nav_controller);

		this.setUseData(
			"mi-assistant",
			"viewing conversation",
			{
				prompt_guid: this.nav_params.get("prompt_guid")
			}
		);
	}

	/**
	 * Generate response from the AI assistant
	 * TODO change return type of Promise<[string, object]> (requires linter update)
	 *
	 * @param user_input			Input from user used to generate next AI response
	 * @param system_instructions	Instructions for the GPT to inform how to generate a response.
	 *
	 * @return Promise<any>
	 */
	generateAIAssistantResponse(user_input: string, system_instructions: string): Promise<any> {
		return this.ai_assistant.generateResponse(user_input, system_instructions);
	}

	/**
	 * Handle the "Read full bio" button click
	 *
	 * @param client		Client user is viewing bio for
	 *
	 * @return void
	 */
	handleBioButtonClick(client: Client): void {
		var confirm_box = this.alert_controller.create({
			cssClass: "blueAlert",
			title: client.getName(),
			message: client.getBackgroundInfo(),
			buttons: [
				{
					cssClass: "confirmButton",
					text: this.app.language.translateString("close"),
					handler: () => {
						// Do nothing
					}
				}
			]
		});

		confirm_box.present();
	}

	/**
	 * Handle the user selecting to a different deployment ID
	 *
	 * @param event		Event containing deployment id
	 *
	 * @return void
	 */
	handleDeploymentSwitch(event: DeploymentID): void {
		this.ai_assistant.setDeploymentID(event);
	}

	/**
	 * Handle redirect to the feedback popup
	 *
	 * @return Promise<void>
	 */
	async handleElearningButtonClick(): Promise<void> {
		// Add user metadata for use in reconstructing navigation data after elearning page error
		this.app.user.addMetadata('elearning_return_segment', {
			client_guid: this.nav_params.get('client_guid'),
			mi_technique_slug: this.nav_params.get('mi_technique_slug'),
			prompt_guid: this.nav_params.get('prompt_guid')
		}, false);

		this.app.helpers.handleNavigation(this.nav_controller, 'elearning/player', { '1': this.mi_technique.getSlug() })
	}

	/**
	 * Handle redirect to the feedback popup
	 *
	 * @return Promise<void>
	 */
	async handleFeedbackButtonClick(): Promise<void> {
		// Add user metadata for use in returning to this page from the feedback survey
		this.app.user.addMetadata('feedback_return_segment', {
			client_guid: this.nav_params.get('client_guid'),
			mi_technique_slug: this.nav_params.get('mi_technique_slug'),
			prompt_guid: this.nav_params.get('prompt_guid')
		}, false);

		this.app.helpers.handleNavigation(this.nav_controller, 'surveys/survey', {'1': 'mymi-feedback'});
	}

	/**
	 * Handle adding the user_survey_guid to the interaction
	 *
	 * @param user_feedback_survey_guid		guid of the feedback survey associated with this interaction
	 *
	 * @return Promise<void>
	 */
	async handleSaveSurveyToInteraction(user_feedback_survey_guid: string): Promise<void> {
		var other_data = this.interaction.getOtherData();
		other_data['feedback_survey_guid'] = user_feedback_survey_guid;
		this.interaction.setOtherData(other_data);
		await this.interaction.saveToRemoteStorage();
	}

	/**
	 * Handle when Send Reply button is clicked
	 *
	 * @return Promise<void>
	 */
	async handleSendReplyButtonClick(): Promise<void> {
		this.app.alerts.presentLoadingAlert(this.loading_controller, this.app.language.translateString("loading_please_wait"));

		if (this.user_input == "") {
			return;
		}

		var user_input = this.user_input;
		this.user_input = ""; // Clear user prompt text box content

		try {
			// Display user prompt
			await this.sendReply(user_input);

			// Generate AI response
			var [ai_message, ai_response] = await this.generateAIAssistantResponse(user_input, this.mi_technique.getAIInstruction(this.prompt.getContent(), this.client.getBackgroundInfo()));

			// Display AI response
			await this.sendAIAssistantReply(ai_message, ai_response);

			this.app.alerts.dismissLoadingAlert();

		} catch (error) {
			console.log("[conversation-page.ts] handleSendReplyButtonClick error: ", error);
			this.app.alerts.dismissLoadingAlert();
		}
	}

	/**
	 * Handle the user reseting the interaction in order to try again
	 *
	 * @return void
	 */
	handleTryAgainButtonClick(): void {
		this.app.alerts.presentLoadingAlert(this.loading_controller, this.app.language.translateString("loading_please_wait"));

		this.interaction = null;

		// Scroll to text box
		setTimeout(() => {
			this.app.helpers.scrollToElement(document.getElementById("user-input-text-box"));
		}, 0);

		this.app.alerts.dismissLoadingAlert();
	}

	/**
	 * Handle if the user has the correct permissions or not
	 *
	 * @return Promise<void>
	 */
	async ionViewCanEnter(): Promise<void> {
		await super.ionViewCanEnter();

		if (!this.app.user.isPermissionEnabled("has-ai-assistant-access")) {
			setTimeout(() => {
				this.app.helpers.handleNavigation(this.nav_controller, "home", { "error": "unauthorized" }, true);
			}, 100);

			throw new Error("Invalid Permissions");
		}
	}

	/**
	 * Handle when view has appeared
	 *
	 * @return Promise<void>
	 */
	async ionViewWillEnter(): Promise<void> {
		this.app.alerts.presentLoadingAlert(this.loading_controller, this.app.language.translateString("loading_please_wait"));

		super.ionViewWillEnter();

		var client_guid = this.nav_params.get("client_guid");
		var prompt_guid = this.nav_params.get("prompt_guid");
		var mi_technique_slug = this.nav_params.get("mi_technique_slug");

		if (
			!client_guid &&
			!prompt_guid &&
			!mi_technique_slug
		) {
			// Retrieve nav data after returning from survey
			var return_segment_data = this.app.user.getMetadata('feedback_return_segment');
			client_guid = return_segment_data.client_guid;
			prompt_guid = return_segment_data.prompt_guid;
			mi_technique_slug = return_segment_data.mi_technique_slug;

			// Reconstruct the navigation stack
			this.nav_controller.insert(0, 'assistant/prompt-select', { mi_technique: mi_technique_slug });
			this.nav_controller.insert(0, 'assistant/technique-list');
			this.nav_controller.insert(0, 'home');
		}

		var promises = [];

		// Load search document for index searching
		this.ai_assistant = new AIAssistant(this.app, "gpt-4-32k-2023-12-08");
		promises.push(this.ai_assistant.loadSearchDocument());

		// Load mock client background
		this.client = new Client(this.app);
		promises.push(this.client.loadFromRemoteStorage(client_guid));

		// Load mock client prompt
		this.prompt = new Prompt(this.app);
		promises.push(this.prompt.loadFromRemoteStorage(prompt_guid));

		// Load mi technique
		this.mi_technique = new MITechnique(this.app);
		promises.push(this.mi_technique.loadFromRemoteStorage(mi_technique_slug));

		// Run independant processes in parallel to speed up load time
		await Promise.all(promises);

		// Load previously completed interaction
		this.interaction_collection = new InteractionCollection(this.app, this.ai_assistant);
		await this.interaction_collection.loadInteractionsByUserGuid(this.app.user.getGuid(), this.nav_params.get("prompt_guid"));
		this.interaction = this.interaction_collection.getAsArray()[0]; // Select most recent interaction

		// Handle returning from the feedback survey and saving the user_survey_guid
		if (this.nav_params.get('1')) {
			await this.handleSaveSurveyToInteraction(this.nav_params.get('1'));
		}

		// Scroll to text box
		setTimeout(() => {
			this.app.helpers.scrollToElement(document.getElementById("user-input-text-box"));
		}, 0);

		this.app.alerts.dismissLoadingAlert();
	}

	/**
	 * Add the ai response to the interaction in order to be displayed
	 *
	 * @param ai_message		Message generated by the AI
	 * @param ai_response		Raw data response from the AI
	 *
	 * @return Promise<void>
	 */
	async sendAIAssistantReply(ai_message: string, ai_response: object): Promise<void> {
		this.interaction.setSystemMessage(ai_message);
		this.interaction.setSystemResponse(ai_response);

		// Add prompt used to generate system message
		this.interaction.addSystemInformation({
			prompt: this.mi_technique.getAIInstruction(this.prompt.getContent(), this.client.getBackgroundInfo()),
			version: this.mi_technique.getVersion(),
			version_slug: this.mi_technique.getVersionSlug()
		});

		try {
			await this.interaction.saveToRemoteStorage();

			// Scroll to new ai message
			setTimeout(() => {
				this.app.helpers.scrollToElement(document.getElementById("assistant-message"));
			}, 0)

		} catch (error) {
			console.log("[conversation-page.ts] Failed to save interaction");
		}
	}

	/**
	 * Add the user prompt to a new interaction in order to be displayed
	 *
	 * @param user_prompt		User_prompt sent by the user to the ai
	 *
	 * @return Promise<void>
	 */
	async sendReply(user_prompt: string): Promise<void> {
		this.interaction = new Interaction(this.app, this.ai_assistant);
		this.interaction.setPromptGuid(this.prompt.getGuid());
		this.interaction.setUserGuid(this.app.user.getGuid());
		this.interaction.setUserMessage(user_prompt);
		this.interaction.setCreateDate(this.app.helpers.getUnixTimeInSeconds());
	}
}
