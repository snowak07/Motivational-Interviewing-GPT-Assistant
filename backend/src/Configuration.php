<?php
/**
 * Class for handling API functions for working with the AIChat Configuration
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class Configuration
{
	/**
	 * Configuration statement used to generate AI Assistant responses
	 *
	 * @var string
	 */
	public string $config_statement = "";

	/**
	 * Retrieve configuration information for the site
	 *
	 * @return \StdClass
	 */
	public function get(): \StdClass
	{
		return (object)array(
			"config_statement" => $this->config_statement
		);
	}

	/**
	 * Load the configuration
	 *
	 * @return void
	 */
	public function load(): void
	{
		global $app;

		$app->plugins->get("aichat")->loadMetadata();
		$metadata = $app->plugins->get("aichat")->getAllMetadataValuesAsArray();
		$this->config_statement = (isset($metadata["configuration_statement"])) ? $metadata["configuration_statement"] : "";
	}

	/**
	 * Save configuration information for AIChat
	 *
	 * @return bool
	 */
	public function save(): bool
	{
		global $app;

		$app->plugins->get("aichat")->setMetadata("configuration_statement", $this->config_statement);
		$app->plugins->get("aichat")->saveMetadata();

		return true;
	}
}