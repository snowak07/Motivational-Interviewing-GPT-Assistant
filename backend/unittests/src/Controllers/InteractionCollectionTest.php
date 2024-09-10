<?php
/**
 * Test the Interaction Collection class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class InteractionCollectionTest extends \PHPUnit\Framework\TestCase
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
	 * @param array 	$mock_interactions_return_data		Results when calling the Interaction class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks(
		array $mock_interactions_return_data = null
	): array {
		global $app;

		// Mock the interactions and configure it to return fake data
		if (!isset($mock_interactions_return_data)) {
			$mock_interactions_return_data = new \StdClass;
			$mock_interactions_return_data->interactions = array();
			$mock_interactions_return_data->order = array();
		}

		$mock_interaction_collection = $this->createMock(\Plugin\AIChat\InteractionCollection::class);
		$mock_interaction_collection->method("getByUserGuid")
					->willReturn($mock_interactions_return_data);

		$mock_user = $this->createMock(\LC\User::class);
		$mock_user->method("load")
					->willReturn(true);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\InteractionCollection', $mock_interaction_collection),
									array('\LC\User', $mock_user),
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_interaction" => $mock_interaction_collection, "mock_interactions_return_data" => $mock_interactions_return_data);
	}

	/**
	 * Test getByUserGuid with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\InteractionCollection(), 'getByUserGuid'),
			"GET",
			array(
				"client_prompt_guid" => "foo-prompt_guid",
				"sort_by" => "create_date asc",
				"show_deleted" => "false",
				"page_num" => 1,
				"num_per_page" => 25,
			),
			array(
				"user_guid" => "foo-user_guid"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test getByUserGuid with invalid page_number
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithInvalidPageNumber(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\InteractionCollection(), 'getByUserGuid'),
			"GET",
			array(
				"sort_by" => "create_date asc",
				"show_deleted" => "false",
				"page_num" => "foo",
				"num_per_page" => 25,
			),
			array(
				"user_guid" => "foo-user_guid"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid page");
	}

	/**
	 * Test getByUserGuid with invalid num_per_page
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithInvalidNumberPerPage(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\InteractionCollection(), 'getByUserGuid'),
			"GET",
			array(
				"sort_by" => "create_date asc",
				"show_deleted" => "false",
				"page_num" => 1,
				"num_per_page" => "foo",
			),
			array(
				"user_guid" => "foo-user_guid"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid num_per_page");
	}

	/**
	 * Test getByUserGuid with user load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithUserLoadError(): void {
		global $app;

		$mock_user = $this->createMock(\LC\User::class);
		$mock_user->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_user);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\InteractionCollection(), 'getByUserGuid'),
			"GET",
			array(
				"sort_by" => "create_date asc",
				"show_deleted" => "false",
				"page_num" => 1,
				"num_per_page" => 25,
			),
			array(
				"user_guid" => "foo-user_guid"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getByUserGuid with a user that does not exist
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithUserDoesNotExist(): void {
		global $app;

		$mock_user = $this->createMock(\LC\User::class);
		$mock_user->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_user);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\InteractionCollection(), 'getByUserGuid'),
			"GET",
			array(
				"sort_by" => "create_date asc",
				"show_deleted" => "false",
				"page_num" => 1,
				"num_per_page" => 25,
			),
			array(
				"user_guid" => "foo-user_guid"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid user");
	}

	/**
	 * Test getByUserGuid with getByUserGuid error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithGetByUserGuidError(): void {
		global $app;

		$mock_user = $this->createMock(\LC\User::class);
		$mock_user->method("load")
					->willReturn(true);

		$mock_interaction_collection = $this->createMock(\Plugin\AIChat\InteractionCollection::class);
		$mock_interaction_collection->method("getByUserGuid")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\InteractionCollection', $mock_interaction_collection),
									array('\LC\User', $mock_user),
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\InteractionCollection(), 'getByUserGuid'),
			"GET",
			array(
				"sort_by" => "create_date asc",
				"show_deleted" => "false",
				"page_num" => 1,
				"num_per_page" => 25,
			),
			array(
				"user_guid" => "foo-user_guid"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}