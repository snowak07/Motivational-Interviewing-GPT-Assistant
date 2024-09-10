<?php
/**
 * Tests the MockClientPrompt class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class MockClientPromptTest extends \PHPUnit\Framework\TestCase
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
	}

	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\MockClientPrompt();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "guid");
		$this->assertEquals($identifier->getName(), "guid");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_mock_client_prompts}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("guid");
		$temp = $data->getValue("background_guid");
		$temp = $data->getValue("content");
		$temp = $data->getValue("mi_technique_slug");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}

	/**
	 * Test buildFromObject with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::buildFromObject)]
	public function testBuildFromObject(): void
	{
		$mock_prompt = new \Plugin\AIChat\MockClientPrompt();

		$object = new \StdClass;
		$object->guid = "foo-guid";
		$object->background_guid = "foo-background_guid";
		$object->mi_technique_slug = "foo-mi_technique_slug";
		$object->content = "foo-content";
		$object->other_data = array("foo" => "bar");
		$object->create_date = 123;
		$object->delete_date = -1;

		$object->client_name = "foo-client_name";
		$object->profile_picture = "foo-profile_picture";
		$object->background_info = "foo-background_info";
		$object->background_other_data = "foo-background_other_data";
		$object->background_create_date = 12;
		$object->background_delete_date = -1;

		$mock_prompt->buildFromObject($object);

		$this->assertEquals($mock_prompt->getValue("guid"), $object->guid);
		$this->assertEquals($mock_prompt->getValue("background_guid"), $object->background_guid);
		$this->assertEquals($mock_prompt->getValue("content"), $object->content);
		$this->assertEquals($mock_prompt->getValue("mi_technique_slug"), $object->mi_technique_slug);
		$this->assertEquals($mock_prompt->getValue("other_data"), $object->other_data);
		$this->assertEquals($mock_prompt->getValue("create_date"), $object->create_date);
		$this->assertEquals($mock_prompt->getValue("delete_date"), $object->delete_date);

		$background = $mock_prompt->background;
		$this->assertEquals($background->getValue("guid"), $object->background_guid);
		$this->assertEquals($background->getValue("client_name"), $object->client_name);
		$this->assertEquals($background->getValue("profile_picture"), $object->profile_picture);
		$this->assertEquals($background->getValue("background_info"), $object->background_info);
		$this->assertEquals($background->getValue("other_data"), $object->background_other_data);
		$this->assertEquals($background->getValue("create_date"), $object->background_create_date);
		$this->assertEquals($background->getValue("delete_date"), $object->background_delete_date);
	}

	/**
	 * Test getByBackgroundGuid with an invalid background guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithInvalidBackgroundGuid(): void
	{
		$this->expectException(\Exception::class);

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByBackgroundGuid("", "", false);
	}

	/**
	 * Test getByBackgroundGuid with an invalid sort by
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithInvalidSortBy(): void
	{
		$this->expectException(\Exception::class);

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByBackgroundGuid("foo-background_guid", "p.delete_date", false);
	}

	/**
	 * Test getByBackgroundGuid with no results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithNoResults(): void
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

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByBackgroundGuid("foo-background_guid", "create_date asc", true);

		$this->assertTrue(isset($data->prompts));
		$this->assertTrue(count($data->prompts) == 0);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 0);
	}

	/**
	 * Test getByBackgroundGuid with query error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithQueryError(): void
	{
		global $app;
		$this->expectException(\Exception::class);

		// Mock the database to throw an error
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->will($this->throwException(new \LC\CustomException("")));
		$app->db = $mock_db;

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByBackgroundGuid("foo-background_guid", "", false);
	}

	/**
	 * Test getByBackgroundGuid with two results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByBackgroundGuid)]
	public function testGetByBackgroundGuidWithTwoResults(): void
	{
		global $app;

		$test_prompt_1 = array(
			"guid" => "foo-guid1",
			"background_guid" => "foo-background_guid",
			"mi_technique_slug" => "foo-mi_technique_slug1",
			"content" => "foo-content1",
			"other_data" => array("foo" => "bar"),
			"create_date" => 1,
			"delete_date" => -1,
			"client_name" => "foo-client_name",
			"profile_picture" => "foo-profile_picture",
			"background_info" => "foo-background_info",
			"background_other_data" => array("bar" => "baz"),
			"background_create_date" => 2,
			"background_delete_date" => -1
		);

		$test_prompt_2 = array(
			"guid" => "foo-guid2",
			"background_guid" => "foo-background_guid",
			"mi_technique_slug" => "foo-mi_technique_slug2",
			"content" => "foo-content2",
			"other_data" => array("foo" => "bar"),
			"create_date" => 2,
			"delete_date" => -1,
			"client_name" => "foo-client_name",
			"profile_picture" => "foo-profile_picture",
			"background_info" => "foo-background_info",
			"background_other_data" => array("bar" => "baz"),
			"background_create_date" => 2,
			"background_delete_date" => -1
		);

		// Mock the results we want
		$mock_cursor = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor->results = array($test_prompt_1, $test_prompt_2);

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")->willReturn($mock_cursor);
		$app->db = $mock_db;

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByBackgroundGuid("foo-background_guid", "create_date desc", false);

		$this->assertTrue(isset($data->prompts));
		$this->assertTrue(count($data->prompts) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $test_prompt_1["guid"]);
		$this->assertEquals($data->order[1], $test_prompt_2["guid"]);

		$result_prompt_1 = $data->prompts[$test_prompt_1["guid"]];
		$this->assertEquals($result_prompt_1["guid"], $test_prompt_1["guid"]);
		$this->assertEquals($result_prompt_1["background_guid"], $test_prompt_1["background_guid"]);
		$this->assertEquals($result_prompt_1["mi_technique_slug"], $test_prompt_1["mi_technique_slug"]);
		$this->assertEquals($result_prompt_1["content"], $test_prompt_1["content"]);
		$this->assertEquals($result_prompt_1["other_data"], $test_prompt_1["other_data"]);
		$this->assertEquals($result_prompt_1["create_date"], $test_prompt_1["create_date"]);
		$this->assertEquals($result_prompt_1["delete_date"], $test_prompt_1["delete_date"]);

		$result_background_1 = $data->prompts[$test_prompt_1["guid"]]["background"];
		$this->assertEquals($result_background_1["guid"], $test_prompt_1["background_guid"]);
		$this->assertEquals($result_background_1["client_name"], $test_prompt_1["client_name"]);
		$this->assertEquals($result_background_1["profile_picture"], $test_prompt_1["profile_picture"]);
		$this->assertEquals($result_background_1["background_info"], $test_prompt_1["background_info"]);
		$this->assertEquals($result_background_1["other_data"], $test_prompt_1["background_other_data"]);
		$this->assertEquals($result_background_1["create_date"], $test_prompt_1["background_create_date"]);
		$this->assertEquals($result_background_1["delete_date"], $test_prompt_1["background_delete_date"]);

		$result_prompt_2 = $data->prompts[$test_prompt_2["guid"]];
		$this->assertEquals($result_prompt_2["guid"], $test_prompt_2["guid"]);
		$this->assertEquals($result_prompt_2["background_guid"], $test_prompt_2["background_guid"]);
		$this->assertEquals($result_prompt_2["mi_technique_slug"], $test_prompt_2["mi_technique_slug"]);
		$this->assertEquals($result_prompt_2["content"], $test_prompt_2["content"]);
		$this->assertEquals($result_prompt_2["other_data"], $test_prompt_2["other_data"]);
		$this->assertEquals($result_prompt_2["create_date"], $test_prompt_2["create_date"]);
		$this->assertEquals($result_prompt_2["delete_date"], $test_prompt_2["delete_date"]);

		$result_background_2 = $data->prompts[$test_prompt_2["guid"]]["background"];
		$this->assertEquals($result_background_2["guid"], $test_prompt_2["background_guid"]);
		$this->assertEquals($result_background_2["client_name"], $test_prompt_2["client_name"]);
		$this->assertEquals($result_background_2["profile_picture"], $test_prompt_2["profile_picture"]);
		$this->assertEquals($result_background_2["background_info"], $test_prompt_2["background_info"]);
		$this->assertEquals($result_background_2["other_data"], $test_prompt_2["background_other_data"]);
		$this->assertEquals($result_background_2["create_date"], $test_prompt_2["background_create_date"]);
		$this->assertEquals($result_background_2["delete_date"], $test_prompt_2["background_delete_date"]);
	}

	/**
	 * Test getByMITechnique with an invalid background guid
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithInvalidMITechnique(): void
	{
		$this->expectException(\Exception::class);

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByMITechnique("", "", false);
	}

	/**
	 * Test getByMITechnique with an invalid sort by
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithInvalidSortBy(): void
	{
		$this->expectException(\Exception::class);

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByMITechnique("foo-technique", "p.delete_date", false);
	}

	/**
	 * Test getByMITechnique with no results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithNoResults(): void
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

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByMITechnique("foo-technique", "create_date asc", true);

		$this->assertTrue(isset($data->prompts));
		$this->assertTrue(count($data->prompts) == 0);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 0);
	}

	/**
	 * Test getByMITechnique with query error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithQueryError(): void
	{
		global $app;
		$this->expectException(\Exception::class);

		// Mock the database to throw an error
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->will($this->throwException(new \LC\CustomException("")));
		$app->db = $mock_db;

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByBackgroundGuid("foo-technique", "", false);
	}

	/**
	 * Test getByMITechnique with two results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientPrompt::getByMITechnique)]
	public function testGetByMITechniqueWithTwoResults(): void
	{
		global $app;

		$test_prompt_1 = array(
			"guid" => "foo-guid1",
			"background_guid" => "foo-background_guid1",
			"mi_technique_slug" => "foo-mi_technique_slug1",
			"content" => "foo-content1",
			"other_data" => array("foo" => "bar"),
			"create_date" => 1,
			"delete_date" => -1,
			"client_name" => "foo-client_name_1",
			"profile_picture" => "foo-profile_picture_1",
			"background_info" => "foo-background_info_1",
			"background_other_data" => array("bar" => "baz"),
			"background_create_date" => 2,
			"background_delete_date" => -1
		);

		$test_prompt_2 = array(
			"guid" => "foo-guid2",
			"background_guid" => "foo-background_guid2",
			"mi_technique_slug" => "foo-mi_technique_slug1",
			"content" => "foo-content2",
			"other_data" => array("foo" => "bar"),
			"create_date" => 2,
			"delete_date" => -1,
			"client_name" => "foo-client_name_2",
			"profile_picture" => "foo-profile_picture_2",
			"background_info" => "foo-background_info_2",
			"background_other_data" => array("bar" => "baz"),
			"background_create_date" => 2,
			"background_delete_date" => -1
		);

		// Mock the results we want
		$mock_cursor = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor->results = array($test_prompt_1, $test_prompt_2);

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")->willReturn($mock_cursor);
		$app->db = $mock_db;

		$client_prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $client_prompt->getByMITechnique("foo-technique", "create_date desc", false);

		$this->assertTrue(isset($data->prompts));
		$this->assertTrue(count($data->prompts) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $test_prompt_1["guid"]);
		$this->assertEquals($data->order[1], $test_prompt_2["guid"]);

		$result_prompt_1 = $data->prompts[$test_prompt_1["guid"]];
		$this->assertEquals($result_prompt_1["guid"], $test_prompt_1["guid"]);
		$this->assertEquals($result_prompt_1["background_guid"], $test_prompt_1["background_guid"]);
		$this->assertEquals($result_prompt_1["content"], $test_prompt_1["content"]);
		$this->assertEquals($result_prompt_1["mi_technique_slug"], $test_prompt_1["mi_technique_slug"]);
		$this->assertEquals($result_prompt_1["other_data"], $test_prompt_1["other_data"]);
		$this->assertEquals($result_prompt_1["create_date"], $test_prompt_1["create_date"]);
		$this->assertEquals($result_prompt_1["delete_date"], $test_prompt_1["delete_date"]);

		$result_background_1 = $data->prompts[$test_prompt_1["guid"]]["background"];
		$this->assertEquals($result_background_1["guid"], $test_prompt_1["background_guid"]);
		$this->assertEquals($result_background_1["client_name"], $test_prompt_1["client_name"]);
		$this->assertEquals($result_background_1["profile_picture"], $test_prompt_1["profile_picture"]);
		$this->assertEquals($result_background_1["background_info"], $test_prompt_1["background_info"]);
		$this->assertEquals($result_background_1["other_data"], $test_prompt_1["background_other_data"]);
		$this->assertEquals($result_background_1["create_date"], $test_prompt_1["background_create_date"]);
		$this->assertEquals($result_background_1["delete_date"], $test_prompt_1["background_delete_date"]);

		$result_prompt_2 = $data->prompts[$test_prompt_2["guid"]];
		$this->assertEquals($result_prompt_2["guid"], $test_prompt_2["guid"]);
		$this->assertEquals($result_prompt_2["background_guid"], $test_prompt_2["background_guid"]);
		$this->assertEquals($result_prompt_2["content"], $test_prompt_2["content"]);
		$this->assertEquals($result_prompt_2["mi_technique_slug"], $test_prompt_2["mi_technique_slug"]);
		$this->assertEquals($result_prompt_2["other_data"], $test_prompt_2["other_data"]);
		$this->assertEquals($result_prompt_2["create_date"], $test_prompt_2["create_date"]);
		$this->assertEquals($result_prompt_2["delete_date"], $test_prompt_2["delete_date"]);

		$result_background_2 = $data->prompts[$test_prompt_2["guid"]]["background"];
		$this->assertEquals($result_background_2["guid"], $test_prompt_2["background_guid"]);
		$this->assertEquals($result_background_2["client_name"], $test_prompt_2["client_name"]);
		$this->assertEquals($result_background_2["profile_picture"], $test_prompt_2["profile_picture"]);
		$this->assertEquals($result_background_2["background_info"], $test_prompt_2["background_info"]);
		$this->assertEquals($result_background_2["other_data"], $test_prompt_2["background_other_data"]);
		$this->assertEquals($result_background_2["create_date"], $test_prompt_2["background_create_date"]);
		$this->assertEquals($result_background_2["delete_date"], $test_prompt_2["background_delete_date"]);
	}

	/**
	 * Integration Test
	 *
	 * @return void
	 */
	public function testIntegration(): void
	{
		// Background 1
		$background_1 = new \Plugin\AIChat\MockClientBackground();
		$background_1->setValue("guid", "foo-background_guid1");
		$background_1->setValue("client_name", "foo-client_name1");
		$background_1->setValue("profile_picture", "foo-profile_picture1");
		$background_1->setValue("background_info", "foo-background_info1");
		$background_1->setValue("other_data", array("foo" => "bar"));
		$background_1->setValue("create_date", 1);
		$background_1->setValue("delete_date", -1);
		$background_1->save();

		// Technique 1
		$technique_1 = new \Plugin\AIChat\MITechnique();
		$technique_1->setValue("slug", "foo-mi_technique_slug1");
		$technique_1->setValue("name", "foo-name1");
		$technique_1->setValue("definition", "foo-definition1");
		$technique_1->setValue("user_instruction", "foo-user_instruction1");
		$technique_1->setValue("ai_instruction", "foo-ai_instruction1");
		$technique_1->setValue("other_data", array("foo" => "bar"));
		$technique_1->setValue("create_date", 1);
		$technique_1->setValue("delete_date", -1);

		// Prompt 1
		$prompt_1 = new \Plugin\AIChat\MockClientPrompt();
		$prompt_1->setValue("guid", "foo-guid1");
		$prompt_1->setValue("background_guid", "foo-background_guid1");
		$prompt_1->setValue("mi_technique_slug", "foo-mi_technique_slug1");
		$prompt_1->setValue("content", "foo-content1");
		$prompt_1->setValue("other_data", array("foo" => "bar"));
		$prompt_1->setValue("create_date", 3);
		$prompt_1->setValue("delete_date", -1);
		$prompt_1->save();

		// Technique 2
		$technique_2 = new \Plugin\AIChat\MITechnique();
		$technique_2->setValue("slug", "foo-mi_technique_slug2");
		$technique_2->setValue("name", "foo-name2");
		$technique_2->setValue("definition", "foo-definition2");
		$technique_2->setValue("user_instruction", "foo-user_instruction2");
		$technique_2->setValue("ai_instruction", "foo-ai_instruction2");
		$technique_2->setValue("other_data", array("foo" => "bar"));
		$technique_2->setValue("create_date", 3);
		$technique_2->setValue("delete_date", -1);

		// Prompt 2 (deleted)
		$prompt_2 = new \Plugin\AIChat\MockClientPrompt();
		$prompt_2->setValue("guid", "foo-guid2");
		$prompt_2->setValue("background_guid", "foo-background_guid1");
		$prompt_2->setValue("mi_technique_slug", "foo-mi_technique_slug2");
		$prompt_2->setValue("content", "foo-content2");
		$prompt_2->setValue("other_data", array("foo" => "bar"));
		$prompt_2->setValue("create_date", 4);
		$prompt_2->setValue("delete_date", -1);
		$prompt_2->save();
		$prompt_2->delete();

		// Background 2
		$background_2 = new \Plugin\AIChat\MockClientBackground();
		$background_2->setValue("guid", "foo-background_guid2");
		$background_2->setValue("client_name", "foo-client_name2");
		$background_2->setValue("profile_picture", "foo-profile_picture2");
		$background_2->setValue("background_info", "foo-background_info2");
		$background_2->setValue("other_data", array("foo" => "bar"));
		$background_2->setValue("create_date", 2);
		$background_2->setValue("delete_date", -1);
		$background_2->save();

		// Prompt 3
		$prompt_3 = new \Plugin\AIChat\MockClientPrompt();
		$prompt_3->setValue("guid", "foo-guid3");
		$prompt_3->setValue("background_guid", "foo-background_guid2");
		$prompt_3->setValue("mi_technique_slug", "foo-mi_technique_slug1");
		$prompt_3->setValue("content", "foo-content3");
		$prompt_3->setValue("other_data", array("foo" => "bar"));
		$prompt_3->setValue("create_date", 5);
		$prompt_3->setValue("delete_date", -1);
		$prompt_3->save();

		$prompt = new \Plugin\AIChat\MockClientPrompt();
		$data = $prompt->getByBackgroundGuid("foo-background_guid1", "create_date asc", false);

		$this->assertTrue(isset($data->prompts));
		$this->assertTrue(count($data->prompts) == 1);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 1);
		$this->assertEquals($data->order[0], $prompt_1->getValue("guid"));

		$loaded_prompt_1 = $data->prompts[$data->order[0]];
		$this->assertEquals($loaded_prompt_1["guid"], $prompt_1->getValue("guid"));
		$this->assertEquals($loaded_prompt_1["background_guid"], $prompt_1->getValue("background_guid"));
		$this->assertEquals($loaded_prompt_1["mi_technique_slug"], $prompt_1->getValue("mi_technique_slug"));
		$this->assertEquals($loaded_prompt_1["content"], $prompt_1->getValue("content"));
		$this->assertEquals($loaded_prompt_1["other_data"]->foo, $prompt_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_prompt_1["create_date"], $prompt_1->getValue("create_date"));
		$this->assertEquals($loaded_prompt_1["delete_date"], $prompt_1->getValue("delete_date"));

		$mock_client_background_1 = $loaded_prompt_1["background"];
		$this->assertEquals($mock_client_background_1["guid"], $background_1->getValue("guid"));
		$this->assertEquals($mock_client_background_1["client_name"], $background_1->getValue("client_name"));
		$this->assertEquals($mock_client_background_1["profile_picture"], $background_1->getValue("profile_picture"));
		$this->assertEquals($mock_client_background_1["background_info"], $background_1->getValue("background_info"));
		$this->assertEquals($mock_client_background_1["other_data"]->foo, $background_1->getValue("other_data")["foo"]);
		$this->assertEquals($mock_client_background_1["create_date"], $background_1->getValue("create_date"));
		$this->assertEquals($mock_client_background_1["delete_date"], $background_1->getValue("delete_date"));

		$data = $prompt->getByMITechnique("foo-mi_technique_slug1", "create_date asc", false);

		$this->assertTrue(isset($data->prompts));
		$this->assertTrue(count($data->prompts) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $prompt_1->getValue("guid"));
		$this->assertEquals($data->order[1], $prompt_3->getValue("guid"));

		$loaded_prompt_1 = $data->prompts[$data->order[0]];
		$this->assertEquals($loaded_prompt_1["guid"], $prompt_1->getValue("guid"));
		$this->assertEquals($loaded_prompt_1["background_guid"], $prompt_1->getValue("background_guid"));
		$this->assertEquals($loaded_prompt_1["mi_technique_slug"], $prompt_1->getValue("mi_technique_slug"));
		$this->assertEquals($loaded_prompt_1["content"], $prompt_1->getValue("content"));
		$this->assertEquals($loaded_prompt_1["other_data"]->foo, $prompt_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_prompt_1["create_date"], $prompt_1->getValue("create_date"));
		$this->assertEquals($loaded_prompt_1["delete_date"], $prompt_1->getValue("delete_date"));

		$mock_client_background_1 = $loaded_prompt_1["background"];
		$this->assertEquals($mock_client_background_1["guid"], $background_1->getValue("guid"));
		$this->assertEquals($mock_client_background_1["client_name"], $background_1->getValue("client_name"));
		$this->assertEquals($mock_client_background_1["profile_picture"], $background_1->getValue("profile_picture"));
		$this->assertEquals($mock_client_background_1["background_info"], $background_1->getValue("background_info"));
		$this->assertEquals($mock_client_background_1["other_data"]->foo, $background_1->getValue("other_data")["foo"]);
		$this->assertEquals($mock_client_background_1["create_date"], $background_1->getValue("create_date"));
		$this->assertEquals($mock_client_background_1["delete_date"], $background_1->getValue("delete_date"));

		$loaded_prompt_3 = $data->prompts[$data->order[1]];
		$this->assertEquals($loaded_prompt_3["guid"], $prompt_3->getValue("guid"));
		$this->assertEquals($loaded_prompt_3["background_guid"], $prompt_3->getValue("background_guid"));
		$this->assertEquals($loaded_prompt_3["mi_technique_slug"], $prompt_3->getValue("mi_technique_slug"));
		$this->assertEquals($loaded_prompt_3["content"], $prompt_3->getValue("content"));
		$this->assertEquals($loaded_prompt_3["other_data"]->foo, $prompt_3->getValue("other_data")['foo']);
		$this->assertEquals($loaded_prompt_3["create_date"], $prompt_3->getValue("create_date"));
		$this->assertEquals($loaded_prompt_3["delete_date"], $prompt_3->getValue("delete_date"));

		$mock_client_background_2 = $loaded_prompt_3["background"];
		$this->assertEquals($mock_client_background_2["guid"], $background_2->getValue("guid"));
		$this->assertEquals($mock_client_background_2["client_name"], $background_2->getValue("client_name"));
		$this->assertEquals($mock_client_background_2["profile_picture"], $background_2->getValue("profile_picture"));
		$this->assertEquals($mock_client_background_2["background_info"], $background_2->getValue("background_info"));
		$this->assertEquals($mock_client_background_2["other_data"]->foo, $background_2->getValue("other_data")["foo"]);
		$this->assertEquals($mock_client_background_2["create_date"], $background_2->getValue("create_date"));
		$this->assertEquals($mock_client_background_2["delete_date"], $background_2->getValue("delete_date"));
	}
}
