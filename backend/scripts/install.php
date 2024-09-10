<?php
/**
 * Install Script
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This file is called when the plugin is first installed
 *
 * NOTE:
 * 	It will not run again if you disable then enable the plugin.
 * 	This file will only run if you first completely remove the plugin
 */

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_interactions}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`guid` varchar(200) NOT NULL COMMENT 'Identifier for interaction',
	`client_prompt_guid` varchar(200) NOT NULL COMMENT 'Identifier of the prompt that started the conversation',
	`user_guid` varchar(200) NOT NULL COMMENT 'Identifier for the user',
	`user_message` text NOT NULL COMMENT 'Content of the users message',
	`system_message` text NOT NULL COMMENT 'Content of the systems message',
	`system_response` MEDIUMTEXT NOT NULL COMMENT 'Response data about the item',
	`system_information` MEDIUMTEXT NOT NULL COMMENT 'Parameters that were used when generating the response',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_search_document_elements}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`guid` varchar(200) NOT NULL COMMENT 'Identifier for document element',
	`document_guid` varchar(200) NOT NULL COMMENT 'Identifier for document the element is a part of',
	`text` MEDIUMTEXT NOT NULL COMMENT 'Text snippet of the document',
	`embedding` MEDIUMTEXT NOT NULL COMMENT 'Vector representation of the text',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_search_documents}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`guid` varchar(200) NOT NULL COMMENT 'Identifier for document element',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_mock_client_backgrounds}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`guid` varchar(200) NOT NULL COMMENT 'Identifier for client background',
	`client_name` varchar(100) NOT NULL COMMENT 'Name of mock client',
	`profile_picture` varchar(200) NULL COMMENT 'Link to profile picture image',
	`background_info` MEDIUMTEXT NOT NULL COMMENT 'Background info about the client',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_mock_client_prompts}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`guid` varchar(200) NOT NULL COMMENT 'Identifier for client prompt',
	`background_guid` varchar(200) NOT NULL COMMENT 'Identifier for client background',
	`mi_technique_slug` varchar(200) NOT NULL COMMENT 'Identifier for mi technique',
	`content` MEDIUMTEXT NOT NULL COMMENT 'Content of the prompt',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_mi_techniques}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`slug` varchar(200) NOT NULL COMMENT 'Identifier for mi technique',
	`name` varchar(300) NOT NULL COMMENT 'Name of the mi technique',
	`definition` TEXT NOT NULL COMMENT 'Definition of the mi technique',
	`user_instruction` TEXT NOT NULL COMMENT 'Instructions for the user in how to use the mi technique',
	`ai_instruction` TEXT NOT NULL COMMENT 'Instructions for the ai on how to form its response for this mi technique',
	`version` varchar(200) NOT NULL COMMENT 'Inputed version of the mi technique',
	`version_slug` varchar(200) NOT NULL COMMENT 'Identifier for mi technique version',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");

$app->db->query("
CREATE TABLE IF NOT EXISTS `{aichat_mi_technique_versions}` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
	`slug` varchar(200) NOT NULL COMMENT 'Identifier for mi technique version',
	`technique_slug` varchar(200) NOT NULL COMMENT 'Identifier of the associated mi technique',
	`name` varchar(300) NOT NULL COMMENT 'Name of the mi technique',
	`definition` TEXT NOT NULL COMMENT 'Definition of the mi technique',
	`user_instruction` TEXT NOT NULL COMMENT 'Instructions for the user in how to use the mi technique',
	`ai_instruction` TEXT NOT NULL COMMENT 'Instructions for the ai on how to form its response for this mi technique',
	`version` varchar(200) NOT NULL COMMENT 'Inputed version of the mi technique',
	`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
	`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
	`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;
");