<?php
/**
 * Test the MockClientPrompt class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class MockClientPromptTest extends \PHPUnit\Framework\TestCase
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
	 * @param string 	$test_prompt_guid			Mock Client Prompt guid to test with
	 * @param array 	$mock_prompts_return_data		Results when calling the MockClientPrompt class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_prompt_guid = "", $mock_prompts_return_data = null): array
	{
		global $app;

		// Mock the prompt and configure it to return fake data
		if (!isset($mock_prompts_return_data)) {
			$mock_prompts_return_data = new \StdClass;
			$mock_prompts_return_data->prompts = array();
			$mock_prompts_return_data->order = array();
		}

		// Mock a MockClientPrompt
		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("delete")
					->willReturn(true);
		$mock_prompt->method("load")
					->willReturn(true);
		$mock_prompt->method("restore")
					->willReturn(true);
		$mock_prompt->method("save")
					->willReturn(true);
		$mock_prompt->method("getByBackgroundGuid")
					->willReturn($mock_prompts_return_data);
		$mock_prompt->method("getByMITechnique")
					->willReturn($mock_prompts_return_data);

		if ($test_prompt_guid) {
			$mock_prompt->guid = $test_prompt_guid;
		}

		$mock_background = $this->createMock(\LC\User::class);
		$mock_background->method("load")
					->willReturn(true);

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);

		// Mock a factory and configure it to build our prompt mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background),
									array('\Plugin\AIChat\MITechnique', $mock_technique)
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_prompt" => $mock_prompt, "mock_prompts_return_data" => $mock_prompts_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test delete with delete error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);
		$mock_prompt->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test delete with invalid MockClientPrompt
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::delete)]
	public function testDeleteWithInvalidMockClientPrompt(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_prompt");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test get with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'get'),
			"GET",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test get with invalid MockClientPrompt
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::get)]
	public function testGetWithInvalidMockClientPrompt(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'get'),
			"GET",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_prompt");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'get'),
			"GET",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getByBackgroundGuid with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidSuccess(): void {
		global $app;

		$mock_prompts_return_data = new \StdClass;
		$mock_prompts_return_data->prompts = array(array(
			"guid" => "foo-guid",
			"background_guid" => "foo-background_guid",
			"content" => "foo-content",
			"mi_technique_slug" => "foo-mi_technique_slug",
			"other_data" => array("foo" => "bar"),
			"create_date" => 123,
			"delete_date" => -1
		));
		$mock_prompts_return_data->order = array(
			"foo-guid"
		);

		$this->setUpDefaultMocks("", $mock_prompts_return_data);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByBackgroundGuid'),
			"GET",
			array(),
			array(
				"guid" => "foo-background_guid"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "200");
		$this->assertTrue(isset($response_body->prompts));
		$this->assertTrue(isset($response_body->order));
	}

	/**
	 * Test getByBackgroundGuid with a background that does not exist
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithBackgroundDoesNotExist(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByBackgroundGuid'),
			"GET",
			array(),
			array(
				"guid" => "foo-background_guid"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_background");
	}

	/**
	 * Test getByBackgroundGuid with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithBackgroundLoadError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByBackgroundGuid'),
			"GET",
			array(),
			array(
				"guid" => "foo-background_guid"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getByBackgroundGuid with error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(true);

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("getByBackgroundGuid")
					->will($this->throwException(new \LC\CustomException("")));

		// Mock a factory and configure it to build our prompt mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByBackgroundGuid'),
			"GET",
			array(),
			array(
				"guid" => "foo-background_guid"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getByMITechnique with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueSuccess(): void {
		global $app;

		$mock_prompts_return_data = new \StdClass;
		$mock_prompts_return_data->prompts = array(array(
			"guid" => "foo-guid",
			"background_guid" => "foo-background_guid",
			"content" => "foo-content",
			"mi_technique_slug" => "foo-mi_technique_slug",
			"other_data" => array("foo" => "bar"),
			"create_date" => 123,
			"delete_date" => -1
		));
		$mock_prompts_return_data->order = array(
			"foo-guid"
		);

		$this->setUpDefaultMocks("", $mock_prompts_return_data);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByMITechnique'),
			"GET",
			array(
				"mi_technique_slug" => "foo-mi_technique_slug"
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "200");
		$this->assertTrue(isset($response_body->prompts));
		$this->assertTrue(isset($response_body->order));
	}

	/**
	 * Test getByMITechnique with error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("getByMITechnique")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);

		// Mock a factory and configure it to build our prompt mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MITechnique', $mock_technique)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByMITechnique'),
			"GET",
			array(
				"mi_technique_slug" => "foo-mi_technique_slug"
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getByMITechnique with invalid technique
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithInvalidTechnique(): void {
		global $app;

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByMITechnique'),
			"GET",
			array(
				"mi_technique_slug" => "",
				"sort_by" => "create_date desc",
				"show_deleted" => "true"
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid mi_technique_slug");
	}

	/**
	 * Test getByMITechnique with technique that does not exist
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithTechniqueDoesNotExist(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		// Mock a factory and configure it to build our prompt mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByMITechnique'),
			"GET",
			array(
				"mi_technique_slug" => "foo-mi_technique_slug"
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique_slug");
	}

	/**
	 * Test getByMITechnique with technique load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithTechniqueLoadError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		// Mock a factory and configure it to build our prompt mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'getByMITechnique'),
			"GET",
			array(
				"mi_technique_slug" => "foo-mi_technique_slug"
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test restore with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test restore with invalid MockClientPrompt
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::restore)]
	public function testRestoreWithInvalidMockClientPrompt(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_prompt");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test restore with restore error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);
		$mock_prompt->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with new MockClientPrompt success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveNewMockClientPromptSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with update MockClientPrompt success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveUpdateMockClientPromptSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with mock client background load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithClientBackgroundLoadError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_background->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with an invalid mock client background
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidClientBackground(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_background->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid background");
	}

	/**
	 * Test save with an invalid mock client background guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidClientBackgroundGuid(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid background_guid");
	}

	/**
	 * Test save with invalid content
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidContent(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid content");
	}

	/**
	 * Test save with invalid create_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array(),
				"create_date" => "foobar"
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid create_date, must be a float");
	}

	/**
	 * Test save with invalid delete_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array(),
				"delete_date" => "foobar"
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid delete_date, must be a float");
	}

	/**
	 * Test save with an invalid mi technique
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidMITechnique(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_background->method("load")
					->willReturn(true);

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background),
									array('\Plugin\AIChat\MITechnique', $mock_technique)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique");
	}

	/**
	 * Test save with Invalid mi_technique_slug
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithInvalidMITechniqueSlug(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"mi_technique_slug" => "",
				"content" => "foo-content",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid mi_technique_slug");
	}

	/**
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_prompt);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"content" => "foo-content",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with a mi technique load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithMITechniqueLoadError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_background->method("load")
					->willReturn(true);

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background),
									array('\Plugin\AIChat\MITechnique', $mock_technique)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with save error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientPrompt::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_prompt = $this->createMock(\Plugin\AIChat\MockClientPrompt::class);
		$mock_prompt->method("load")
					->willReturn(true);
		$mock_prompt->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(true);

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientPrompt', $mock_prompt),
									array('\Plugin\AIChat\MockClientBackground', $mock_background),
									array('\Plugin\AIChat\MITechnique', $mock_technique)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientPrompt(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"background_guid" => "foo-background_guid",
				"content" => "foo-content",
				"mi_technique_slug" => "foo-mi_technique_slug",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}