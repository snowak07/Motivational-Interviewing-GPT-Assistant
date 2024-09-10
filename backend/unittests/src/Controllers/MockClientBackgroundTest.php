<?php
/**
 * Test the MockClientBackground class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class MockClientBackgroundTest extends \PHPUnit\Framework\TestCase
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
	 * @param string 	$test_background_guid			Mock Client Background guid to test with
	 * @param array 	$mock_backgrounds_return_data		Results when calling the MockClientBackground class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_background_guid = "", $mock_backgrounds_return_data = null): array
	{
		global $app;

		// Mock a MockClientBackground
		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("delete")
					->willReturn(true);
		$mock_background->method("load")
					->willReturn(true);
		$mock_background->method("restore")
					->willReturn(true);
		$mock_background->method("save")
					->willReturn(true);

		if ($test_background_guid) {
			$mock_background->guid = $test_background_guid;
		}

		// Mock the backgrounds and configure it to return fake data
		if (!isset($mock_backgrounds_return_data)) {
			$mock_backgrounds_return_data = new \StdClass;
			$mock_backgrounds_return_data->backgrounds = array();
			$mock_backgrounds_return_data->order = array();
		}

		// Mock a factory and configure it to build our background mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MockClientBackground', $mock_background)
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_background" => $mock_background, "mock_backgrounds_return_data" => $mock_backgrounds_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(true);
		$mock_background->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'delete'),
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
	 * Test delete with invalid MockClientBackground
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::delete)]
	public function testDeleteWithInvalidMockClientBackground(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_background");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'get'),
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
	 * Test get with invalid MockClientBackground
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::get)]
	public function testGetWithInvalidMockClientBackground(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'get'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_background");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'get'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'restore'),
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
	 * Test restore with invalid MockClientBackground
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::restore)]
	public function testRestoreWithInvalidMockClientBackground(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mock_client_background");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'restore'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(true);
		$mock_background->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'restore'),
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
	 * Test save with new MockClientBackground success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveNewMockClientBackgroundSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with update MockClientBackground success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveUpdateMockClientBackgroundSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with invalid background_info
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithInvalidBackgroundInfo(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid background_info");
	}

	/**
	 * Test save with invalid create_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
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
	 * Test save with Invalid user_guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithInvalidClientName(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid client_name");
	}

	/**
	 * Test save with invalid client_prompt_guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithInvalidProfilePicture(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "",
				"background_info" => "foo-background_info",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid profile_picture");
	}

	/**
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MockClientBackground::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_background = $this->createMock(\Plugin\AIChat\MockClientBackground::class);
		$mock_background->method("load")
					->willReturn(true);
		$mock_background->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_background);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MockClientBackground(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"client_name" => "foo-client_name",
				"profile_picture" => "foo-profile_picture",
				"background_info" => "foo-background_info",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}