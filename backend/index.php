<?php
/**
 * Main running file for plugin
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Add the namespace for the plugin into the autoloader
 *
 * This is where you include the namespace from config.yml
 */
global $composer_autoloader;
$composer_autoloader->addPsr4("Plugin\\AIChat\\", __DIR__ . "/src");

// Include hooks and permissions (routes.php will be included automatically)
include __DIR__ . "/routes.php";
include __DIR__ . "/hooks.php";
include __DIR__ . "/permissions.php";
