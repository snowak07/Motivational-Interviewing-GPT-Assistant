/**
 * Module for Conversation component
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { AssistantConversationPage } from './conversation-page';
import { IonicPageModule } from 'ionic-angular';
import { NgModule } from '@angular/core';
import { SharedModule } from '../shared.module';

@NgModule({
	declarations: [
		AssistantConversationPage
	],
	imports: [
		IonicPageModule.forChild(AssistantConversationPage),
		SharedModule,
	],
	entryComponents: [
		AssistantConversationPage
	],
	exports: [
		AssistantConversationPage
	]
})
export class AssistantConversationPageModule { }