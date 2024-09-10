<?php
/**
 * Tests the MockClientBackground class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class MockClientBackgroundTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\MockClientBackground::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\MockClientBackground();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "guid");
		$this->assertEquals($identifier->getName(), "guid");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_mock_client_backgrounds}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("guid");
		$temp = $data->getValue("client_name");
		$temp = $data->getValue("profile_picture");
		$temp = $data->getValue("background_info");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}
}
