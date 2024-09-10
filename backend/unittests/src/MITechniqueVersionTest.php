<?php
/**
 * Tests the MITechniqueVersion class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class MITechniqueVersionTest extends \PHPUnit\Framework\TestCase
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

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$mi_technique_versions_tbl . ";");
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

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$mi_technique_versions_tbl . ";");
	}

	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechniqueVersion::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\MITechniqueVersion();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "slug");
		$this->assertEquals($identifier->getName(), "slug");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_mi_technique_versions}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("slug");
		$temp = $data->getValue("technique_slug");
		$temp = $data->getValue("name");
		$temp = $data->getValue("definition");
		$temp = $data->getValue("user_instruction");
		$temp = $data->getValue("ai_instruction");
		$temp = $data->getValue("version");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}

	/**
	 * Test getTechniqueVersions with an invalid sort by
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechniqueVersion::getTechniqueVersions)]
	public function testGetTechniqueVersionsWithInvalidSortBy(): void
	{
		$this->expectException(\Exception::class);

		$technique = new \Plugin\AIChat\MITechniqueVersion();
		$data = $technique->getTechniqueVersions("foo-technique_slug", "t.delete_date", false);
	}

	/**
	 * Test getTechniqueVersions with no results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechniqueVersion::getTechniqueVersions)]
	public function testGetTechniquesWithNoResults(): void
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

		$technique = new \Plugin\AIChat\MITechniqueVersion();
		$data = $technique->getTechniqueVersions("foo-technique_slug", "create_date asc", true);

		$this->assertTrue(isset($data->technique_versions));
		$this->assertTrue(count($data->technique_versions) == 0);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 0);
	}

	/**
	 * Test getTechniqueVersions with query error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechniqueVersion::getTechniqueVersions)]
	public function testGetTechniquesWithQueryError(): void
	{
		global $app;
		$this->expectException(\Exception::class);

		// Mock the database to throw an error
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->will($this->throwException(new \LC\CustomException("")));
		$app->db = $mock_db;

		$technique = new \Plugin\AIChat\MITechniqueVersion();
		$data = $technique->getTechniqueVersions("foo-technique_slug", "", false);
	}

	/**
	 * Test getTechniqueVersions with two results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechniqueVersion::getTechniqueVersions)]
	public function testGetTechniquesWithTwoResults(): void
	{
		global $app;

		$test_technique_version_1 = array(
			"slug" => "foo-slug1",
			"technique_slug" => "foo-technique_slug1",
			"name" => "foo-name1",
			"definition" => "foo-definition1",
			"user_instruction" => "foo-user_instruction1",
			"ai_instruction" => "foo-ai_instruction1",
			"other_data" => array("foo" => "bar"),
			"create_date" => 1,
			"delete_date" => -1
		);

		$test_technique_version_2 = array(
			"slug" => "foo-slug2",
			"technique_slug" => "foo-technique_slug1",
			"name" => "foo-name2",
			"definition" => "foo-definition2",
			"user_instruction" => "foo-user_instruction2",
			"ai_instruction" => "foo-ai_instruction2",
			"other_data" => array("foo" => "bar"),
			"create_date" => 2,
			"delete_date" => -1
		);

		// Mock the results we want
		$mock_cursor = new \LC\UnitTests\Mocks\MockCursor();
		$mock_cursor->results = array($test_technique_version_1, $test_technique_version_2);

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")->willReturn($mock_cursor);
		$app->db = $mock_db;

		$technique_version = new \Plugin\AIChat\MITechniqueVersion();
		$data = $technique_version->getTechniqueVersions("foo-technique_slug1", "create_date desc", false);

		$this->assertTrue(isset($data->technique_versions));
		$this->assertTrue(count($data->technique_versions) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $test_technique_version_1["slug"]);
		$this->assertEquals($data->order[1], $test_technique_version_2["slug"]);

		$result_technique_version_1 = $data->technique_versions[$test_technique_version_1["slug"]];
		$this->assertEquals($result_technique_version_1["slug"], $test_technique_version_1["slug"]);
		$this->assertEquals($result_technique_version_1["name"], $test_technique_version_1["name"]);
		$this->assertEquals($result_technique_version_1["definition"], $test_technique_version_1["definition"]);
		$this->assertEquals($result_technique_version_1["user_instruction"], $test_technique_version_1["user_instruction"]);
		$this->assertEquals($result_technique_version_1["ai_instruction"], $test_technique_version_1["ai_instruction"]);
		$this->assertEquals($result_technique_version_1["other_data"], $test_technique_version_1["other_data"]);
		$this->assertEquals($result_technique_version_1["create_date"], $test_technique_version_1["create_date"]);
		$this->assertEquals($result_technique_version_1["delete_date"], $test_technique_version_1["delete_date"]);

		$result_technique_version_2 = $data->technique_versions[$test_technique_version_2["slug"]];
		$this->assertEquals($result_technique_version_2["slug"], $test_technique_version_2["slug"]);
		$this->assertEquals($result_technique_version_2["name"], $test_technique_version_2["name"]);
		$this->assertEquals($result_technique_version_2["definition"], $test_technique_version_2["definition"]);
		$this->assertEquals($result_technique_version_2["user_instruction"], $test_technique_version_2["user_instruction"]);
		$this->assertEquals($result_technique_version_2["ai_instruction"], $test_technique_version_2["ai_instruction"]);
		$this->assertEquals($result_technique_version_2["other_data"], $test_technique_version_2["other_data"]);
		$this->assertEquals($result_technique_version_2["create_date"], $test_technique_version_2["create_date"]);
		$this->assertEquals($result_technique_version_2["delete_date"], $test_technique_version_2["delete_date"]);
	}

	/**
	 * Integration Test
	 *
	 * @return void
	 */
	public function testIntegration(): void
	{
		// Technique 1
		$technique_1 = new \Plugin\AIChat\MITechnique();
		$technique_1->setValue("slug", "foo-technique_slug1");
		$technique_1->setValue("name", "foo-name1");
		$technique_1->setValue("definition", "foo-definition1");
		$technique_1->setValue("user_instruction", "foo-user_instruction1");
		$technique_1->setValue("ai_instruction", "foo-ai_instruction1");
		$technique_1->setValue("version", "foo-version2");
		$technique_1->setValue("other_data", array("foo" => "bar"));
		$technique_1->setValue("create_date", 1);
		$technique_1->setValue("delete_date", -1);

		// Technique Version 1
		$technique_version_1 = new \Plugin\AIChat\MITechniqueVersion();
		$technique_version_1->setValue("slug", "foo-slug1");
		$technique_version_1->setValue("technique_slug", "foo-technique_slug1");
		$technique_version_1->setValue("name", "foo-name1");
		$technique_version_1->setValue("definition", "foo-definition1");
		$technique_version_1->setValue("user_instruction", "foo-user_instruction1");
		$technique_version_1->setValue("ai_instruction", "foo-ai_instruction1");
		$technique_version_1->setValue("version", "foo-version1");
		$technique_version_1->setValue("other_data", array("foo" => "bar"));
		$technique_version_1->setValue("create_date", 1);
		$technique_version_1->setValue("delete_date", -1);
		$technique_version_1->save();

		// Technique Version 2
		$technique_version_2 = new \Plugin\AIChat\MITechniqueVersion();
		$technique_version_2->setValue("slug", "foo-slug2");
		$technique_version_2->setValue("technique_slug", "foo-technique_slug1");
		$technique_version_2->setValue("name", "foo-name2");
		$technique_version_2->setValue("definition", "foo-definition2");
		$technique_version_2->setValue("user_instruction", "foo-user_instruction2");
		$technique_version_2->setValue("ai_instruction", "foo-ai_instruction2");
		$technique_version_2->setValue("version", "foo-version2");
		$technique_version_2->setValue("other_data", array("foo" => "bar"));
		$technique_version_2->setValue("create_date", 2);
		$technique_version_2->setValue("delete_date", -1);
		$technique_version_2->save();

		// Technique 3 (Deleted)
		$technique_version_3 = new \Plugin\AIChat\MITechniqueVersion();
		$technique_version_3->setValue("slug", "foo-slug3");
		$technique_version_3->setValue("technique_slug", "foo-technique_slug1");
		$technique_version_3->setValue("name", "foo-name3");
		$technique_version_3->setValue("definition", "foo-definition3");
		$technique_version_3->setValue("user_instruction", "foo-user_instruction3");
		$technique_version_3->setValue("ai_instruction", "foo-ai_instruction3");
		$technique_version_3->setValue("version", "foo-version3");
		$technique_version_3->setValue("other_data", array("foo" => "bar"));
		$technique_version_3->setValue("create_date", 3);
		$technique_version_3->setValue("delete_date", -1);
		$technique_version_3->save();
		$technique_version_3->delete();

		// Get all technique versions
		$technique = new \Plugin\AIChat\MITechniqueVersion();
		$data = $technique->getTechniqueVersions("foo-technique_slug1", "create_date asc", false);

		// Test that correct results were returned
		$this->assertTrue(isset($data->technique_versions));
		$this->assertTrue(count($data->technique_versions) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $technique_version_1->getValue("slug"));
		$this->assertEquals($data->order[1], $technique_version_2->getValue("slug"));

		$loaded_technique_1 = $data->technique_versions[$data->order[0]];
		$this->assertEquals($loaded_technique_1["slug"], $technique_version_1->getValue("slug"));
		$this->assertEquals($loaded_technique_1["name"], $technique_version_1->getValue("name"));
		$this->assertEquals($loaded_technique_1["definition"], $technique_version_1->getValue("definition"));
		$this->assertEquals($loaded_technique_1["user_instruction"], $technique_version_1->getValue("user_instruction"));
		$this->assertEquals($loaded_technique_1["ai_instruction"], $technique_version_1->getValue("ai_instruction"));
		$this->assertEquals($loaded_technique_1["version"], $technique_version_1->getValue("version"));
		$this->assertEquals($loaded_technique_1["other_data"]->foo, $technique_version_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_technique_1["create_date"], $technique_version_1->getValue("create_date"));
		$this->assertEquals($loaded_technique_1["delete_date"], $technique_version_1->getValue("delete_date"));

		$loaded_technique_2 = $data->technique_versions[$data->order[1]];
		$this->assertEquals($loaded_technique_2["slug"], $technique_version_2->getValue("slug"));
		$this->assertEquals($loaded_technique_2["name"], $technique_version_2->getValue("name"));
		$this->assertEquals($loaded_technique_2["definition"], $technique_version_2->getValue("definition"));
		$this->assertEquals($loaded_technique_2["user_instruction"], $technique_version_2->getValue("user_instruction"));
		$this->assertEquals($loaded_technique_2["ai_instruction"], $technique_version_2->getValue("ai_instruction"));
		$this->assertEquals($loaded_technique_2["version"], $technique_version_2->getValue("version"));
		$this->assertEquals($loaded_technique_2["other_data"]->foo, $technique_version_2->getValue("other_data")['foo']);
		$this->assertEquals($loaded_technique_2["create_date"], $technique_version_2->getValue("create_date"));
		$this->assertEquals($loaded_technique_2["delete_date"], $technique_version_2->getValue("delete_date"));
	}
}
