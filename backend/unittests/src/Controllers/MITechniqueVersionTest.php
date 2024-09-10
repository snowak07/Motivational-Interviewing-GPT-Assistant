<?php
/**
 * Test the MITechniqueVersion class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class MITechniqueVersionTest extends \PHPUnit\Framework\TestCase
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
	 * @param string 	$test_technique_version_slug			    MI Technique slug to test with
	 * @param array 	$mock_technique_versions_return_data		Results when calling the MITechniqueVersion class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_technique_version_slug = "", $mock_technique_versions_return_data = null): array
	{
		global $app;

		// Mock the techniques and configure it to return fake data
		if (!isset($mock_technique_versions_return_data)) {
			$mock_technique_versions_return_data = new \StdClass;
			$mock_technique_versions_return_data->technique_versions = array();
			$mock_technique_versions_return_data->order = array();
		}

		// Mock a MITechniqueVersion
		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("delete")
					->willReturn(true);
		$mock_technique_version->method("load")
					->willReturn(true);
		$mock_technique_version->method("restore")
					->willReturn(true);
		$mock_technique_version->method("save")
					->willReturn(true);
		$mock_technique_version->method("getTechniqueVersions")
					->willReturn($mock_technique_versions_return_data);

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);

		if ($test_technique_version_slug) {
			$mock_technique_version->slug = $test_technique_version_slug;
		}

		// Mock a factory and configure it to build our technique mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
						array(
							array('\Plugin\AIChat\MITechniqueVersion', $mock_technique_version),
							array('\Plugin\AIChat\MITechnique', $mock_technique)
						)
					);

		\LC\Factory::setMe($mock_factory);

		return array("mock_technique_version" => $mock_technique_version, "mock_technique_versions_return_data" => $mock_technique_versions_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'delete'),
			"POST",
			array(),
			array(
				"slug" => "1234"
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(true);
		$mock_technique_version->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'delete'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test delete with invalid MITechniqueVersion
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::delete)]
	public function testDeleteWithInvalidMITechniqueVersion(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'delete'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique_version");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'delete'),
			"POST",
			array(),
			array(
				"slug" => "1234"
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'get'),
			"GET",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test get with invalid MITechniqueVersion
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::get)]
	public function testGetWithInvalidMITechniqueVersion(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'get'),
			"GET",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique_version");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'get'),
			"GET",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getTechniqueVersions with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::getTechniqueVersions)]
	public function testGetTechniqueVersionsSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'getTechniqueVersions'),
			"GET",
			array(),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test getTechniqueVersions with error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::get)]
	public function testGetTechniqueVersionsWithError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("getTechniqueVersions")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'getTechniqueVersions'),
			"GET",
			array(),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test getTechniqueVersions with an invalid mi_technique
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::get)]
	public function testGetTechniqueVersionsWithInvalidTechnique(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'getTechniqueVersions'),
			"GET",
			array(
				"technique_slug" => "1234"
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique");
	}

	/**
	 * Test getTechniqueVersions with load technique error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::get)]
	public function testGetTechniqueVersionsWithLoadTechniqueError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'getTechniqueVersions'),
			"GET",
			array(
				"technique_slug" => "1234"
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test restore with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'restore'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test restore with invalid MITechniqueVersion
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::restore)]
	public function testRestoreWithInvalidMITechniqueVersion(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'restore'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique_version");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'restore'),
			"POST",
			array(),
			array(
				"slug" => "1234"
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(true);
		$mock_technique_version->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'restore'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with new MITechniqueVersion success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveNewMITechniqueVersionSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with update MITechniqueVersion success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveUpdateMITechniqueVersionSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test save with invalid ai_instruction
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidAIInstruction(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid ai_instruction");
	}

	/**
	 * Test save with invalid create_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
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
	 * Test save with invalid definition
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidDefinition(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid definition");
	}

	/**
	 * Test save with invalid delete_date
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-defintion",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
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
	 * Test save with Invalid name
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidName(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid name");
	}

	/**
	 * Test save with invalid technique_slug
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidTechniqueSlug(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid technique_slug");
	}

	/**
	 * Test save with invalid user_instruction
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidUserInstruction(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid user_instruction");
	}

	/**
	 * Test save with invalid version
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithInvalidVersion(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "400");
		$this->assertEquals($response_body->error, "invalid version");
	}

	/**
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(true);
		$mock_technique_version->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique_version);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}

	/**
	 * Test save with a technique that does not exist
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithTechniqueDoesNotExist(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "404");
	}

	/**
	 * Test save with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechniqueVersion::save)]
	public function testSaveWithTechniqueLoadError(): void {
		global $app;

		$mock_technique_version = $this->createMock(\Plugin\AIChat\MITechniqueVersion::class);
		$mock_technique_version->method("load")
					->willReturn(true);

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
						array(
							array('\Plugin\AIChat\MITechniqueVersion', $mock_technique_version),
							array('\Plugin\AIChat\MITechnique', $mock_technique)
						)
					);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), 'save'),
			"POST",
			array(
				"slug" => "1234",
				"technique_slug" => "foo-technique_slug",
				"name" => "foo-name",
				"definition" => "foo-definition",
				"user_instruction" => "foo-user_instruction",
				"ai_instruction" => "foo-ai_instruction",
				"version" => "foo-version",
				"other_data" => array()
			),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "500");
	}
}