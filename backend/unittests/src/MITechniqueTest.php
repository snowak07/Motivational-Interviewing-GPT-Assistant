<?php
/**
 * Tests the MITechnique class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class MITechniqueTest extends \PHPUnit\Framework\TestCase
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

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$mi_techniques_tbl . ";");
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

		$app->db->query("TRUNCATE TABLE " . \Plugin\AIChat\Helpers::$mi_techniques_tbl . ";");

	}

	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechnique::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\MITechnique();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "slug");
		$this->assertEquals($identifier->getName(), "slug");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_mi_techniques}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("slug");
		$temp = $data->getValue("name");
		$temp = $data->getValue("definition");
		$temp = $data->getValue("user_instruction");
		$temp = $data->getValue("ai_instruction");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}

	/**
	 * Test getTechniques with an invalid sort by
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechnique::getTechniques)]
	public function testGetTechniquesWithInvalidSortBy(): void
	{
		$this->expectException(\Exception::class);

		$technique = new \Plugin\AIChat\MITechnique();
		$data = $technique->getTechniques("t.delete_date", false);
	}

	/**
	 * Test getTechniques with no results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechnique::getTechniques)]
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

		$technique = new \Plugin\AIChat\MITechnique();
		$data = $technique->getTechniques("create_date asc", true);

		$this->assertTrue(isset($data->techniques));
		$this->assertTrue(count($data->techniques) == 0);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 0);
	}

	/**
	 * Test getTechniques with query error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechnique::getTechniques)]
	public function testGetTechniquesWithQueryError(): void
	{
		global $app;
		$this->expectException(\Exception::class);

		// Mock the database to throw an error
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")
				->will($this->throwException(new \LC\CustomException("")));
		$app->db = $mock_db;

		$technique = new \Plugin\AIChat\MITechnique();
		$data = $technique->getTechniques("", false);
	}

	/**
	 * Test getTechniques with two results
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechnique::getTechniques)]
	public function testGetTechniquesWithTwoResults(): void
	{
		global $app;

		$test_technique_1 = array(
			"slug" => "foo-slug1",
			"name" => "foo-name1",
			"definition" => "foo-definition1",
			"user_instruction" => "foo-user_instruction1",
			"ai_instruction" => "foo-ai_instruction1",
			"other_data" => array("foo" => "bar"),
			"create_date" => 1,
			"delete_date" => -1
		);

		$test_technique_2 = array(
			"slug" => "foo-slug2",
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
		$mock_cursor->results = array($test_technique_1, $test_technique_2);

		// Mock the database
		$mock_db = $this->createMock("\LC\Database\DB");
		$mock_db->method("query")->willReturn($mock_cursor);
		$app->db = $mock_db;

		$technique = new \Plugin\AIChat\MITechnique();
		$data = $technique->getTechniques("create_date desc", false);

		$this->assertTrue(isset($data->techniques));
		$this->assertTrue(count($data->techniques) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $test_technique_1["slug"]);
		$this->assertEquals($data->order[1], $test_technique_2["slug"]);

		$result_technique_1 = $data->techniques[$test_technique_1["slug"]];
		$this->assertEquals($result_technique_1["slug"], $test_technique_1["slug"]);
		$this->assertEquals($result_technique_1["name"], $test_technique_1["name"]);
		$this->assertEquals($result_technique_1["definition"], $test_technique_1["definition"]);
		$this->assertEquals($result_technique_1["user_instruction"], $test_technique_1["user_instruction"]);
		$this->assertEquals($result_technique_1["ai_instruction"], $test_technique_1["ai_instruction"]);
		$this->assertEquals($result_technique_1["other_data"], $test_technique_1["other_data"]);
		$this->assertEquals($result_technique_1["create_date"], $test_technique_1["create_date"]);
		$this->assertEquals($result_technique_1["delete_date"], $test_technique_1["delete_date"]);

		$result_technique_2 = $data->techniques[$test_technique_2["slug"]];
		$this->assertEquals($result_technique_2["slug"], $test_technique_2["slug"]);
		$this->assertEquals($result_technique_2["name"], $test_technique_2["name"]);
		$this->assertEquals($result_technique_2["definition"], $test_technique_2["definition"]);
		$this->assertEquals($result_technique_2["user_instruction"], $test_technique_2["user_instruction"]);
		$this->assertEquals($result_technique_2["ai_instruction"], $test_technique_2["ai_instruction"]);
		$this->assertEquals($result_technique_2["other_data"], $test_technique_2["other_data"]);
		$this->assertEquals($result_technique_2["create_date"], $test_technique_2["create_date"]);
		$this->assertEquals($result_technique_2["delete_date"], $test_technique_2["delete_date"]);
	}

	/**
	 * Test save with integration test
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MITechnique::save)]
	public function testSaveIntegration(): void
	{
		global $app;

		$technique = new \Plugin\AIChat\MITechnique();
		$technique->setValue("slug", "foo-slug");
		$technique->setValue("name", "foo-name");
		$technique->setValue("definition", "foo-definition");
		$technique->setValue("user_instruction", "foo-user_instruction");
		$technique->setValue("ai_instruction", "foo-ai_instruction");
		$technique->setValue("version", "foo-version");
		$technique->setValue("version", "foo-version");
		$technique->setValue("other_data", array("foo" => "bar"));
		$data = $technique->save();

		$this->assertTrue($technique->getValue("version_slug") !== "");
		$this->assertTrue(str_contains($technique->getValue("version_slug"), "foo-version"));
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
		$technique_1->setValue("slug", "foo-slug1");
		$technique_1->setValue("name", "foo-name1");
		$technique_1->setValue("definition", "foo-definition1");
		$technique_1->setValue("user_instruction", "foo-user_instruction1");
		$technique_1->setValue("ai_instruction", "foo-ai_instruction1");
		$technique_1->setValue("version", "foo-version1");
		$technique_1->setValue("other_data", array("foo" => "bar"));
		$technique_1->setValue("create_date", 1);
		$technique_1->setValue("delete_date", -1);
		$technique_1->save();

		// Technique 2
		$technique_2 = new \Plugin\AIChat\MITechnique();
		$technique_2->setValue("slug", "foo-slug2");
		$technique_2->setValue("name", "foo-name2");
		$technique_2->setValue("definition", "foo-definition2");
		$technique_2->setValue("user_instruction", "foo-user_instruction2");
		$technique_2->setValue("ai_instruction", "foo-ai_instruction2");
		$technique_2->setValue("version", "foo-version2");
		$technique_2->setValue("other_data", array("foo" => "bar"));
		$technique_2->setValue("create_date", 2);
		$technique_2->setValue("delete_date", -1);
		$technique_2->save();

		// Technique 3 (Deleted)
		$technique_3 = new \Plugin\AIChat\MITechnique();
		$technique_3->setValue("slug", "foo-slug3");
		$technique_3->setValue("name", "foo-name3");
		$technique_3->setValue("definition", "foo-definition3");
		$technique_3->setValue("user_instruction", "foo-user_instruction3");
		$technique_3->setValue("ai_instruction", "foo-ai_instruction3");
		$technique_3->setValue("version", "foo-version3");
		$technique_3->setValue("other_data", array("foo" => "bar"));
		$technique_3->setValue("create_date", 3);
		$technique_3->setValue("delete_date", -1);
		$technique_3->save();
		$technique_3->delete();

		// Get all techniques
		$technique = new \Plugin\AIChat\MITechnique();
		$data = $technique->getTechniques("create_date asc", false);

		// Test that correct results were returned
		$this->assertTrue(isset($data->techniques));
		$this->assertTrue(count($data->techniques) == 2);
		$this->assertTrue(isset($data->order));
		$this->assertTrue(count($data->order) == 2);
		$this->assertEquals($data->order[0], $technique_1->getValue("slug"));
		$this->assertEquals($data->order[1], $technique_2->getValue("slug"));

		$loaded_technique_1 = $data->techniques[$data->order[0]];
		$this->assertEquals($loaded_technique_1["slug"], $technique_1->getValue("slug"));
		$this->assertEquals($loaded_technique_1["name"], $technique_1->getValue("name"));
		$this->assertEquals($loaded_technique_1["definition"], $technique_1->getValue("definition"));
		$this->assertEquals($loaded_technique_1["user_instruction"], $technique_1->getValue("user_instruction"));
		$this->assertEquals($loaded_technique_1["ai_instruction"], $technique_1->getValue("ai_instruction"));
		$this->assertEquals($loaded_technique_1["version"], $technique_1->getValue("version"));
		$this->assertEquals($loaded_technique_1["other_data"]->foo, $technique_1->getValue("other_data")['foo']);
		$this->assertEquals($loaded_technique_1["create_date"], $technique_1->getValue("create_date"));
		$this->assertEquals($loaded_technique_1["delete_date"], $technique_1->getValue("delete_date"));

		$loaded_technique_2 = $data->techniques[$data->order[1]];
		$this->assertEquals($loaded_technique_2["slug"], $technique_2->getValue("slug"));
		$this->assertEquals($loaded_technique_2["name"], $technique_2->getValue("name"));
		$this->assertEquals($loaded_technique_2["definition"], $technique_2->getValue("definition"));
		$this->assertEquals($loaded_technique_2["user_instruction"], $technique_2->getValue("user_instruction"));
		$this->assertEquals($loaded_technique_2["ai_instruction"], $technique_2->getValue("ai_instruction"));
		$this->assertEquals($loaded_technique_2["version"], $technique_2->getValue("version"));
		$this->assertEquals($loaded_technique_2["other_data"]->foo, $technique_2->getValue("other_data")['foo']);
		$this->assertEquals($loaded_technique_2["create_date"], $technique_2->getValue("create_date"));
		$this->assertEquals($loaded_technique_2["delete_date"], $technique_2->getValue("delete_date"));
	}
}
