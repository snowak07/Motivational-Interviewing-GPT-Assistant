<?php
/**
 * Test the SearchDocumentElement class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class SearchDocumentElementTest extends \PHPUnit\Framework\TestCase
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
		\Plugin\AIChat\Helpers::unsetMe();
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
		\Plugin\AIChat\Helpers::unsetMe();
	}

	/**
	 * Set up default mocks for our test
	 *
	 * @param string 	$test_search_document_element_guid			SearchDocumentElement guid to test with
	 * @param array 	$mock_search_document_elements_return_data		Results when calling the SearchDocumentElement class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_search_document_element_guid = "", $mock_search_document_elements_return_data = null): array
	{
		global $app;

		// Mock the search documents and configure it to return fake data
		if (!isset($mock_search_document_element_return_data)) {
			$mock_search_document_element_return_data = new \StdClass;
			$mock_search_document_element_return_data->elements = array();
			$mock_search_document_element_return_data->order = array();
		}

		// Mock a SearchDocumentElement
		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("delete")
					->willReturn(true);
		$mock_search_document_element->method("load")
					->willReturn(true);
		$mock_search_document_element->method("restore")
					->willReturn(true);
		$mock_search_document_element->method("save")
					->willReturn(true);

		if ($test_search_document_element_guid) {
			$mock_search_document_element->guid = $test_search_document_element_guid;
		}

		// Mock a SearchDocument
		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("save")
					->willReturn(true);
		$mock_search_document->method("getValue")
					->willReturn("foo-document_guid");

		// Mock a factory and configure it to build our search document mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\SearchDocumentElement', $mock_search_document_element),
									array('\Plugin\AIChat\SearchDocument', $mock_search_document),
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_search_document_element" => $mock_search_document_element, "mock_search_document_element_return_data" => $mock_search_document_element_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(true);
		$mock_search_document_element->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'delete'),
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
	 * Test delete with invalid SearchDocumentElement
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::delete)]
	public function testDeleteWithInvalidSearchDocumentElement(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'delete'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document element");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'get'),
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
	 * Test get with invalid SearchDocumentElement
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::get)]
	public function testGetWithInvalidSearchDocumentElement(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'get'),
			"GET",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document element");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'get'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'restore'),
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
	 * Test restore with invalid SearchDocumentElement
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::restore)]
	public function testRestoreWithInvalidSearchDocumentElement(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'restore'),
			"POST",
			array(),
			array(
				"guid" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search document element");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'restore'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(true);
		$mock_search_document_element->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'restore'),
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
	 * Test save with new SearchDocumentElement success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveNewSearchDocumentElementSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with update SearchDocumentElement success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveUpdateSearchDocumentSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
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
	 * Test save with invalid embedding
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithInvalidEmbedding(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "",
				"other_data" => array(),
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid embedding");
	}

	/**
	 * Test save with invalid search document
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithInvalidSearchDocument(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(true);

		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\SearchDocumentElement', $mock_search_document_element),
									array('\Plugin\AIChat\SearchDocument', $mock_search_document),
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-document_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array(),
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid search_document");
	}

	/**
	 * Test save with invalid text
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithInvalidText(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "",
				"embedding" => "foo-embedding",
				"other_data" => array(),
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid text");
	}

	/**
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with a search document load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithLoadSearchDocumentError(): void {
		global $app;

		// Mock a SearchDocumentElement
		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(true);

		// Mock a SearchDocument
		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		// Mock a factory and configure it to build our search document mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\SearchDocumentElement', $mock_search_document_element),
									array('\Plugin\AIChat\SearchDocument', $mock_search_document),
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "",
				"document_guid" => "foo-document_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with a search document save error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithSaveNewSearchDocumentSaveError(): void {
		global $app;

		// Mock a SearchDocumentElement
		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(true);

		// Mock a SearchDocument
		$mock_search_document = $this->createMock(\Plugin\AIChat\SearchDocument::class);
		$mock_search_document->method("load")
					->willReturn(true);
		$mock_search_document->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		// Mock a factory and configure it to build our search document mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\SearchDocumentElement', $mock_search_document_element),
									array('\Plugin\AIChat\SearchDocument', $mock_search_document),
								)
							);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "",
				"document_guid" => "",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with a new search search document save success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithSaveNewSearchDocumentSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "",
				"document_guid" => "",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array()
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
	#[CoversFunction(\Plugin\AIChat\Controllers\SearchDocumentElement::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_search_document_element = $this->createMock(\Plugin\AIChat\SearchDocumentElement::class);
		$mock_search_document_element->method("load")
					->willReturn(true);
		$mock_search_document_element->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_search_document_element);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), 'save'),
			"POST",
			array(
				"guid" => "1234",
				"document_guid" => "foo-doc_guid",
				"text" => "foo-text",
				"embedding" => "foo-embedding",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}