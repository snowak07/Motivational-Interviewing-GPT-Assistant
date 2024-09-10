<?php
/**
 * Permissions for the plugin
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

$app->setPermission("administrates-ai-assistant", "Administrates AI Assistant Settings", "Users can edit configuration settings of the ai assistant for all users");
$app->setPermission("has-ai-assistant-access", "Has Access to the AI Assistant", "Users can use the ai assistant features");