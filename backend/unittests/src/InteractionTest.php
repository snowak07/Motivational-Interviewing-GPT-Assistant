<?php
/**
 * Tests the Interaction class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class InteractionTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\Interaction::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\Interaction();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "guid");
		$this->assertEquals($identifier->getName(), "guid");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_interactions}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("guid");
		$temp = $data->getValue("client_prompt_guid");
		$temp = $data->getValue("user_message");
		$temp = $data->getValue("system_message");
		$temp = $data->getValue("system_response");
		$temp = $data->getValue("system_information");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}
}
