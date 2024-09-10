<?php
/**
 * Create, retrieve, update, delete functions for an AIChat mock client background
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class MockClientBackground extends \LC\Generic\Item
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
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$mock_client_backgrounds_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "guid", "guid");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"client_name" => array("type" => "string"),
			"profile_picture" => array("type" => "string"),
			"background_info" => array("type" => "string"),
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}
}