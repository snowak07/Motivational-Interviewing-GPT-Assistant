<?php
/**
 * Hooks for the plugin
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Run updates for this plugin
 *
 * @note Keep this hook to make it easy to update your plugin
 */
(\LC\Hooks::me())->addHook($this, "app_loaded", function() {
	if ($this->isUpgradable()) {
		$this->runUpdates();
	}
});
