<?php
/**
 * Tests the SearchDocumentElement class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\UnitTests;

class SearchDocumentElementTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test __construct with success
	 *
	 * @return void
	 */
	#[CoversFunction(\Plugin\AIChat\SearchDocumentElement::__construct)]
	public function testConstruct(): void
	{
		global $app;

		$data = new \Plugin\AIChat\SearchDocumentElement();
		$identifier = $data->getIdentifier();

		$this->assertEquals($identifier->getType(), "guid");
		$this->assertEquals($identifier->getName(), "guid");
		$this->assertEquals($identifier->getValue(), "");

		$this->assertEquals($data->getDatabaseTable(), "{aichat_search_document_elements}");

		// We check that retrieving values doesn't throw an error
		$temp = $data->getValue("guid");
		$temp = $data->getValue("document_guid");
		$temp = $data->getValue("text");
		$temp = $data->getValue("embedding");
		$temp = $data->getValue("other_data");
		$temp = $data->getValue("create_date");
		$temp = $data->getValue("delete_date");
	}
}
