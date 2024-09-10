/**
 * Interaction class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AIAssistant } from './ai-assistant';
import { App } from '../../../providers/app';
import { Injectable } from '@angular/core';

@Injectable()

export class Interaction {
	/**
	 * Route used for saving to remote storage
	 *
	 * @var string
	 */
	protected SAVE_ROUTE = "/aichat/interaction/save";

	/**
	 * Identifier for the interaction
	 *
	 * @var string
	 */
	protected guid: string = "";

	/**
	 * Identifier of the chat session
	 *
	 * @var string
	 */
	protected prompt_guid: string = "";

	/**
	 * User identifier of interaction
	 *
	 * @var string
	 */
	protected user_guid: string = "";

	/**
	 * User message of the interaction
	 *
	 * @var string
	 */
	protected user_message: string = "";

	/**
	 * AI message response of the interaction
	 *
	 * @var string
	 */
	protected system_message: string = "";

	/**
	 * Raw data response returned from GPT API
	 *
	 * @var object
	 */
	protected system_response: object = { };

	/**
	 * Data used in generating the GPT model used to generate an ai response
	 *
	 * @var object
	 */
	protected system_information: object = { };

	/**
	 * Other data associated with the interaction
	 *
	 * @var object
	 */
	protected other_data: object = { };

	/**
	 * Date message was created
	 *
	 * @var number
	 */
	protected create_date: number = null;

	/**
	 * Date message was deleted
	 *
	 * @var number
	 */
	protected delete_date: number = -1.0;

	/**
	 * Constructor for the object
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
		this.system_information = ai_assistant.getSystemInfo();
	}

	/**
	 * Add keyed items to System Information
	 *
	 * @param sys_info 			keyed information to add to System Information
	 *
	 * @return void
	 */
	public addSystemInformation(sys_info: object): void {
		// Combine objects with sys_info overwritting already existing properties
		this.system_information = {...sys_info, ...this.system_information};
	}

	/**
	 * Get create_date
	 *
	 * @return number
	 */
	public getCreateDate(): number {
		return this.create_date;
	}

	/**
	 * Get delete_date
	 *
	 * @return number
	 */
	public getDeleteDate(): number {
		return this.delete_date;
	}

	/**
	 * Get guid
	 *
	 * @return string
	 */
	public getGuid(): string {
		return this.guid;
	}

	/**
	 * Get other_data
	 *
	 * @return object
	 */
	public getOtherData(): object {
		return this.other_data;
	}

	/**
	 * Get prompt_guid
	 *
	 * @return string
	 */
	public getPromptGuid(): string {
		return this.prompt_guid;
	}

	/**
	 * Get system_information
	 *
	 * @return object
	 */
	public getSystemInformation(): object {
		return this.system_information;
	}

	/**
	 * Get system_message
	 *
	 * @return string
	 */
	public getSystemMessage(): string {
		return this.system_message;
	}

	/**
	 * Get system_response
	 *
	 * @return object
	 */
	public getSystemResponse(): object {
		return this.system_response;
	}

	/**
	 * Get user guid
	 *
	 * @return string
	 */
	public getUserGuid(): string {
		return this.user_guid;
	}

	/**
	 * Get user_message
	 *
	 * @return string
	 */
	public getUserMessage(): string {
		return this.user_message;
	}

	/**
	 * Load information about message
	 *
	 * @param object		Object to load from
	 *
	 * @return void
	 */
	loadFromObject(object: any): void {
		if (object.guid) { this.guid = object.guid; }
		if (object.client_prompt_guid) { this.prompt_guid = object.client_prompt_guid; }
		if (object.user_guid) { this.user_guid = object.user_guid; }
		if (object.user_message) { this.user_message = object.user_message; }
		if (object.system_message) { this.system_message = object.system_message; }
		if (object.system_response) { this.system_response = JSON.parse(object.system_response); }
		if (object.system_information) { this.system_information = JSON.parse(object.system_information); }
		if (object.other_data && typeof object.other_data == 'string') { this.other_data = JSON.parse(object.other_data); }
		if (object.other_data && typeof object.other_data == 'object') { this.other_data = object.other_data; }
		if (object.create_date) { this.create_date = object.create_date; }
		if (object.delete_date) { this.delete_date = object.delete_date; }
	}

	/**
	 * Return Interaction fields as an object
	 *
	 * @return object
	 */
	returnAsObject(): object {
		return {
			guid: this.guid,
			client_prompt_guid: this.prompt_guid,
			user_guid: this.user_guid,
			user_message: this.user_message,
			system_message: this.system_message,
			system_response: JSON.stringify(this.system_response),
			system_information: JSON.stringify(this.system_information),
			other_data: JSON.stringify(this.other_data),
			create_date: this.create_date,
			delete_date: this.delete_date
		}
	}

	/**
	 * Save Interaction to remote storage
	 *
	 * @return Promise<string>
	 */
	async saveToRemoteStorage(): Promise<string> {
		var response = await this.app.requests.createPostApiRequest(this.SAVE_ROUTE, this.returnAsObject());
		var guid = JSON.parse(response.data).guid;
		this.setGuid(guid);
		return guid;
	}

	/**
	 * Set create_date
	 *
	 * @param create_date		Creation date
	 *
	 * @return void
	 */
	public setCreateDate(create_date: number): void {
		this.create_date = create_date;
	}

	/**
	 * Set delete_date
	 *
	 * @param delete_date		Deletion date
	 *
	 * @return void
	 */
	public setDeleteDate(delete_date: number): void {
		this.delete_date = delete_date;
	}

	/**
	 * Set guid
	 *
	 * @param guid		new guid to set to
	 *
	 * @return void
	 */
	public setGuid(guid: string): void {
		this.guid = guid;
	}

	/**
	 * Set other_data
	 *
	 * @param data 		data to set other_data to
	 *
	 * @return void
	 */
	public setOtherData(data: object): void {
		this.other_data = data;
	}

	/**
	 * Set prompt_guid
	 *
	 * @param prompt_guid		Identifier of the session
	 *
	 * @return void
	 */
	public setPromptGuid(prompt_guid: string): void {
		this.prompt_guid = prompt_guid;
	}

	/**
	 * Set system_information
	 *
	 * @param system_information		Settings used to generate the response
	 *
	 * @return void
	 */
	public setSystemInformation(system_information: object): void {
		this.system_information = system_information;
	}

	/**
	 * Set system_message
	 *
	 * @param system_message		text response of the ai
	 *
	 * @return void
	 */
	public setSystemMessage(system_message: string): void {
		this.system_message = system_message;
	}

	/**
	 * Set system_response
	 *
	 * @param system_response		Raw response data returned by the ai
	 *
	 * @return void
	 */
	public setSystemResponse(system_response: object): void {
		this.system_response = system_response;
	}

	/**
	 * Set user guid
	 *
	 * @param user_guid		new user_guid to set to
	 *
	 * @return void
	 */
	public setUserGuid(user_guid: string): void {
		this.user_guid = user_guid;
	}

	/**
	 * Set user_message
	 *
	 * @param user_message		Text prompt sent to the ai by the user
	 *
	 * @return void
	 */
	public setUserMessage(user_message: string): void {
		this.user_message = user_message;
	}
}