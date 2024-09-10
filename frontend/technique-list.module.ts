/**
 * Module for Technique List component
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { IonicPageModule } from 'ionic-angular';
import { NgModule } from '@angular/core';
import { SharedModule } from '../shared.module';
import { TechniqueListPage } from './technique-list';

@NgModule({
	declarations: [
		TechniqueListPage
	],
	imports: [
		IonicPageModule.forChild(TechniqueListPage),
		SharedModule,
	],
	entryComponents: [
		TechniqueListPage
	],
	exports: [
		TechniqueListPage
	]
})
export class TechniqueListPageModule { }