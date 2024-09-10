/**
 * Component and page for handling the Techniques List page
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AlertController, IonicPage, LoadingController, NavController, NavParams } from 'ionic-angular';
import { App } from '../../providers/app';
import { AppController } from '../../providers/core/app-controller';
import { Component } from '@angular/core';
import { MITechnique } from './providers/mi-technique';

@IonicPage({
	name: "assistant/technique-list",
	segment: "assistant/technique-list/",
	defaultHistory: ["home"]
})

@Component({
	selector: 'page-technique-list',
	templateUrl: 'technique-list.html'
})

export class TechniqueListPage extends AppController {
	/**
	 * Route for retrieving all mi techniques
	 *
	 * @var string
	 */
	protected GET_TECHNIQUES_ROUTE: string = "/aichat/techniques/get";

	/**
	 * Map of techniques and whether their card elements are expanded or not
	 *
	 * @var object
	 */
	protected is_technique_expanded_map: object = {};

	/**
	 * Ordered list of mi techniques
	 *
	 * @var MITechnique[]
	 */
	protected ordered_techniques: MITechnique[] = [ ];

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
			"viewing mi technique list",
			{}
		);
	}

	/**
	 * Handle expanding or de-expanding a technique card
	 *
	 * @param technique_slug		slug of the technique to expand the button of
	 *
	 * @return void
	 */
	expandMenu(technique_slug: string): void {
		this.is_technique_expanded_map[technique_slug] = !this.is_technique_expanded_map[technique_slug];
	}

	/**
	 * Handle prompt click and redirect to the conversation page or to the client bio popup.
	 *
	 * @param event 		Event containing html element data
	 * @param technique		Selected technique
	 *
	 * @return void
	 */
	handleTechniqueCardClick(event: any, technique: MITechnique): void {
		if (event.target.className.includes("expanded-menu") || event.target.className.includes("technique-settings-button-icon")) {
			// Do nothing
		} else {
			this.app.helpers.handleNavigation(this.nav_controller, 'assistant/prompt-select', {'mi_technique': technique.getSlug()})
		}
	}

	/**
	 * Handle when the view appears
	 *
	 * @return Promise<void>
	 */
	async ionViewWillEnter(): Promise<void> {
		super.ionViewWillEnter();

		await this.loadTechniques();

		// Instantiate a map that indicates which prompts have an expanded card or not
		this.is_technique_expanded_map = {};
		this.ordered_techniques.forEach((technique: MITechnique) => {
			this.is_technique_expanded_map[technique.getSlug()] = false;
		});
	}

	/**
	 * Load techniques
	 *
	 * @return Promise<void>
	 */
	async loadTechniques(): Promise<void> {
		this.ordered_techniques = [];

		try {
			var response = await this.app.requests.createGetApiRequest(this.GET_TECHNIQUES_ROUTE);
			var response_data = JSON.parse(response.data);

			for (var index in response_data.order) {
				var slug = response_data.order[index];
				var mi_technique = new MITechnique(this.app);
				var technique_object = response_data.techniques[slug];
				mi_technique.loadFromObject(technique_object);

				this.ordered_techniques.push(mi_technique);
			}

		} catch (error) {
			console.log("[technique-list.ts] Error loading techniques", error);
		}
	}
}