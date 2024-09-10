/**
 * Module for Client List component
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { IonicPageModule } from 'ionic-angular';
import { NgModule } from '@angular/core';
import { PromptSelectPage } from './prompt-select';
import { SharedModule } from '../shared.module';

@NgModule({
	declarations: [
		PromptSelectPage
	],
	imports: [
		IonicPageModule.forChild(PromptSelectPage),
		SharedModule,
	],
	entryComponents: [
		PromptSelectPage
	],
	exports: [
		PromptSelectPage
	]
})
export class PromptSelectPageModule { }