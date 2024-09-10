/**
 * Prompt class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { App } from '../../../providers/app';
import { Injectable } from '@angular/core';
import { MITechniqueType } from './types/mi-technique-type';

@Injectable()

export class Prompt {
	/**
	 * Route for retrieving the prompt
	 *
	 * @var string
	 */
	protected GET_ROUTE: string = "/aichat/prompt/get/{guid}";

	/**
	 * Route used for saving to remote storage
	 *
	 * @var string
	 */
	protected SAVE_ROUTE = "/aichat/prompt/save";

	/**
	 * Identifier for the prompt
	 *
	 * @var string
	 */
	private guid: string = "";

	/**
	 * Identifier of the associated background/client
	 *
	 * @var string
	 */
	private background_guid: string = "";

	/**
	 * Content of the prompt
	 *
	 * @var string
	 */
	private content: string = "";

	/**
	 * Motivation Interviewing technique associated with the prompt
	 *
	 * @var MITechniqueType
	 */
	private mi_technique_slug: MITechniqueType = null;

	/**
	 * Other data about the prompt
	 *
	 * @var object
	 */
	private other_data: object = { };

	/**
	 * Date message was created
	 *
	 * @var number
	 */
	private create_date: number = null;

	/**
	 * Date message was deleted
	 *
	 * @var number
	 */
	private delete_date: number = -1.0;

	/**
	 * Constructor for the object
	 *
	 * @param app
	 *
	 * @return void
	 */
	constructor(
		protected app: App,
	) {
		// Do nothing
	}

	/**
	 * Get background_guid
	 *
	 * @return string
	 */
	public getBackgroundGuid(): string {
		return this.background_guid;
	}

	/**
	 * Get content
	 *
	 * @return string
	 */
	public getContent(): string {
		return this.content;
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
	 * Get mi technique slug
	 *
	 * @return MITechniqueType
	 */
	public getMITechniqueType(): MITechniqueType {
		return this.mi_technique_slug;
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
	 * Get optional therapist prompt information
	 *
	 * @return string
	 */
	public getTherapistPrompt(): string {
		if (!this.other_data || !this.other_data['therapist_prompt']) {
			return "";
		}

		return this.other_data['therapist_prompt'];
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
		if (object.background_guid) { this.background_guid = object.background_guid; }
		if (object.content) { this.content = object.content; }
		if (object.mi_technique_slug) { this.mi_technique_slug = object.mi_technique_slug; }
		if (object.other_data) { this.other_data = object.other_data; }
		if (object.create_date) { this.create_date = object.create_date; }
		if (object.delete_date) { this.delete_date = object.delete_date; }
	}

	/**
	 * Load prompt from remote storage
	 *
	 * @param guid		Identifier of Client to load
	 *
	 * @return Promise<void>
	 */
	async loadFromRemoteStorage(guid: string): Promise<void> {
		var route = this.GET_ROUTE.replace("{guid}", guid);
		var response = await this.app.requests.createGetApiRequest(route);
		var prompt_data = JSON.parse(response.data)["mock_client_prompt"];
		return this.loadFromObject(prompt_data);
	}

	/**
	 * Return Interaction fields as an object
	 *
	 * @return object
	 */
	returnAsObject(): object {
		return {
			guid: this.guid,
			background_guid: this.background_guid,
			content: this.content,
			mi_technique_slug: this.mi_technique_slug,
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
		var guid = await this.app.requests.createPostApiRequest(this.SAVE_ROUTE, this.returnAsObject());
		this.setGuid(guid);
		return guid;
	}

	/**
	 * Set background_guid
	 *
	 * @param background_guid		Identifier for background info about the client
	 *
	 * @return void
	 */
	public setBackgroundGuid(background_guid: string): void {
		this.background_guid = background_guid;
	}

	/**
	 * Set content
	 *
	 * @param content		Content of the prompt
	 *
	 * @return void
	 */
	public setContent(content: string): void {
		this.content = content;
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
	 * Set mi_technique_slug
	 *
	 * @param mi_technique_slug		Technique used for the prompt
	 *
	 * @return void
	 */
	public setMITechniqueType(mi_technique_slug: MITechniqueType): void {
		this.mi_technique_slug = mi_technique_slug;
	}

	/**
	 * Set other_data
	 *
	 * @param other_data		Other data about the client
	 *
	 * @return void
	 */
	public setOtherData(other_data: object): void {
		this.other_data = other_data;
	}
}