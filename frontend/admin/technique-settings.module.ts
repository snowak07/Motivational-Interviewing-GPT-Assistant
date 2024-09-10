/**
 * Module for Technique Settings component
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { IonicPageModule } from 'ionic-angular';
import { NgModule } from '@angular/core';
import { SharedModule } from '../../shared.module';
import { TechniqueSettingsPage } from './technique-settings';

@NgModule({
	declarations: [
		TechniqueSettingsPage
	],
	imports: [
		IonicPageModule.forChild(TechniqueSettingsPage),
		SharedModule,
	],
	entryComponents: [
		TechniqueSettingsPage
	],
	exports: [
		TechniqueSettingsPage
	]
})
export class TechniqueSettingsPageModule { }