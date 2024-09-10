<?php
/**
 * Uninstall Script
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This file will uninstall the plugin
 *
 * NOTE:
 * 	This will only run if you completely uninstall the plugin using the admin panel
 */

$app->db->query("
	DROP TABLE IF EXISTS `{aichat_interactions}`;
	DROP TABLE IF EXISTS `{aichat_search_document_elements}`;
	DROP TABLE IF EXISTS `{aichat_search_documents}`;
	DROP TABLE IF EXISTS `{aichat_mock_client_backgrounds}`;
	DROP TABLE IF EXISTS `{aichat_mock_client_prompts}`;
	DROP TABLE IF EXISTS `{aichat_mi_techniques}`;
");