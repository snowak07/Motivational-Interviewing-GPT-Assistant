<?php
/**
 * Create, retrieve, update, delete functions for an AIChat search document element
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class SearchDocumentElement extends \LC\Generic\Item
{
	/**
	 * Construct the object
	 */
	public function __construct()
	{
		parent::__construct();

		/*
		 * Database where we will be storing the objects
		 */
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$search_document_elements_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "guid", "guid");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"document_guid" => array("type" => "string"),
			"text" => array("type" => "string"),
			"embedding" => array("type" => "string"),
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}
}