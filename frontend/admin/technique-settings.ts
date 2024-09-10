/**
 * Component and page for handling the Techniques Settings page
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AlertController, IonicPage, LoadingController, NavController, NavParams, ToastController } from 'ionic-angular';
import { App } from '../../../providers/app';
import { AppController } from '../../../providers/core/app-controller';
import { Component } from '@angular/core';
import { MITechnique } from '../providers/mi-technique';

@IonicPage({
	name: "assistant/technique-settings",
	segment: "assistant/technique-settings/",
	defaultHistory: ["home"]
})

@Component({
	selector: 'page-technique-settings',
	templateUrl: 'technique-settings.html'
})

export class TechniqueSettingsPage extends AppController {
	/**
	 * Text value of ai instruction field
	 *
	 * @var string
	 */
	protected ai_instruction_field_value: string = "";

	/**
	 * Text value of defintion field
	 *
	 * @var string
	 */
	protected definition_field_value: string = "";

	/**
	 * MITechnique being edited
	 *
	 * @var MITechnique
	 */
	protected mi_technique: MITechnique = null;

	/**
	 * Text value of name field
	 *
	 * @var string
	 */
	protected name_field_value: string = "";

	/**
	 * Text value of user instruction field
	 *
	 * @var string
	 */
	protected user_instruction_field_value: string = "";

	/**
	 * Text value of the version field
	 *
	 * @var string
	 */
	protected version_field_value: string = "";

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
			"editing mi technique",
			{ mi_technique: nav_params.get("mi_technique") }
		);
	}

	/**
	 * Handle saving the new fields to the mi_technique
	 *
	 * @return Promise<void>
	 */
	async handleSaveButtonClick(): Promise<void> {
		if (this.name_field_value == "") {
			this.app.alerts.presentSimpleAlert(this.app, this.alert_controller, this.app.language.translateString("ai_assistant_technique_settings_page_invalid_name"));
			return;
		}

		if (this.definition_field_value == "") {
			this.app.alerts.presentSimpleAlert(this.app, this.alert_controller, this.app.language.translateString("ai_assistant_technique_settings_page_invalid_definition"));
			return;
		}

		if (this.user_instruction_field_value == "") {
			this.app.alerts.presentSimpleAlert(this.app, this.alert_controller, this.app.language.translateString("ai_assistant_technique_settings_page_invalid_user_instruction"));
			return;
		}

		if (this.ai_instruction_field_value == "") {
			this.app.alerts.presentSimpleAlert(this.app, this.alert_controller, this.app.language.translateString("ai_assistant_technique_settings_page_invalid_ai_instruction"));
			return;
		}

		if (this.version_field_value == "") {
			this.app.alerts.presentSimpleAlert(this.app, this.alert_controller, this.app.language.translateString("ai_assistant_technique_settings_page_invalid_version"));
			return;
		}

		try {
			await this.alert_controller.create({
				cssClass: "blueAlert",
				title: this.app.language.translateString("ai_assistant_technique_settings_page_save_confirm_message"),
				buttons: [
					{
						cssClass: "denyButton",
						text: this.app.language.translateString("no"),
						role: 'cancel',
						handler: (() => {
							// Do nothing
						})
					},
					{
						cssClass: "confirmButton",
						text: "yes",
						handler: (() => {
							this.saveTechnique();
						})
					}]
			}).present();

		} catch (error) {
			console.log("[technique-settings.ts] handleSaveButtonClick error", error);
		}
	}

	/**
	 * Handle if the user can enter the page
	 *
	 * @return Promise<void>
	 */
	async ionViewCanEnter(): Promise<void> {
		await super.ionViewCanEnter();

		if (!this.app.user.isPermissionEnabled("administrates-ai-assistant")) {
			setTimeout(() => {
				this.app.helpers.handleNavigation(this.nav_controller, "home", { "error": "unauthorized" }, true);
			}, 100);

			throw new Error("Invalid Permissions");
		}
	}

	/**
	 * Handle when the view appears
	 *
	 * @return Promise<void>
	 */
	async ionViewWillEnter(): Promise<void> {
		super.ionViewWillEnter();

		if (this.nav_params.get("mi_technique")) {
			this.mi_technique = new MITechnique(this.app);
			await this.mi_technique.loadFromRemoteStorage(this.nav_params.get("mi_technique"));

			this.name_field_value = this.mi_technique.getName();
			this.definition_field_value = this.mi_technique.getDefinition();
			this.user_instruction_field_value = this.mi_technique.getUserInstruction();
			this.ai_instruction_field_value = this.mi_technique.getAIInstruction();
			this.version_field_value = this.mi_technique.getVersion();
		}
	}

	/**
	 * Handle saving a technique
	 *
	 * @return void
	 */
	saveTechnique(): void {
		this.app.alerts.presentLoadingAlert(this.loading_controller, this.app.language.translateString("saving_please_wait"));
		this.mi_technique.setName(this.name_field_value);
		this.mi_technique.setDefinition(this.definition_field_value);
		this.mi_technique.setUserInstruction(this.user_instruction_field_value);
		this.mi_technique.setAIInstruction(this.ai_instruction_field_value);
		this.mi_technique.setVersion(this.version_field_value);

		this.mi_technique.saveToRemoteStorage().then((response) => {
			console.log("[technique-settings.ts] saveToRemoteStorage response", response);
			this.app.alerts.dismissLoadingAlert();
			this.saveUseData("mi-assistant", "saving mi technique", { mi_technique: this.mi_technique.getSlug() });
			this.app.alerts.presentSimpleToast(this.toast_controller, this.app.language.translateString("ai_assistant_settings_page_save_success"));
		})
		.catch((error) => {
			this.app.alerts.dismissLoadingAlert();
			console.log("[technique-settings.ts] Save technique error", error);
		})
	}
}