/**
 * MITechnique class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { App } from '../../../providers/app';
import { Injectable } from '@angular/core';
import { MITechniqueType } from './types/mi-technique-type';

@Injectable()

export class MITechnique {
	/**
	 * Route for retrieving the current mi technique
	 *
	 * @var string
	 */
	protected GET_ROUTE: string = "/aichat/technique/get/{slug}";

	/**
	 * Route for saving the current mi technique
	 *
	 * @var string
	 */
	protected SAVE_ROUTE: string = "/aichat/technique/save";

	/**
	 * Identifier for the mi technique
	 *
	 * @var MITechniqueType
	 */
	private slug: MITechniqueType = null;

	/**
	 * Name of mi technique
	 *
	 * @var string
	 */
	private name: string = "";

	/**
	 * Definition of the mi technique
	 *
	 * @var string
	 */
	private definition: string = "";

	/**
	 * User instruction for the mi technique
	 *
	 * @var string
	 */
	private user_instruction: string = "";

	/**
	 * AI instruction for the mi technique
	 *
	 * @var string
	 */
	private ai_instruction: string = "";

	/**
	 * Version of the mi technique
	 *
	 * @var string
	 */
	private version: string = "";

	/**
	 * Version slug of the mi technique version
	 *
	 * @var string
	 */
	private version_slug: string = "";

	/**
	 * Other data about the mi technique
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
	 * Get ai_instruction
	 *
	 * @param client_statement		Statement of the mock client to insert into the instructions
	 * @param client_bio			Bio of the mock client to insert into the instructions
	 *
	 * @return string
	 */
	public getAIInstruction(client_statement: string = "", client_bio: string = ""): string {
		var ai_instruction = this.ai_instruction;

		if (client_statement != "") {
			ai_instruction = ai_instruction.replace("{client_statement}", client_statement);
		}

		if (client_bio != "") {
			ai_instruction = ai_instruction.replace("{client_bio}", client_bio);
		}

		return ai_instruction;
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
	 * Get definition
	 *
	 * @return string
	 */
	public getDefinition(): string {
		return this.definition;
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
	 * Get name
	 *
	 * @return string
	 */
	public getName(): string {
		return this.name;
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
	 * Get slug
	 *
	 * @return string
	 */
	public getSlug(): string {
		return this.slug;
	}

	/**
	 * Get user_instruction
	 *
	 * @param client_name		Name of the client to insert into instructions
	 *
	 * @return string
	 */
	public getUserInstruction(client_name: string = ""): string {
		var user_instruction = this.user_instruction;

		if (client_name != "") {
			user_instruction = user_instruction.replace("{client_name}", client_name);
		}

		return user_instruction;
	}

	/**
	 * Get version
	 *
	 * @return string
	 */
	public getVersion(): string {
		return this.version;
	}

	/**
	 * Get version slug
	 *
	 * @return string
	 */
	public getVersionSlug(): string {
		return this.version_slug;
	}

	/**
	 * Load information about message
	 *
	 * @param object		Object to load from
	 *
	 * @return void
	 */
	loadFromObject(object: any): void {
		if (object.slug) { this.slug = object.slug; }
		if (object.name) { this.name = object.name; }
		if (object.definition) { this.definition = object.definition; }
		if (object.user_instruction) { this.user_instruction = object.user_instruction; }
		if (object.ai_instruction) { this.ai_instruction = object.ai_instruction; }
		if (object.version) { this.version = object.version; }
		if (object.version_slug) { this.version_slug = object.version_slug; }
		if (object.other_data) { try { this.other_data = JSON.parse(object.other_data); } catch (error) { this.other_data = object.other_data; }}
		if (object.create_date) { this.create_date = object.create_date; }
		if (object.delete_date) { this.delete_date = object.delete_date; }
	}

	/**
	 * Load mi technique from remote storage
	 *
	 * @param slug		Identifier of MITechnique to load
	 *
	 * @return Promise<void>
	 */
	async loadFromRemoteStorage(slug: MITechniqueType): Promise<void> {
		var route = this.GET_ROUTE.replace("{slug}", slug);
		var response = await this.app.requests.createGetApiRequest(route);
		var technique_data = JSON.parse(response.data)["mi_technique"];
		return this.loadFromObject(technique_data);
	}

	/**
	 * Return Interaction fields as an object
	 *
	 * @return object
	 */
	returnAsObject(): object {
		return {
			slug: this.slug,
			name: this.name,
			definition: this.definition,
			user_instruction: this.user_instruction,
			ai_instruction: this.ai_instruction,
			version: this.version,
			version_slug: this.version_slug,
			other_data: JSON.stringify(this.other_data),
			create_date: this.create_date,
			delete_date: this.delete_date
		}
	}

	/**
	 * Save MITechnique to remote storage
	 *
	 * @return Promise<string>
	 */
	async saveToRemoteStorage(): Promise<string> {
		var response = await this.app.requests.createPostApiRequest(this.SAVE_ROUTE, this.returnAsObject());
		var slug = JSON.parse(response.data).slug;
		this.setSlug(slug);
		return slug;
	}

	/**
	 * Set ai_instruction
	 *
	 * @param ai_instruction		Instructions for the AI
	 *
	 * @return void
	 */
	public setAIInstruction(ai_instruction: string): void {
		this.ai_instruction = ai_instruction;
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
	 * Set definition
	 *
	 * @param definition		Instructions for the AI
	 *
	 * @return void
	 */
	public setDefinition(definition: string): void {
		this.definition = definition;
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
	 * Set name
	 *
	 * @param name		new name to set to
	 *
	 * @return void
	 */
	public setName(name: string): void {
		this.name = name;
	}

	/**
	 * Set other_data
	 *
	 * @param other_data		Other data about the mi technique
	 *
	 * @return void
	 */
	public setOtherData(other_data: object): void {
		this.other_data = other_data;
	}

	/**
	 * Set slug
	 *
	 * @param slug		Slug of the mi technique
	 *
	 * @return void
	 */
	public setSlug(slug: MITechniqueType): void {
		this.slug = slug;
	}

	/**
	 * Set user_instruction
	 *
	 * @param user_instruction		Instructions for the user
	 *
	 * @return void
	 */
	public setUserInstruction(user_instruction: string): void {
		this.user_instruction = user_instruction;
	}

	/**
	 * Set version
	 *
	 * @param version 			Version of the mi technique
	 *
	 * @return void
	 */
	public setVersion(version: string): void {
		this.version = version;
	}

	/**
	 * Set version slug
	 *
	 * @param version_slug 			Version slug of the mi technique version
	 *
	 * @return void
	 */
	public setVersionSlug(version_slug: string): void {
		this.version_slug = version_slug;
	}
}