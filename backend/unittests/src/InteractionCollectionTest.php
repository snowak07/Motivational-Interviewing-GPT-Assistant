<?php
/**
 * Test the Interaction Collection class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class InteractionCollectionTest extends \PHPUnit\Framework\TestCase
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

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$interactions_tbl . ";");
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

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$interactions_tbl . ";");
	}

	/**
	 * Test getByUserGuid with an invalid sort by
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithInvalidSortBy(): void
	{
		$this->expectException(\Exception::class);

		$interaction_collection = new \Plugin\AIChat\InteractionCollection();
		$data = $interaction_collection->getByUserGuid("foo-user_guid", "foo-client_prompt_guid", "i.title");
	}

	/**
	 * Test getByUserGuid with no results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithNoResults(): void
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

		$collection = new \Plugin\AIChat\InteractionCollection();
		$data = $collection->getByUserGuid("foo");

		$this->assertTrue(isset($data->interactions));
		$this->assertTrue(count($data->interactions) == 0);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 0);
	}

	/**
	 * Test getByUserGuid with query error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithQueryError(): void
	{
		global $app;
		$this->expectException(\Exception::class);

		// Mock the database to throw an error
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->will($this->throwException(new \LC\CustomException("")));
		$app->db = $mock_db;

		$collection = new \Plugin\AIChat\InteractionCollection();
		$data = $collection->getByUserGuid("foo");
	}

	/**
	 * Test getByUserGuid with two results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\InteractionCollection::getByUserGuid)]
	public function testGetByUserGuidWithTwoResults(): void
	{
		global $app;

		$test_interaction_1 = array(
			"guid" => "foo-guid1",
			"client_prompt_guid" => "foo-client_prompt_guid1",
			"user_guid" => "foo-user_guid1",
			"user_message" => "foo-user_message1",
			"system_message" => "foo-system_message1",
			"system_response" => "foo-system_response1",
			"system_information" => "foo-system_information1",
			"other_data" => array("foo" => "bar"),
			"create_date" => 1,
			"delete_date" => -1
		);

		$test_interaction_2 = array(
			"guid" => "foo-guid2",
			"client_prompt_guid" => "foo-client_prompt_guid2",
			"user_guid" => "foo-user_guid1",
			"user_message" => "foo-user_message2",
			"system_message" => "foo-system_message2",
			"system_response" => "foo-system_response2",
			"system_information" => "foo-system_information2",
			"other_data" => array("foo" => "bar"),
			"create_date" => 2,
			"delete_date" => -1
		);

		// Mock the results we want
		$mock_cursor = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor->results = array($test_interaction_1, $test_interaction_2);

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")->willReturn($mock_cursor);
		$app->db = $mock_db;

		$interaction_collection = new \Plugin\AIChat\InteractionCollection();
		$data = $interaction_collection->getByUserGuid("foo-user_guid1", "foo-client_prompt_guid1", "create_date desc", 2, 3, true);

		$this->assertTrue(isset($data->interactions));
		$this->assertTrue(count($data->interactions) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $test_interaction_1["guid"]);
		$this->assertEquals($data->order[1], $test_interaction_2["guid"]);

		$result_interaction_1 = $data->interactions[$test_interaction_1["guid"]];
		$this->assertEquals($result_interaction_1["guid"], $test_interaction_1["guid"]);
		$this->assertEquals($result_interaction_1["client_prompt_guid"], $test_interaction_1["client_prompt_guid"]);
		$this->assertEquals($result_interaction_1["user_guid"], $test_interaction_1["user_guid"]);
		$this->assertEquals($result_interaction_1["user_message"], $test_interaction_1["user_message"]);
		$this->assertEquals($result_interaction_1["system_message"], $test_interaction_1["system_message"]);
		$this->assertEquals($result_interaction_1["system_response"], $test_interaction_1["system_response"]);
		$this->assertEquals($result_interaction_1["system_information"], $test_interaction_1["system_information"]);
		$this->assertEquals($result_interaction_1["other_data"], $test_interaction_1["other_data"]);
		$this->assertEquals($result_interaction_1["create_date"], $test_interaction_1["create_date"]);
		$this->assertEquals($result_interaction_1["delete_date"], $test_interaction_1["delete_date"]);

		$result_interaction_2 = $data->interactions[$test_interaction_2["guid"]];
		$this->assertEquals($result_interaction_2["guid"], $test_interaction_2["guid"]);
		$this->assertEquals($result_interaction_2["client_prompt_guid"], $test_interaction_2["client_prompt_guid"]);
		$this->assertEquals($result_interaction_2["user_guid"], $test_interaction_2["user_guid"]);
		$this->assertEquals($result_interaction_2["user_message"], $test_interaction_2["user_message"]);
		$this->assertEquals($result_interaction_2["system_message"], $test_interaction_2["system_message"]);
		$this->assertEquals($result_interaction_2["system_response"], $test_interaction_2["system_response"]);
		$this->assertEquals($result_interaction_2["system_information"], $test_interaction_2["system_information"]);
		$this->assertEquals($result_interaction_2["other_data"], $test_interaction_2["other_data"]);
		$this->assertEquals($result_interaction_2["create_date"], $test_interaction_2["create_date"]);
		$this->assertEquals($result_interaction_2["delete_date"], $test_interaction_2["delete_date"]);
	}

	/**
	 * Integration test
	 *
	 * @return void
	 */
	public function testIntegration(): void
	{
		// User 1
		$interaction_1 = new \Plugin\AIChat\Interaction();
		$interaction_1->setValue("guid", "foo-guid-1");
		$interaction_1->setValue("client_prompt_guid", "foo-client_prompt_guid-1");
		$interaction_1->setValue("user_guid", "foo-user_guid-1");
		$interaction_1->setValue("user_message", "foo-user_message 1 &()+}{}|");
		$interaction_1->setValue("system_message", "foo-system_message 1");
		$interaction_1->setValue("system_response", "{foo-response: \'bar\'}");
		$interaction_1->setValue("system_information", "{'foo-system_informtion': 'foobar'}");
		$interaction_1->setValue("other_data", array("foo" => "bar"));
		$interaction_1->setValue("create_date", 1);
		$interaction_1->setValue("delete_date", -1);
		$interaction_1->save();

		// User 1
		$interaction_2 = new \Plugin\AIChat\Interaction();
		$interaction_2->setValue("guid", "foo-guid-2");
		$interaction_2->setValue("client_prompt_guid", "foo-client_prompt_guid-1");
		$interaction_2->setValue("user_guid", "foo-user_guid-1");
		$interaction_2->setValue("user_message", "foo-user_message 2");
		$interaction_2->setValue("system_message", "foo-system_message 2 #@%@#(@)#$");
		$interaction_2->setValue("system_response", "{bar-response: 'baz'}");
		$interaction_2->setValue("system_information", "{\'bar-system_informtion\': 'foobar'}");
		$interaction_2->setValue("other_data", array("foo" => "bar"));
		$interaction_2->setValue("create_date", 2);
		$interaction_2->setValue("delete_date", -1);
		$interaction_2->save();

		// User 1 (Deleted)
		$interaction_3 = new \Plugin\AIChat\Interaction();
		$interaction_3->setValue("guid", "foo-guid-3");
		$interaction_3->setValue("client_prompt_guid", "foo-client_prompt_guid-2");
		$interaction_3->setValue("user_guid", "foo-user_guid-1");
		$interaction_3->setValue("user_message", "foo-user_message 3");
		$interaction_3->setValue("system_message", "foo-system_message 3");
		$interaction_3->setValue("system_response", "{bar-response: 'baz'}");
		$interaction_3->setValue("system_information", "{\'bar-system_informtion\': 'foobar'}");
		$interaction_3->setValue("other_data", array("foo" => "bar"));
		$interaction_3->setValue("create_date", 3);
		$interaction_3->setValue("delete_date", 4);
		$interaction_3->save();
		$interaction_3->delete();

		// User 2
		$interaction_4 = new \Plugin\AIChat\Interaction();
		$interaction_4->setValue("guid", "foo-guid-4");
		$interaction_4->setValue("client_prompt_guid", "foo-client_prompt_guid-3");
		$interaction_4->setValue("user_guid", "foo-user_guid-2");
		$interaction_4->setValue("user_message", "foo-user_message 1");
		$interaction_4->setValue("system_message", "foo-system_message 1");
		$interaction_4->setValue("system_response", "{foo-response: \'bar\'}");
		$interaction_4->setValue("system_information", "{'foo-system_informtion': 'foobar'}");
		$interaction_4->setValue("other_data", array("foo" => "bar"));
		$interaction_4->setValue("create_date", 2);
		$interaction_4->setValue("delete_date", -1);
		$interaction_4->save();

		// User 1 (Deleted then restored)
		$interaction_5 = new \Plugin\AIChat\Interaction();
		$interaction_5->setValue("guid", "foo-guid-5");
		$interaction_5->setValue("client_prompt_guid", "foo-client_prompt_guid-4");
		$interaction_5->setValue("user_guid", "foo-user_guid-1");
		$interaction_5->setValue("user_message", "foo-user_message 4");
		$interaction_5->setValue("system_message", "foo-system_message 4");
		$interaction_5->setValue("system_response", '{"foo" => "bar", "baz" => "buz"}');
		$interaction_5->setValue("system_information", '{"bar" => "baz"}');
		$interaction_5->setValue("other_data", array("foo" => "bar"));
		$interaction_5->setValue("create_date", 4);
		$interaction_5->setValue("delete_date", -1);
		$interaction_5->save();
		$interaction_5->delete();
		$interaction_5->restore();

		// Test loaded interaction vs saved interaction
		$loaded_interaction_1 = new \Plugin\AIChat\Interaction();
		$loaded_interaction_1->load($interaction_1->getValue("guid"));

		$this->assertEquals($loaded_interaction_1->getValue("guid"), $interaction_1->getValue("guid"));
		$this->assertEquals($loaded_interaction_1->getValue("client_prompt_guid"), $interaction_1->getValue("client_prompt_guid"));
		$this->assertEquals($loaded_interaction_1->getValue("user_guid"), $interaction_1->getValue("user_guid"));
		$this->assertEquals($loaded_interaction_1->getValue("user_message"), $interaction_1->getValue("user_message"));
		$this->assertEquals($loaded_interaction_1->getValue("system_message"), $interaction_1->getValue("system_message"));
		$this->assertEquals($loaded_interaction_1->getValue("system_response"), $interaction_1->getValue("system_response"));
		$this->assertEquals($loaded_interaction_1->getValue("system_information"), $interaction_1->getValue("system_information"));
		$this->assertEquals($loaded_interaction_1->getValue("other_data")->foo, $interaction_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_interaction_1->getValue("create_date"), $interaction_1->getValue("create_date"));
		$this->assertEquals($loaded_interaction_1->getValue("delete_date"), $interaction_1->getValue("delete_date"));

		// Retrieve all interactions of a user
		$interaction_collection = new \Plugin\AIChat\InteractionCollection();
		$data = $interaction_collection->getByUserGuid($interaction_1->getValue("user_guid"));

		$this->assertTrue(isset($data->interactions));
		$this->assertTrue(count($data->interactions) == 3);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 3);
		$this->assertEquals($data->order[0], $interaction_1->getValue("guid"));
		$this->assertEquals($data->order[1], $interaction_2->getValue("guid"));
		$this->assertEquals($data->order[2], $interaction_5->getValue("guid"));

		$interaction_collection_item_1 = $data->interactions[$data->order[0]];
		$this->assertEquals($interaction_collection_item_1["guid"], $interaction_1->getValue("guid"));
		$this->assertEquals($interaction_collection_item_1["client_prompt_guid"], $interaction_1->getValue("client_prompt_guid"));
		$this->assertEquals($interaction_collection_item_1["user_guid"], $interaction_1->getValue("user_guid"));
		$this->assertEquals($interaction_collection_item_1["user_message"], $interaction_1->getValue("user_message"));
		$this->assertEquals($interaction_collection_item_1["system_message"], $interaction_1->getValue("system_message"));
		$this->assertEquals($interaction_collection_item_1["system_response"], $interaction_1->getValue("system_response"));
		$this->assertEquals($interaction_collection_item_1["system_information"], $interaction_1->getValue("system_information"));
		$this->assertEquals($interaction_collection_item_1["other_data"]->foo, $interaction_1->getValue("other_data")['foo']);
		$this->assertEquals($interaction_collection_item_1["create_date"], $interaction_1->getValue("create_date"));
		$this->assertEquals($interaction_collection_item_1["delete_date"], $interaction_1->getValue("delete_date"));

		$interaction_collection_item_2 = $data->interactions[$data->order[1]];
		$this->assertEquals($interaction_collection_item_2["guid"], $interaction_2->getValue("guid"));
		$this->assertEquals($interaction_collection_item_2["client_prompt_guid"], $interaction_2->getValue("client_prompt_guid"));
		$this->assertEquals($interaction_collection_item_2["user_guid"], $interaction_2->getValue("user_guid"));
		$this->assertEquals($interaction_collection_item_2["user_message"], $interaction_2->getValue("user_message"));
		$this->assertEquals($interaction_collection_item_2["system_message"], $interaction_2->getValue("system_message"));
		$this->assertEquals($interaction_collection_item_2["system_response"], $interaction_2->getValue("system_response"));
		$this->assertEquals($interaction_collection_item_2["system_information"], $interaction_2->getValue("system_information"));
		$this->assertEquals($interaction_collection_item_2["other_data"]->foo, $interaction_2->getValue("other_data")['foo']);
		$this->assertEquals($interaction_collection_item_2["create_date"], $interaction_2->getValue("create_date"));
		$this->assertEquals($interaction_collection_item_2["delete_date"], $interaction_2->getValue("delete_date"));

		$interaction_collection_item_3 = $data->interactions[$data->order[2]];
		$this->assertEquals($interaction_collection_item_3["guid"], $interaction_5->getValue("guid"));
		$this->assertEquals($interaction_collection_item_3["client_prompt_guid"], $interaction_5->getValue("client_prompt_guid"));
		$this->assertEquals($interaction_collection_item_3["user_guid"], $interaction_5->getValue("user_guid"));
		$this->assertEquals($interaction_collection_item_3["user_message"], $interaction_5->getValue("user_message"));
		$this->assertEquals($interaction_collection_item_3["system_message"], $interaction_5->getValue("system_message"));
		$this->assertEquals($interaction_collection_item_3["system_response"], $interaction_5->getValue("system_response"));
		$this->assertEquals($interaction_collection_item_3["system_information"], $interaction_5->getValue("system_information"));
		$this->assertEquals($interaction_collection_item_3["other_data"]->foo, $interaction_5->getValue("other_data")['foo']);
		$this->assertEquals($interaction_collection_item_3["create_date"], $interaction_5->getValue("create_date"));
		$this->assertEquals($interaction_collection_item_3["delete_date"], $interaction_5->getValue("delete_date"));

		$interaction_collection = new \Plugin\AIChat\InteractionCollection();
		$data = $interaction_collection->getByUserGuid($interaction_1->getValue("user_guid"), $interaction_1->getValue("client_prompt_guid"));

		$this->assertTrue(isset($data->interactions));
		$this->assertTrue(count($data->interactions) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $interaction_1->getValue("guid"));
		$this->assertEquals($data->order[1], $interaction_2->getValue("guid"));

		$interaction_collection_item_1 = $data->interactions[$data->order[0]];
		$this->assertEquals($interaction_collection_item_1["guid"], $interaction_1->getValue("guid"));
		$this->assertEquals($interaction_collection_item_1["client_prompt_guid"], $interaction_1->getValue("client_prompt_guid"));
		$this->assertEquals($interaction_collection_item_1["user_guid"], $interaction_1->getValue("user_guid"));
		$this->assertEquals($interaction_collection_item_1["user_message"], $interaction_1->getValue("user_message"));
		$this->assertEquals($interaction_collection_item_1["system_message"], $interaction_1->getValue("system_message"));
		$this->assertEquals($interaction_collection_item_1["system_response"], $interaction_1->getValue("system_response"));
		$this->assertEquals($interaction_collection_item_1["system_information"], $interaction_1->getValue("system_information"));
		$this->assertEquals($interaction_collection_item_1["other_data"]->foo, $interaction_1->getValue("other_data")['foo']);
		$this->assertEquals($interaction_collection_item_1["create_date"], $interaction_1->getValue("create_date"));
		$this->assertEquals($interaction_collection_item_1["delete_date"], $interaction_1->getValue("delete_date"));

		$interaction_collection_item_2 = $data->interactions[$data->order[1]];
		$this->assertEquals($interaction_collection_item_2["guid"], $interaction_2->getValue("guid"));
		$this->assertEquals($interaction_collection_item_2["client_prompt_guid"], $interaction_2->getValue("client_prompt_guid"));
		$this->assertEquals($interaction_collection_item_2["user_guid"], $interaction_2->getValue("user_guid"));
		$this->assertEquals($interaction_collection_item_2["user_message"], $interaction_2->getValue("user_message"));
		$this->assertEquals($interaction_collection_item_2["system_message"], $interaction_2->getValue("system_message"));
		$this->assertEquals($interaction_collection_item_2["system_response"], $interaction_2->getValue("system_response"));
		$this->assertEquals($interaction_collection_item_2["system_information"], $interaction_2->getValue("system_information"));
		$this->assertEquals($interaction_collection_item_2["other_data"]->foo, $interaction_2->getValue("other_data")['foo']);
		$this->assertEquals($interaction_collection_item_2["create_date"], $interaction_2->getValue("create_date"));
		$this->assertEquals($interaction_collection_item_2["delete_date"], $interaction_2->getValue("delete_date"));
	}
}