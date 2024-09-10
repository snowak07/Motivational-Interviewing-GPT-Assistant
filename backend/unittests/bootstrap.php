<?php
/**
 * Used in loading the necessary classes for testing the plugin
 *
 * @copyright Center for Health Enhancement Systems Studies
 */

$plugin_directory = dirname(__DIR__);
$labcoat_path = $plugin_directory;

// When the plugin is used alone (usually for unit testing)
if (file_exists($plugin_directory . "/labcoat/unittests/bootstrap.php")) {
	$labcoat_path = $plugin_directory . "/labcoat/";

// When the plugin is used in a Labcoat based website
} else if (file_exists($plugin_directory . "/../../labcoat/unittests/bootstrap.php")) {
	$labcoat_path = $plugin_directory . "/../../labcoat/";

} else {
	throw new \Exception("Labcoat installation not found.");
}

// Include the bootstrap file for Labcoat. It sets up a mock app to use.
require $labcoat_path . "unittests/bootstrap.php";

// Register classes in the autoloader defined in the bootstrap file for Labcoat
$composer_autoloader->addPsr4("Plugin\\AIChat\\", $plugin_directory . "/src");
$composer_autoloader->addPsr4("Plugin\\AIChat\\UnitTests\\", $plugin_directory . "/unittests/src");

// Initialize the plugin
$plugin = new \LC\Plugin("aichat", $plugin_directory);
$plugin->activate();
$plugin->load();

// Add the plugin to the list plugins
$app->plugins->add($plugin);

// Install the plugin and run updates on it
$plugin->runInstall();
$plugin->runUpdates();