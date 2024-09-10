<?php
/**
 * Create, retrieve, update, delete functions for a mi technique
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class MITechnique extends \LC\Generic\Item
{
	/**
	 * Possible ways to sort the interactions
	 *
	 * @var array
	 */
	private $valid_technique_sql_sortby_values = array(
		"" => "t.create_date ASC",
		"create_date asc" => "t.create_date ASC",
		"create_date desc" => "t.create_date DESC"
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
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$mi_techniques_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "slug", "slug", "", "name");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"name" => array("type" => "string"),
			"definition" => array("type" => "string"),
			"user_instruction" => array("type" => "string"),
			"ai_instruction" => array("type" => "string"),
			"version" => array("type" => "string"),
			"version_slug" => array("type" => "string"),
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}

	/**
	 * Retrieve all mi techniques
	 *
	 * @param string $sort_by			What order to sort the results in
	 * @param bool $show_deleted		Whether or not to returned deleted results
	 *
	 * @return \StdClass
	 */
	public function getTechniques(
		string $sort_by,
		bool $show_deleted
	): \StdClass {
		global $app;

		$data = new \StdClass;
		$data->techniques = array();
		$data->order = array();

		if (!isset($sort_by, $this->valid_technique_sql_sortby_values[$sort_by])) {
			throw (\LC\Factory::me())->build("\Exception", "invalid sort by option");
		}

		$sql = "
			SELECT
				t.*
			FROM
				" . \Plugin\AIChat\Helpers::$mi_techniques_tbl . " t
			WHERE
				(? = '1' OR t.delete_date < 0)
			ORDER BY
				" . $sort_by;
		$params = array($show_deleted);

		$cursor = $app->db->query($sql, $params);
		while ($result = $cursor->fetch(\PDO::FETCH_OBJ)) {
			$technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");
			$technique->buildFromObject($result);

			$data->techniques[$technique->getValue("slug")] = $technique->returnFieldValues();
			$data->order[] = $technique->getValue("slug");
		}
		$cursor->closeCursor();

		return $data;
	}

	/**
	 * Create or update the item in the database
	 *
	 * @return bool
	 */
	public function save(): bool
	{
		// Save new MITechniqueVersion on each new update even if its a brand new technique
		$technique_version = new \Plugin\AIChat\MITechniqueVersion;
		$technique_version->setValue("technique_slug", $this->getValue("slug"));
		$technique_version->setValue("name", $this->getValue("name"));
		$technique_version->setValue("definition", $this->getValue("definition"));
		$technique_version->setValue("user_instruction", $this->getValue("user_instruction"));
		$technique_version->setValue("ai_instruction", $this->getValue("ai_instruction"));
		$technique_version->setValue("version", $this->getValue("version"));
		$technique_version->setValue("other_data", $this->getValue("other_data"));
		$technique_version->setValue("create_date", microtime(true));
		$technique_version->setValue("delete_date", -1);
		$technique_version->save();

		$version_slug = $technique_version->getValue("slug");
		$this->setValue("version_slug", $version_slug);

		return parent::save();
	}
}