<?php
/**
 * Test the Configuration class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * PluginCollection object to use
	 *
	 * @var \LC\PluginCollection
	 */
	protected $plugins = null;

	/**
	 * Set up for each test
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		global $app;

		$this->plugins = $app->plugins;
		\LC\Factory::unsetMe();
	}

	/**
	 * Clean up after each test
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
		global $app;

		$app->plugins = $this->plugins;
		\LC\Factory::unsetMe();
	}

	/**
	 * Test get
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Configuration::get)]
	public function testGetWithSuccess(): void
	{
		$config = new \Plugin\AIChat\Configuration();
		$config->config_statement = "foo-config_statement";

		$results = $config->get();
		$this->assertEquals($results->config_statement, "foo-config_statement");
	}

	/**
	 * Test load
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Configuration::load)]
	public function testLoadConfiguration(): void
	{
		global $app;

		$mock_plugin = $this->createMock(\LC\Plugin::class);
		$mock_plugin->method("getAllMetadataValuesAsArray")->willReturn(
			array(
				"configuration_statement" => "foo-config_statement"
			)
		);

		$mock_plugins = $this->createMock(\LC\PluginCollection::class);
		$mock_plugins->method("get")->willReturn($mock_plugin);
		$app->plugins = $mock_plugins;

		$configuration = new \Plugin\AIChat\Configuration();
		$configuration->load();
		$this->assertEquals($configuration->config_statement, "foo-config_statement");
	}

	/**
	 * Test save with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Configuration::save)]
	public function testSaveWithSuccess(): void
	{
		global $app;

		$mock_plugin = $this->createMock(\LC\Plugin::class);

		$mock_plugins = $this->createMock(\LC\PluginCollection::class);
		$mock_plugins->method("get")->willReturn($mock_plugin);
		$app->plugins = $mock_plugins;

		$configuration = new \Plugin\AIChat\Configuration();
		$configuration->config_statement = "foo-config_statement";

		$result = $configuration->save();

		$this->assertTrue($result);
	}
}