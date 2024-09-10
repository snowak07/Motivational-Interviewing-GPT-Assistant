<?php
/**
 * Test the SearchDocument class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class SearchDocumentTest extends \PHPUnit\Framework\TestCase
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
	 * @param string 	$test_search_document_guid			SearchDocument guid to test with
	 * @param array 	$mock_search_documents_return_data		Results when calling the SearchDocument class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_search_document_guid = "", $mock_search_documents_return_data = null): array
	{
		global $app;

		// Mock the search documents and configure it to return fake data
		if (!isset($mock_search_document_return_data)) {
			$mock_search_document_return_data = new \StdClass;
			$mock_search_document_return_data->elements = array();
			$mock_search_document_return_data->order = array();
		}

		// Mock a SearchDocument
		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("delete")
					->willReturn(true);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("restore")
					->willReturn(true);
		$mock_search_document->method("save")
					->willReturn(true);
		$mock_search_document->method("getElements")
					->willReturn($mock_search_document_return_data);
		$mock_search_document->method("returnFieldValues")
					->willReturn(array(
						"guid" => "1234",
						"other_data" => array(),
						"create_date" => 123,
						"delete_date" => -1
					));

		if ($test_search_document_guid) {
			$mock_search_document->guid = $test_search_document_guid;
		}

		// Mock a factory and configure it to build our search document mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\SearchDocument', $mock_search_document),
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_search_document" => $mock_search_document, "mock_search_document_return_data" => $mock_search_document_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'delete'),
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
	 * Test delete with invalid SearchDocument
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::delete)]
	public function testDeleteWithInvalidSearchDocument(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'get'),
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
	 * Test get with invalid SearchDocument
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::get)]
	public function testGetWithInvalidSearchDocument(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'get'),
			"GET",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'get'),
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
	 * Test getElements with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::getElements)]
	public function testGetElementsSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'getElements'),
			"GET",
			array(
				"show_deleted" => "true"
			),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test getElements with invalid search document guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::getElements)]
	public function testGetElementsWithInvalidSearchDocument(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'getElements'),
			"GET",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document");
	}

	/**
	 * Test getElements with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::getElements)]
	public function testGetElementsWithGetError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("getElements")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'getElements'),
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
	 * Test getElements with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::getElements)]
	public function testGetElementsWithSearchDocumentLoadError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'getElements'),
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
	 * Test restore with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'restore'),
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
	 * Test restore with invalid SearchDocument
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::restore)]
	public function testRestoreWithInvalidSearchDocument(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'restore'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'restore'),
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
	 * Test save with new SearchDocument success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::save)]
	public function testSaveNewSearchDocumentSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'save'),
			"POST",
			array(
				"guid" => "",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with update SearchDocument success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::save)]
	public function testSaveUpdateSearchDocumentSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with invalid create_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'save'),
			"POST",
			array(
				"guid" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'save'),
			"POST",
			array(
				"guid" => "1234",
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
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'save'),
			"POST",
			array(
				"guid" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocument::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocument(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}