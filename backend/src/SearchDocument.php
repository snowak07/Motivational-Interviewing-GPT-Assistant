<?php
/**
 * Create, retrieve, update, delete functions for an AIChat search document
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class SearchDocument extends \LC\Generic\Item
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
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$search_documents_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "guid", "guid");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}

	/**
	 * Return a list of search document elements and the search document itself
	 *
	 * @param string	document_guid		Identifier of document to pull elements from
	 * @param bool	show_deleted		Whether to show the deleted elements
	 *
	 * @return \StdClass
	 */
	public function getElements(
		string $document_guid,
		bool $show_deleted = false
	): \StdClass {
		global $app;

		$data = new \StdClass;
		$data->elements = array();
		$data->order = array();

		$sql = "SELECT *
				FROM
					" . \Plugin\AIChat\Helpers::$search_document_elements_tbl . "
				WHERE
					document_guid = ? AND
					(? = '1' OR delete_date < 0)
				ORDER BY create_date ASC";

		$cursor = $app->db->query($sql, array($document_guid, (int)$show_deleted));
		while ($result = $cursor->fetch(\PDO::FETCH_OBJ)) {
			$search_document_element = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocumentElement");
			$search_document_element->buildFromObject($result);

			$data->elements[$search_document_element->getValue("guid")] = $search_document_element->returnFieldValues();
			$data->order[] = $search_document_element->getValue("guid");
		}
		$cursor->closeCursor();

		return $data;
	}
}