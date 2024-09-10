<?php
/**
 * Test the MITechnique class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests\Controllers;

class MITechniqueTest extends \PHPUnit\Framework\TestCase
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
	 * @param string 	$test_technique_slug			    MI Technique slug to test with
	 * @param array 	$mock_techniques_return_data		Results when calling the MITechnique class
	 *
	 * @return array
	 */
	public function setUpDefaultMocks($test_technique_slug = "", $mock_techniques_return_data = null): array
	{
		global $app;

		// Mock the techniques and configure it to return fake data
		if (!isset($mock_techniques_return_data)) {
			$mock_techniques_return_data = new \StdClass;
			$mock_techniques_return_data->techniques = array();
			$mock_techniques_return_data->order = array();
		}

		// Mock a MITechnique
		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("delete")
					->willReturn(true);
		$mock_technique->method("load")
					->willReturn(true);
		$mock_technique->method("restore")
					->willReturn(true);
		$mock_technique->method("save")
					->willReturn(true);
		$mock_technique->method("getTechniques")
					->willReturn($mock_techniques_return_data);

		if ($test_technique_slug) {
			$mock_technique->slug = $test_technique_slug;
		}

		// Mock a factory and configure it to build our technique mock
		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturnMap(
								array(
									array('\Plugin\AIChat\MITechnique', $mock_technique)
								)
							);

		\LC\Factory::setMe($mock_factory);

		return array("mock_technique" => $mock_technique, "mock_techniques_return_data" => $mock_techniques_return_data);
	}

	/**
	 * Test delete with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::delete)]
	public function testDeleteSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::delete)]
	public function testDeleteWithError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);
		$mock_technique->method("delete")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'delete'),
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
	 * Test delete with invalid MITechnique
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::delete)]
	public function testDeleteWithInvalidMITechnique(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'delete'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique");
	}

	/**
	 * Test delete with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::delete)]
	public function testDeleteWithLoadError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'delete'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::get)]
	public function testGetSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'get'),
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
	 * Test get with invalid MITechnique
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::get)]
	public function testGetWithInvalidMITechnique(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'get'),
			"GET",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique");
	}

	/**
	 * Test get with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::get)]
	public function testGetWithLoadError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'get'),
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
	 * Test getTechniques with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::getTechniques)]
	public function testGetTechniquesSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'getTechniques'),
			"GET",
			array(),
			array()
		);
		$response = $result["response"];

		$this->assertEquals((string)$response->getStatusCode(), "200");
	}

	/**
	 * Test getTechniques with error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::get)]
	public function testGetTechniquesWithError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("getTechniques")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'getTechniques'),
			"GET",
			array(),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::restore)]
	public function testRestoreSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'restore'),
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
	 * Test restore with invalid MITechnique
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::restore)]
	public function testRestoreWithInvalidMITechnique(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(false);

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'restore'),
			"POST",
			array(),
			array(
				"slug" => "1234"
			)
		);
		$response = $result["response"];

		$response_body = json_decode((string)$response->getBody());
		$this->assertEquals((string)$response->getStatusCode(), "404");
		$this->assertEquals($response_body->error, "invalid mi_technique");
	}

	/**
	 * Test restore with load error
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::restore)]
	public function testRestoreWithLoadError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'restore'),
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::restore)]
	public function testRestoreWithRestoreError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);
		$mock_technique->method("restore")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'restore'),
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
	 * Test save with new MITechnique success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveNewMITechniqueSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "",
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
	 * Test save with update MITechnique success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveUpdateMITechniqueSuccess(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidAIInstruction(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidCreateDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidDefinition(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidDeleteDate(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidName(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	 * Test save with invalid user_instruction
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidUserInstruction(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithInvalidVersion(): void {
		global $app;

		$this->setUpDefaultMocks();

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithLoadError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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
	#[CoversFunction(\Plugin\AIChat\Controllers\MITechnique::save)]
	public function testSaveWithSaveError(): void {
		global $app;

		$mock_technique = $this->createMock(\Plugin\AIChat\MITechnique::class);
		$mock_technique->method("load")
					->willReturn(true);
		$mock_technique->method("save")
					->will($this->throwException(new \LC\CustomException("")));

		$mock_factory = $this->createMock(\LC\Factory::class);
		$mock_factory->method("build")
					->willReturn($mock_technique);

		\LC\Factory::setMe($mock_factory);

		$result = \LC\UnitTests\UnitTestHelpers::executeMockApiRequest(
			array(new \Plugin\AIChat\Controllers\MITechnique(), 'save'),
			"POST",
			array(
				"slug" => "1234",
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