<?php
/**
 * Tests the SearchDocument class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class SearchDocumentTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Database to use
	 *
	 * @var \LC\Database\DB
	 */
	protected $app_db = null;

	/**
	 * Set up for each test
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		global $app;

		$this->app_db = $app->db;

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$search_documents_tbl . ";");
		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$search_document_elements_tbl . ";");
	}

	/**
	 * Clean up after each test
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
		global $app;

		$app->db = $this->app_db;

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$search_documents_tbl . ";");
		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$search_document_elements_tbl . ";");
	}

	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\SearchDocument::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\SearchDocument();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "guid");
		$this->assertEquals($identifier->getName(), "guid");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_search_documents}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("guid");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}

	/**
	 * Test getElements with no results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\SearchDocument::getElements)]
	public function testGetElementsWithNoResults(): void
	{
		global $app;

		// Mock the results we want
		$mock_cursor = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor->results = array();

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->willReturn($mock_cursor);
		$app->db = $mock_db;

		$search_document = new \Plugin\AIChat\SearchDocument();
		$data = $search_document->getElements("foo-guid", false);

		$this->assertTrue(isset($data->elements));
		$this->assertTrue(count($data->elements) == 0);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 0);
	}

	/**
	 * Test getElements with query error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\SearchDocument::getElements)]
	public function testGetElementsWithQueryError(): void
	{
		global $app;
		$this->expectException(\Exception::class);

		// Mock the database to throw an error
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->will($this->throwException(new \LC\CustomException("")));
		$app->db = $mock_db;

		$search_document = new \Plugin\AIChat\SearchDocument();
		$data = $search_document->getElements("foo-guid", false);
	}

	/**
	 * Test getElements with two results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\SearchDocument::getElements)]
	public function testGetElementsWithTwoResults(): void
	{
		global $app;

		$test_element_1 = array(
			"guid" => "foo-guid1",
			"document_guid" => "foo-document_guid1",
			"text" => "foo-text1",
			"embedding" => "foo-embedding1",
			"other_data" => array("foo" => "bar"),
			"create_date" => 1,
			"delete_date" => -1
		);

		$test_element_2 = array(
			"guid" => "foo-guid2",
			"document_guid" => "foo-document_guid1",
			"text" => "foo-text2",
			"embedding" => "foo-embedding2",
			"other_data" => array("foo" => "bar"),
			"create_date" => 2,
			"delete_date" => 3
		);

		$mock_cursor_load = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor_load->results = array();

		// Mock the results we want
		$mock_cursor = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor->results = array($test_element_1, $test_element_2);

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")->willReturn($mock_cursor);
		$app->db = $mock_db;

		$search_document = new \Plugin\AIChat\SearchDocument();
		$data = $search_document->getElements($test_element_1['document_guid'], true);

		$this->assertTrue(isset($data->elements));
		$this->assertTrue(count($data->elements) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $test_element_1["guid"]);
		$this->assertEquals($data->order[1], $test_element_2["guid"]);
		$this->assertTrue(isset($data->elements[$test_element_1["guid"]]));
		$this->assertTrue(isset($data->elements[$test_element_1["guid"]]));
	}

	/**
	 * Integration test
	 *
	 * @return void
	 */
	public function testIntegration(): void
	{
		// Document 1
		$search_document_1 = new \Plugin\AIChat\SearchDocument();
		$search_document_1->setValue("guid", "foo-document_guid-1");
		$search_document_1->setValue("other_data", array("foo" => "bar"));
		$search_document_1->setValue("create_date", 1);
		$search_document_1->setValue("delete_date", -1);
		$search_document_1->save();

		// Document 1 Element 1
		$search_document_element_1 = new \Plugin\AIChat\SearchDocumentElement();
		$search_document_element_1->setValue("guid", "foo-element_guid-1");
		$search_document_element_1->setValue("document_guid", "foo-document_guid-1");
		$search_document_element_1->setValue("text", "foo-text 1 #@%@#(@)#$");
		$search_document_element_1->setValue("embedding", "[0.900348343, 0.23909034930, 0.340903490]");
		$search_document_element_1->setValue("other_data", array("foo" => "bar"));
		$search_document_element_1->setValue("create_date", 2);
		$search_document_element_1->setValue("delete_date", -1);
		$search_document_element_1->save();

		// Document 1 Element 2 (Deleted)
		$search_document_element_2 = new \Plugin\AIChat\SearchDocumentElement();
		$search_document_element_2->setValue("guid", "foo-element_guid-2");
		$search_document_element_2->setValue("document_guid", "foo-document_guid-1");
		$search_document_element_2->setValue("text", "foo-text 2 1: FOOBAR;, II.1.2.3.");
		$search_document_element_2->setValue("embedding", "[0.900348343, 0.23909034930, 0.340903490]");
		$search_document_element_2->setValue("other_data", array("foo" => "bar"));
		$search_document_element_2->setValue("create_date", 3);
		$search_document_element_2->setValue("delete_date", -1);
		$search_document_element_2->save();
		$search_document_element_2->delete();

		// Document 1 Element 3 (Deleted then restored)
		$search_document_element_3 = new \Plugin\AIChat\SearchDocumentElement();
		$search_document_element_3->setValue("guid", "foo-element_guid-3");
		$search_document_element_3->setValue("document_guid", "foo-document_guid-1");
		$search_document_element_3->setValue("text", "foo-text 3 1: BARBAZ;, III.1.2.3.");
		$search_document_element_3->setValue("embedding", "[0.900348343, 0.23909034930, 0.340903490]");
		$search_document_element_3->setValue("other_data", array("foo" => "bar"));
		$search_document_element_3->setValue("create_date", 4);
		$search_document_element_3->setValue("delete_date", -1);
		$search_document_element_3->save();
		$search_document_element_3->delete();
		$search_document_element_3->restore();

		// Document 2
		$search_document_2 = new \Plugin\AIChat\SearchDocument();
		$search_document_2->setValue("guid", "foo-document_guid-2");
		$search_document_2->setValue("other_data", array("foo" => "bar"));
		$search_document_2->setValue("create_date", 5);
		$search_document_2->setValue("delete_date", -1);
		$search_document_2->save();

		// Document 2 Element 1
		$search_document_element_4 = new \Plugin\AIChat\SearchDocumentElement();
		$search_document_element_4->setValue("guid", "foo-element_guid-4");
		$search_document_element_4->setValue("document_guid", "foo-document_guid-2");
		$search_document_element_4->setValue("text", "foo-text 4 #@%@#(@)#$");
		$search_document_element_4->setValue("embedding", "[0.900348343, 0.23909034930, 0.340903490]");
		$search_document_element_4->setValue("other_data", array("foo" => "bar"));
		$search_document_element_4->setValue("create_date", 6);
		$search_document_element_4->setValue("delete_date", -1);
		$search_document_element_4->save();

		// Test loaded document vs saved document
		$loaded_search_document_1 = new \Plugin\AIChat\SearchDocument();
		$loaded_search_document_1->load($search_document_1->getValue("guid"));

		$this->assertEquals($loaded_search_document_1->getValue("guid"), $search_document_1->getValue("guid"));
		$this->assertEquals($loaded_search_document_1->getValue("other_data")->foo, $search_document_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_search_document_1->getValue("create_date"), $search_document_1->getValue("create_date"));
		$this->assertEquals($loaded_search_document_1->getValue("delete_date"), $search_document_1->getValue("delete_date"));

		// Test loaded element vs saved element
		$loaded_search_document_element_1 = new \Plugin\AIChat\SearchDocumentElement();
		$loaded_search_document_element_1->load($search_document_element_1->getValue("guid"));

		$this->assertEquals($loaded_search_document_element_1->getValue("guid"), $search_document_element_1->getValue("guid"));
		$this->assertEquals($loaded_search_document_element_1->getValue("document_guid"), $search_document_element_1->getValue("document_guid"));
		$this->assertEquals($loaded_search_document_element_1->getValue("text"), $search_document_element_1->getValue("text"));
		$this->assertEquals($loaded_search_document_element_1->getValue("embedding"), $search_document_element_1->getValue("embedding"));
		$this->assertEquals($loaded_search_document_element_1->getValue("other_data")->foo, $search_document_element_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_search_document_element_1->getValue("create_date"), $search_document_element_1->getValue("create_date"));
		$this->assertEquals($loaded_search_document_element_1->getValue("delete_date"), $search_document_element_1->getValue("delete_date"));

		// Retrieve all elements of search document 1
		$search_document = new \Plugin\AIChat\SearchDocument();
		$data = $search_document->getElements($search_document_1->getValue("guid"), false);

		$this->assertTrue(isset($data->elements));
		$this->assertTrue(count($data->elements) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $search_document_element_1->getValue("guid"));
		$this->assertEquals($data->order[1], $search_document_element_3->getValue("guid"));

		$search_document_element_data_1 = $data->elements[$data->order[0]];
		$this->assertEquals($search_document_element_data_1["guid"], $search_document_element_1->getValue("guid"));
		$this->assertEquals($search_document_element_data_1["document_guid"], $search_document_element_1->getValue("document_guid"));
		$this->assertEquals($search_document_element_data_1["text"], $search_document_element_1->getValue("text"));
		$this->assertEquals($search_document_element_data_1["embedding"], $search_document_element_1->getValue("embedding"));
		$this->assertEquals($search_document_element_data_1["other_data"]->foo, $search_document_element_1->getValue("other_data")['foo']);
		$this->assertEquals($search_document_element_data_1["create_date"], $search_document_element_1->getValue("create_date"));
		$this->assertEquals($search_document_element_data_1["delete_date"], $search_document_element_1->getValue("delete_date"));

		$search_document_element_data_2 = $data->elements[$data->order[1]];
		$this->assertEquals($search_document_element_data_2["guid"], $search_document_element_3->getValue("guid"));
		$this->assertEquals($search_document_element_data_2["document_guid"], $search_document_element_3->getValue("document_guid"));
		$this->assertEquals($search_document_element_data_2["text"], $search_document_element_3->getValue("text"));
		$this->assertEquals($search_document_element_data_2["embedding"], $search_document_element_3->getValue("embedding"));
		$this->assertEquals($search_document_element_data_2["other_data"]->foo, $search_document_element_3->getValue("other_data")['foo']);
		$this->assertEquals($search_document_element_data_2["create_date"], $search_document_element_3->getValue("create_date"));
		$this->assertEquals($search_document_element_data_2["delete_date"], $search_document_element_3->getValue("delete_date"));
	}
}
