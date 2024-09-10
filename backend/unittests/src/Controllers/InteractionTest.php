<?php
/**
 * Test the Interaction class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class InteractionTest extends \PHPUnit\Framework\TestCase
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
	 * @param string 	$test_interaction_guid			Interaction guid to test with
	 * @param array 	$mock_interactions_return_data		Results when calling the Interaction class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_interaction_guid = "", $mock_interactions_return_data = null): array
	{
		global $app;

		// Mock a Interaction
		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("delete")
					->willReturn(true);
		$mock_interaction->method("load")
					->willReturn(true);
		$mock_interaction->method("restore")
					->willReturn(true);
		$mock_interaction->method("save")
					->willReturn(true);

		if ($test_interaction_guid) {
			$mock_interaction->guid = $test_interaction_guid;
		}

		$mock_user = $this->createMock(\LC\User::class);
		$mock_user->method("load")
					->willReturn(true);

		// Mock the interactions and configure it to return fake data
		if (!isset($mock_interactions_return_data)) {
			$mock_interactions_return_data = new \StdClass;
			$mock_interactions_return_data->interactions = array();
			$mock_interactions_return_data->order = array();
		}

		// Mock a factory and configure it to build our interaction mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\Interaction', $mock_interaction),
									array('\LC\User', $mock_user)
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_interaction" => $mock_interaction, "mock_interactions_return_data" => $mock_interactions_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(true);
		$mock_interaction->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'delete'),
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
	 * Test delete with invalid Interaction
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::delete)]
	public function testDeleteWithInvalidInteraction(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid interaction");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'get'),
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
	 * Test get with invalid Interaction
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::get)]
	public function testGetWithInvalidInteraction(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'get'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid interaction");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'get'),
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
	 * Test restore with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'restore'),
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
	 * Test restore with invalid Interaction
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::restore)]
	public function testRestoreWithInvalidInteraction(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid interaction");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'restore'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(true);
		$mock_interaction->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'restore'),
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
	 * Test save with new Interaction success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveNewInteractionSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with update Interaction success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveUpdateInteractionSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with blank user_guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithBlankUserGuid(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "",
				"user_message" => "",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid user_guid");
	}

	/**
	 * Test save with invalid client_prompt_guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidChatSessionGuid(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid client_prompt_guid");
	}

	/**
	 * Test save with invalid create_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
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
	 * Test save with invalid system_message
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidSystemChatResponse(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid system_message");
	}

	/**
	 * Test save with invalid system_information
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidSystemInformation(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array(),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid system_information");
	}

	/**
	 * Test save with invalid system_response
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidSystemResponse(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array(),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid system_response");
	}

	/**
	 * Test save with invalid user
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidUser(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(true);

		$mock_user = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_user->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\Interaction', $mock_interaction),
									array('\LC\User', $mock_user)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid user");
	}

	/**
	 * Test save with invalid user_message
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithInvalidUserPrompt(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid user_message");
	}

	/**
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(true);
		$mock_interaction->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_interaction);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with user load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\Interaction::save)]
	public function testSaveWithUserLoadError(): void {
		global $app;

		$mock_interaction = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_interaction->method("load")
					->willReturn(true);

		$mock_user = $this->createMock(\Plugin\AIChat\Interaction::class);
		$mock_user->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\Interaction', $mock_interaction),
									array('\LC\User', $mock_user)
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\Interaction(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_prompt_guid" => "foo-client_prompt_guid",
				"user_guid" => "foo-user_guid",
				"user_message" => "foo-user_message",
				"system_message" => "foo-system_message",
				"system_response" => array("foo" => "bar", "baz" => "buz"),
				"system_information" => array("foo" => "bar", "baz" => "buz"),
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}