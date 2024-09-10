/**
 * Client class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { App } from '../../../providers/app';
import { Injectable } from '@angular/core';

@Injectable()

export class Client {
	/**
	 * Route for retrieving the current client/background
	 *
	 * @var string
	 */
	protected GET_ROUTE: string = "/aichat/background/get/{guid}";

	/**
	 * Route used for saving to remote storage
	 *
	 * @var string
	 */
	protected SAVE_ROUTE = "/aichat/background/save";

	/**
	 * Identifier for the client
	 *
	 * @var string
	 */
	private guid: string = "";

	/**
	 * Name of client
	 *
	 * @var string
	 */
	private client_name: string = "";

	/**
	 * Profile picture uri link
	 *
	 * @var string
	 */
	private profile_picture: string = "";

	/**
	 * Background info about the client
	 *
	 * @var string
	 */
	private background_info: string = "";

	/**
	 * Other data about the client
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
	 * Get background_info
	 *
	 * @return string
	 */
	public getBackgroundInfo(): string {
		return this.background_info;
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
	 * Get client_name
	 *
	 * @return string
	 */
	public getName(): string {
		return this.client_name;
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
	 * Get profile_picture
	 *
	 * @return string
	 */
	public getProfilePicture(): string {
		return this.profile_picture;
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
		if (object.client_name) { this.client_name = object.client_name; }
		if (object.profile_picture) { this.profile_picture = object.profile_picture; }
		if (object.background_info) { this.background_info = object.background_info; }
		if (object.other_data) { this.other_data = JSON.parse(object.other_data); }
		if (object.create_date) { this.create_date = object.create_date; }
		if (object.delete_date) { this.delete_date = object.delete_date; }
	}

	/**
	 * Load client from remote storage
	 *
	 * @param guid		Identifier of Client to load
	 *
	 * @return Promise<void>
	 */
	async loadFromRemoteStorage(guid: string): Promise<void> {
		var route = this.GET_ROUTE.replace("{guid}", guid);
		var response = await this.app.requests.createGetApiRequest(route);
		var client_data = JSON.parse(response.data)["mock_client_background"];
		return this.loadFromObject(client_data);
	}

	/**
	 * Return Interaction fields as an object
	 *
	 * @return object
	 */
	returnAsObject(): object {
		return {
			guid: this.guid,
			client_name: this.client_name,
			profile_picture: this.profile_picture,
			background_info: this.background_info,
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
	 * Set background_info
	 *
	 * @param background_info		Background info about the client
	 *
	 * @return void
	 */
	public setBackgroundInfo(background_info: string): void {
		this.background_info = background_info;
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
	 * Set client_name
	 *
	 * @param client_name		Name of the client
	 *
	 * @return void
	 */
	public setName(client_name: string): void {
		this.client_name = client_name;
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

	/**
	 * Set profile_picture
	 *
	 * @param profile_picture		Profile picture uri of the client
	 *
	 * @return void
	 */
	public setProfilePicture(profile_picture: string): void {
		this.profile_picture = profile_picture;
	}
}