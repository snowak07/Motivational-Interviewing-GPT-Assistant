<?php
/**
 * Script for updating the plugin
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

global $app;
$plugin = $app->plugins->get($this->slug);

/**
 * Upgrades the database to version 1.0.5
 */
if (version_compare($plugin->getAppVersion(), "1.0.5", "<")) {
	try { $app->db->query("ALTER TABLE `{aichat_interactions}` ADD COLUMN `user_guid` varchar(200) NOT NULL AFTER `chat_session_guid`"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_interactions}` ADD INDEX `user_guid` (`user_guid`);"); } catch (\Exception $error) { };

	$plugin->setAppVersion("1.0.5");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

/**
 * Upgrades the database to version 1.1.0. Adds new database tables for storing search documents.
 */
if (version_compare($plugin->getAppVersion(), "1.1.0", "<")) {
	try {
		$app->db->query("
		CREATE TABLE IF NOT EXISTS `{aichat_search_document_elements}` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
			`guid` varchar(200) NOT NULL COMMENT 'Identifier for document element',
			`document_guid` varchar(200) NOT NULL COMMENT 'Identifier for document the element is a part of',
			`text` MEDIUMTEXT NOT NULL COMMENT 'Text snippet of the document',
			`embedding` MEDIUMTEXT NOT NULL COMMENT 'Response data about the item',
			`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
			`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
			`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1;
		");
	} catch (\Exception $error) { };

	try {
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
	} catch (\Exception $error) { };

	$plugin->setAppVersion("1.1.0");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

/**
 * Upgrades the database to version 1.1.4
 */
if (version_compare($plugin->getAppVersion(), "1.1.4", "<")) {
	$plugin->setAppVersion("1.1.4");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

/**
 * Upgrades the database to version 1.2.0. Adds new database tables for storing mock patient prompts and backgrounds
 */
if (version_compare($plugin->getAppVersion(), "1.2.0", "<")) {
	try {
		$app->db->query("
		CREATE TABLE IF NOT EXISTS `{aichat_mock_patient_backgrounds}` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
			`guid` varchar(200) NOT NULL COMMENT 'Identifier for patient background',
			`patient_name` varchar(100) NOT NULL COMMENT 'Name of mock patient',
			`profile_picture` varchar(200) NULL COMMENT 'Link to profile picture image',
			`background_info` MEDIUMTEXT NOT NULL COMMENT 'Background info about the patient',
			`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
			`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
			`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1;
		");
	} catch (\Exception $error) { };

	try {
		$app->db->query("
		CREATE TABLE IF NOT EXISTS `{aichat_mock_patient_prompts}` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
			`guid` varchar(200) NOT NULL COMMENT 'Identifier for patient prompt',
			`background_guid` varchar(200) NOT NULL COMMENT 'Identifier for patient background',
			`content` MEDIUMTEXT NOT NULL COMMENT 'Content of the prompt',
			`mi_technique` varchar(50) NULL COMMENT 'Link to profile picture image',
			`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
			`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
			`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1;
		");
	} catch (\Exception $error) { };

	try { $app->db->query("ALTER TABLE `{aichat_interactions}` RENAME COLUMN `chat_session_guid` to `patient_prompt_guid`"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_interactions}` RENAME COLUMN `user_prompt` to `user_message`"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_interactions}` RENAME COLUMN `system_chat_response` to `system_message`"); } catch (\Exception $error) { };

	$plugin->setAppVersion("1.2.0");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

/**
 * Upgrades the database to version 1.2.2
 */
if (version_compare($plugin->getAppVersion(), "1.2.2", "<")) {
	// Alter aichat_mock_patient_backgrounds comments
	try { $app->db->query("ALTER TABLE `{aichat_mock_patient_backgrounds}` MODIFY COLUMN `guid` varchar(200) NOT NULL COMMENT 'Identifier for client background'"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_mock_patient_backgrounds}` MODIFY COLUMN `patient_name` varchar(100) NOT NULL COMMENT 'Name of mock client'"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_mock_patient_backgrounds}` MODIFY COLUMN `background_info` MEDIUMTEXT NOT NULL COMMENT 'Background info about the client'"); } catch (\Exception $error) { };

	// Alter aichat_mock_patient_prompts comments
	try { $app->db->query("ALTER TABLE `{aichat_mock_patient_prompts}` MODIFY COLUMN `guid` varchar(200) NOT NULL COMMENT 'Identifier for client prompt'"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_mock_patient_prompts}` MODIFY COLUMN `background_guid` varchar(200) NOT NULL COMMENT 'Identifier for client background'"); } catch (\Exception $error) { };

	// Rename columns, changing 'patient' to 'client'
	try { $app->db->query("ALTER TABLE `{aichat_interactions}` RENAME COLUMN `patient_prompt_guid` to `client_prompt_guid`"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_mock_patient_backgrounds}` RENAME COLUMN `patient_name` to `client_name`"); } catch (\Exception $error) { };

	// Alter table names, changing 'patient' to 'client'
	try { $app->db->query("RENAME TABLE `{aichat_mock_patient_backgrounds}` to `{aichat_mock_client_backgrounds}`"); } catch (\Exception $error) { };
	try { $app->db->query("RENAME TABLE `{aichat_mock_patient_prompts}` to `{aichat_mock_client_prompts}`"); } catch (\Exception $error) { };

	$plugin->setAppVersion("1.2.2");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

/**
 * Upgrades the database to version 1.3.0
 */
if (version_compare($plugin->getAppVersion(), "1.3.0", "<")) {
	try {
		$app->db->query("
		CREATE TABLE IF NOT EXISTS `{aichat_mi_techniques}` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Id for row, unused',
			`slug` varchar(200) NOT NULL COMMENT 'Identifier for mi technique',
			`name` varchar(300) NOT NULL COMMENT 'Name of the mi technique',
			`definition` TEXT NOT NULL COMMENT 'Definition of the mi technique',
			`user_instruction` TEXT NOT NULL COMMENT 'Instructions for the user in how to use the mi technique',
			`ai_instruction` TEXT NOT NULL COMMENT 'Instructions for the ai on how to form its response for this mi technique',
			`other_data` MEDIUMTEXT NULL COMMENT 'Other data about the item',
			`create_date` double NOT NULL COMMENT 'Date and time item was created. In seconds, based on Unix Time',
			`delete_date` double NOT NULL DEFAULT -1 COMMENT 'Date and time item was deleted. In seconds, based on Unix Time',
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1;
		");
	} catch (\Exception $error) { };

	// Alter mi_technique field to fit with new mi_technique table
	try { $app->db->query("ALTER TABLE `{aichat_mock_client_prompts}` MODIFY COLUMN `mi_technique` varchar(200) NOT NULL COMMENT 'Identifier for mi technique' AFTER background_guid"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_mock_client_prompts}` RENAME COLUMN `mi_technique` to `mi_technique_slug`"); } catch (\Exception $error) { };

	// Fix comment
	try { $app->db->query("ALTER TABLE `{aichat_search_document_elements}` MODIFY COLUMN `embedding` MEDIUMTEXT NOT NULL COMMENT 'Vector representation of the text'"); } catch (\Exception $error) { };

	$plugin->setAppVersion("1.3.0");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

if (version_compare($plugin->getAppVersion(), "2.1.0", "<")) {
	try {
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
	} catch (\Exception $error) { };

	$plugin->setAppVersion("2.1.0");
	$app->plugins->add($plugin);
	$app->plugins->save();
}

if (version_compare($plugin->getAppVersion(), "2.1.1", "<")) {
	try { $app->db->query("ALTER TABLE `{aichat_mi_techniques}` ADD COLUMN `version_slug` varchar(200) NOT NULL AFTER `ai_instruction`"); } catch (\Exception $error) { };
	try { $app->db->query("ALTER TABLE `{aichat_mi_techniques}` ADD COLUMN `version` varchar(200) NOT NULL AFTER `ai_instruction`"); } catch (\Exception $error) { };

	$plugin->setAppVersion("2.1.1");
	$app->plugins->add($plugin);
	$app->plugins->save();
}