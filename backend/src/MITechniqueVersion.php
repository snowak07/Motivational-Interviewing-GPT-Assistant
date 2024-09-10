<?php
/**
 * Create, retrieve, update, delete functions for a mi technique version
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class MITechniqueVersion extends \LC\Generic\Item
{
	/**
	 * Possible ways to sort the interactions
	 *
	 * @var array
	 */
	private $valid_technique_version_sql_sortby_values = array(
		"" => "ta.create_date ASC",
		"create_date asc" => "ta.create_date ASC",
		"create_date desc" => "ta.create_date DESC"
	);

	/**
	 * Construct the object
	 */
	public function __construct()
	{
		parent::__construct();

		/*
		 * Database where we will be storing the objects
		 */
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$mi_technique_versions_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "slug", "slug", "", "version");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"technique_slug" => array("type" => "string"),
			"name" => array("type" => "string"),
			"definition" => array("type" => "string"),
			"user_instruction" => array("type" => "string"),
			"ai_instruction" => array("type" => "string"),
			"version" => array("type" => "string"),
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}

	/**
	 * Retrieve all mi technique versions
	 *
	 * @param string $mi_technique_slug     Slug of technique to retrieve versions for
	 * @param string $sort_by			    What order to sort the results in
	 * @param bool $show_deleted		    Whether or not to returned deleted results
	 *
	 * @return \StdClass
	 */
	public function getTechniqueVersions(
		string $mi_technique_slug,
		string $sort_by,
		bool $show_deleted
	): \StdClass {
		global $app;

		$data = new \StdClass;
		$data->technique_versions = array();
		$data->order = array();

		if (!isset($sort_by, $this->valid_technique_version_sql_sortby_values[$sort_by])) {
			throw (\LC\Factory::me())->build("\Exception", "invalid sort by option");
		}

		$params = array($show_deleted);

		$sql = "
			SELECT
				ta.*
			FROM
				" . \Plugin\AIChat\Helpers::$mi_technique_versions_tbl . " ta
			WHERE
				(? = '1' OR ta.delete_date < 0) ";

		if ($mi_technique_slug != "") {
			$sql = $sql . "AND ta.technique_slug = ?";
			array_push($params, $mi_technique_slug);
		}

		$sql = $sql . " ORDER BY " . $sort_by;

		$cursor = $app->db->query($sql, $params);
		while ($result = $cursor->fetch(\PDO::FETCH_OBJ)) {
			$technique_version = (\LC\Factory::me())->build("\Plugin\AIChat\MITechniqueVersion");
			$technique_version->buildFromObject($result);

			$data->technique_versions[$technique_version->getValue("slug")] = $technique_version->returnFieldValues();
			$data->order[] = $technique_version->getValue("slug");
		}
		$cursor->closeCursor();

		return $data;
	}
}