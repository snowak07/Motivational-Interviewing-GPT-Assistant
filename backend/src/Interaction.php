<?php
/**
 * Create, retrieve, update, delete functions for an AIChat interaction
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class Interaction extends \LC\Generic\Item
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
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$interactions_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "guid", "guid");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"client_prompt_guid" => array("type" => "string"),
			"user_guid" => array("type" => "string"),
			"user_message" => array("type" => "string"),
			"system_message" => array("type" => "string"),
			"system_response" => array("type" => "string"),
			"system_information" => array("type" => "string"),
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}
}