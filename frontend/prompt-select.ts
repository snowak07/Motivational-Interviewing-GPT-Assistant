/**
 * Component and page for handling the Client List page
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AlertController, IonicPage, LoadingController, NavController, NavParams } from 'ionic-angular';
import { App } from '../../providers/app';
import { AppController } from '../../providers/core/app-controller';
import { Client } from './providers/client';
import { ClientPrompt } from './providers/interfaces/client-prompt';
import { Component } from '@angular/core';
import { MITechnique } from './providers/mi-technique';
import { MITechniqueType } from './providers/types/mi-technique-type';
import { Prompt } from './providers/prompt';

@IonicPage({
	name: "assistant/prompt-select",
	segment: "assistant/prompt-select/",
	defaultHistory: ["home"]
})

@Component({
	selector: 'page-prompt-select',
	templateUrl: 'prompt-select.html'
})

export class PromptSelectPage extends AppController {
	/**
	 * Retrieve clients from remote storage by mi technique
	 *
	 * @var string
	 */
	protected GET_CLIENTS_BY_TECHNIQUE_ROUTE: string = "/aichat/prompts/technique/get";

	/**
	 * Retrieve prompts from remote storage by client guid
	 *
	 * @var string
	 */
	protected GET_PROMPTS_BY_CLIENT_ROUTE: string = "/aichat/prompts/background/get/{guid}";

	/**
	 * MI Technique associated with the prompts
	 *
	 * @var mi_technique
	 */
	protected mi_technique: MITechnique = null;

	/**
	 * Ordered list of available clients
	 *
	 * @var ClientPrompt
	 */
	protected ordered_client_prompts: ClientPrompt[] = [ ];

	/**
	 * Header of the page determined dynamically based on if the page is a list of prompts or clients
	 *
	 * @var string
	 */
	protected page_header: string = "";

	/**
	 * Constructor for the page
	 *
	 * @param alert_controller
	 * @param app
	 * @param loading_controller
	 * @param nav_controller
	 * @param nav_params
	 *
	 * @return void
	 */
	constructor(
		protected alert_controller: AlertController,
		protected app: App,
		protected loading_controller: LoadingController,
		protected nav_controller: NavController,
		protected nav_params: NavParams,
	) {
		super(app, nav_controller);

		this.setUseData(
			"mi-assistant",
			"viewing prompt list",
			{}
		);
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
	 * Handle prompt click and redirect to the conversation page or to the client bio popup.
	 *
	 * @param event 		Event containing html element data
	 * @param client		Selected client
	 * @param prompt		Selected prompt
	 *
	 * @return void
	 */
	handlePromptCardClick(event: any, client: Client, prompt: Prompt): void {
		if (event.target.className.includes("bio")) {
			this.handleBioButtonClick(client);
		} else {
			this.handlePromptClick(prompt.getGuid(), client.getGuid(), prompt.getMITechniqueType())
		}
	}

	/**
	 * Handle selecting a prompt
	 *
	 * @param prompt_guid		guid of the prompt
	 * @param client_guid		guid of client being selected
	 * @param mi_technique		slug of mi technique of selected prompt
	 *
	 * @return void
	 */
	handlePromptClick(prompt_guid: string, client_guid: string, mi_technique: MITechniqueType): void {
		this.app.helpers.handleNavigation(this.nav_controller, "assistant/conversation", { "prompt_guid": prompt_guid, "client_guid": client_guid, "mi_technique_slug": mi_technique });
	}

	/**
	 * Handle when the view appears
	 *
	 * @return void
	 */
	ionViewWillEnter(): void {
		super.ionViewWillEnter();

		if (this.nav_params.get("mi_technique")) {
			if (this.nav_params.get("mi_technique") in this.app.globals.mi_technique_title_map) {
				this.page_header = this.app.globals.mi_technique_title_map[this.nav_params.get("mi_technique")];
			}

			this.mi_technique = new MITechnique(this.app);
			this.mi_technique.loadFromRemoteStorage(this.nav_params.get("mi_technique"));

			// Handle new Techniques
			if (this.page_header == "") {
				this.page_header = this.mi_technique.getName();
			}

			this.loadClients(this.nav_params.get("mi_technique"));

		} else if (this.nav_params.get("client_guid")) {
			this.page_header = this.app.language.translateString("ai_assistant_prompt_select_page_title");

			this.loadPrompts(this.nav_params.get("client_guid"));
		}
	}

	/**
	 * Load clients by mi_technique
	 *
	 * @param mi_technique		slug of mi_technique to load prompts/clients for
	 *
	 * @return Promise<void>
	 */
	async loadClients(mi_technique: MITechniqueType): Promise<void> {
		this.ordered_client_prompts = [];

		try {
			var response = await this.app.requests.createGetApiRequest(this.GET_CLIENTS_BY_TECHNIQUE_ROUTE, { mi_technique_slug: mi_technique });
			var response_data = JSON.parse(response.data);

			for (var index in response_data.order) {
				var prompt_guid = response_data.order[index];

				var prompt = new Prompt(this.app);
				var prompt_object = response_data.prompts[prompt_guid];
				prompt.loadFromObject(prompt_object);

				var client = new Client(this.app);
				var client_object = prompt_object["background"];
				client.loadFromObject(client_object);

				var client_prompt: ClientPrompt = {
					client: client,
					prompt: prompt
				};

				this.ordered_client_prompts.push(client_prompt);
			}

		} catch (error) {
			console.log("[client-select.ts] Error loading clients", error);
		}
	}

	/**
	 * Load prompt by client_guid
	 *
	 * @param client_guid 		Guid of client to load prompts for
	 *
	 * @return Promise<void>
	 */
	async loadPrompts(client_guid: string): Promise<void> {
		this.ordered_client_prompts = [];

		try {
			var route = this.GET_PROMPTS_BY_CLIENT_ROUTE.replace("{guid}", client_guid);
			var response = await this.app.requests.createGetApiRequest(route, { client_guid: client_guid });
			var response_data = JSON.parse(response.data);

			var client_object = response_data.mock_client_background;
			var client = new Client(this.app);
			client.loadFromObject(client_object);

			for (var index in response_data.order) {
				var prompt_guid = response_data.order[index];

				var prompt = new Prompt(this.app);
				var prompt_object = response_data.prompts[prompt_guid];
				prompt.loadFromObject(prompt_object);

				var client_prompt: ClientPrompt = {
					client: client,
					prompt: prompt
				};

				this.ordered_client_prompts.push(client_prompt);
			}

		} catch (error) {
			console.log("[client-select.ts] Error loading clients", error);
		}
	}
}