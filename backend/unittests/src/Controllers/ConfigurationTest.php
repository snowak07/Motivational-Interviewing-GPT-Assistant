<?php
/**
 * Test the Configuration class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Set up for each test
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		global $app;

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

		\LC\Factory::unsetMe();
	}

	/**
	 * Set up default mocks for our test
	 *
	 * @param \StdClass		$mock_config_return_data		Results when calling the Configuration class
	 *
	 * @return void
	 */
	public function setUpDefaultMocks(
		\StdClass $mock_config_return_data = null
	): void {
		global $app;

		if (!isset($mock_config_return_data)) {
			$mock_config_return_data = new \StdClass;
			$mock_config_return_data->config_statement = "";
		}

		// Mock a Configuration
		$mock_configuration = $this->createMock(\Plugin\AIChat\Configuration::class);
		$mock_configuration->method("get")
					->willReturn($mock_config_return_data);
		$mock_configuration->method("save")
					->willReturn(true);

		// Mock a factory and configure it to build our interaction mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\Configuration', $mock_configuration)
								)
							);

		\LC\Factory::setMe($mock_factory);

		return;
	}

	/**
	 * Test get with config get error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Configuration::get)]
	public function testGetWithConfigGetError(): void {
		global $app;

		// Mock a Configuration
		$mock_configuration = $this->createMock(\Plugin\AIChat\Configuration::class);
		$mock_configuration->method("get")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_configuration);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Configuration(), 'get'),
			"GET",
			array(),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test get with config load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Configuration::get)]
	public function testGetWithConfigLoadError(): void {
		global $app;

		// Mock a Configuration
		$mock_configuration = $this->createMock(\Plugin\AIChat\Configuration::class);
		$mock_configuration->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_configuration);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Configuration(), 'get'),
			"GET",
			array(),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test get with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Configuration::get)]
	public function testGetWithSuccess(): void {
		global $app;

		$mock_configuration_data = new \StdClass;
		$mock_configuration_data->config_statement = "bar-config_statement";

		$this->setUpDefaultMocks($mock_configuration_data);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Configuration(), 'get'),
			"GET",
			array(),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "200");
		$this->assertEquals($response_body->configuration->config_statement, "bar-config_statement");
	}

	/**
	 * Test save with invalid config_statement
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Configuration::save)]
	public function testSaveWithInvalidConfigStatement(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Configuration(), 'save'),
			"POST",
			array(
				"config_statement" => ""
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid config_statement");
	}

	/**
	 * Test save with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Configuration::save)]
	public function testSaveWithSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Configuration(), 'save'),
			"POST",
			array(
				"config_statement" => "foo-config_statement"
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with save error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Configuration::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_configuration = $this->createMock(\Plugin\AIChat\Configuration::class);
		$mock_configuration->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_configuration);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Configuration(), 'save'),
			"POST",
			array(
				"config_statement" => "foo-config_statement"
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}