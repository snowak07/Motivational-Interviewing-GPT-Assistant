/**
 * Interaction collection
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AIAssistant } from './ai-assistant';
import { App } from '../../../providers/app';
import { Collection } from '../../../providers/core/collection';
import { Injectable } from '@angular/core';
import { Interaction } from './interaction';

@Injectable()

export class InteractionCollection extends Collection<Interaction> {
	/**
	 * Route for retrieving users by guid
	 *
	 * @var string
	 */
	protected GET_BY_USER_GUID_ROUTE: string = "/aichat/interactions/get-user-interactions/{user_guid}";

	/**
	 * Constructor for object
	 *
	 * @param app
	 * @param ai_assistant
	 *
	 * @return void
	 */
	constructor(
		protected app: App,
		protected ai_assistant: AIAssistant
	) {
		super();
	}

	/**
	 * Load interations for a user
	 *
	 * @param user_guid					guid of user to load interactions for
	 * @param client_prompt_guid		guid of the prompt to load interactions for
	 *
	 * @return Promise<void>
	 */
	async loadInteractionsByUserGuid(user_guid: string, client_prompt_guid: string = ""): Promise<void> {
		var route = this.GET_BY_USER_GUID_ROUTE.replace("{user_guid}", user_guid);
		var response = await this.app.requests.createGetApiRequest(route, { client_prompt_guid: client_prompt_guid, sort_by: "create_date desc" });
		var response_data = JSON.parse(response.data);

		for (var index in response_data.order) {
			var guid = response_data.order[index];
			var interaction = new Interaction(this.app, this.ai_assistant);
			var interaction_object = response_data.interactions[guid];
			interaction.loadFromObject(interaction_object);

			this.add(guid, interaction);
		}
	}
}