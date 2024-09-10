<?php
/**
 * Create, retrieve, update, delete functions for an AIChat mock client prompt
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class MockClientPrompt extends \LC\Generic\Item
{
	/**
	 * Possible ways to sort the interactions
	 *
	 * @var array
	 */
	protected array $valid_prompt_sql_sortby_values = array(
		"" => "p.create_date ASC",
		"create_date asc" => "p.create_date ASC",
		"create_date desc" => "p.create_date DESC"
	);

	/**
	 * Background object associated with the prompt
	 *
	 * @var \Plugin\AIChat\MockClientBackground
	 */
	public ?\Plugin\AIChat\MockClientBackground $background = null;

	/**
	 * Construct the object
	 */
	public function __construct()
	{
		parent::__construct();

		/*
		 * Database where we will be storing the objects
		 */
		$this->setDatabaseTable(\Plugin\AIChat\Helpers::$mock_client_prompts_tbl);

		/*
		 * Create an identifier for the object
		 */
		$identifier = (\LC\Factory::me())->build("\LC\Generic\IdentifierField", "guid", "guid");
		$this->setIdentifier($identifier);

		// Setting fields for the object
		$this->setFieldsUsingArray(array(
			"background_guid" => array("type" => "string"),
			"content" => array("type" => "string"),
			"mi_technique_slug" => array("type" => "string"),
			"other_data" => array("type" => "json", "allow_nulls" => 1)
		));
	}

	/**
	 * Build from a object from another object such as cursor or StdClass
	 *
	 * @param \StdClass $object		Object to build from, must contain all the fields for a normal item
	 *
	 * @return void
	 */
	public function buildFromObject(\StdClass $object): void
	{
		parent::buildFromObject($object);

		$temp_object = new \StdClass;
		if ($object->background_guid != "") {
			$temp_object->guid = $object->background_guid;
		}

		if (isset($object->client_name)) {
			$temp_object->client_name = $object->client_name;
		}

		if (isset($object->profile_picture)) {
			$temp_object->profile_picture = $object->profile_picture;
		}

		if (isset($object->background_info)) {
			$temp_object->background_info = $object->background_info;
		}

		if (isset($object->background_other_data)) {
			$temp_object->other_data = $object->background_other_data;
		}

		if (isset($object->background_create_date)) {
			$temp_object->create_date = $object->background_create_date;
		}

		if (isset($object->background_delete_date)) {
			$temp_object->delete_date = $object->background_delete_date;
		}

		$background = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientBackground");
		$background->buildFromObject($temp_object);
		$this->background = $background;
	}

	/**
	 * Retrieve prompts by background guid
	 *
	 * @param string $background_guid	Identifier of background to get prompts for
	 * @param string $sort_by			What order to sort the results in
	 * @param bool $show_deleted		Whether or not to returned deleted results
	 *
	 * @return \StdClass
	 */
	public function getByBackgroundGuid(
		string $background_guid,
		string $sort_by,
		bool $show_deleted
	): \StdClass {
		global $app;

		if (!isset($background_guid) || $background_guid == "") {
			throw (\LC\Factory::me())->build("\Exception", "invalid background_guid");
		}

		$data = new \StdClass;
		$data->prompts = array();
		$data->order = array();

		if (!isset($sort_by, $this->valid_prompt_sql_sortby_values[$sort_by])) {
			throw (\LC\Factory::me())->build("\Exception", "invalid sort by option");
		}

		$sql = "
			SELECT
				p.*,
				b.client_name,
				b.profile_picture,
				b.background_info,
				b.other_data AS background_other_data,
				b.create_date AS background_create_date,
				b.delete_date AS background_delete_date
			FROM
				" . \Plugin\AIChat\Helpers::$mock_client_prompts_tbl . " p
			LEFT JOIN (
				SELECT * FROM " . \Plugin\AIChat\Helpers::$mock_client_backgrounds_tbl . " ba
				WHERE ba.guid = ? AND ba.delete_date < 0
				ORDER BY ba.create_date DESC
				LIMIT 1
			) b ON p.background_guid = b.guid
			WHERE
				p.background_guid = ? AND
				(? = '1' OR p.delete_date < 0)
			ORDER BY
				" . $sort_by;
		$params = array($background_guid, $background_guid, $show_deleted);

		$cursor = $app->db->query($sql, $params);
		while ($result = $cursor->fetch(\PDO::FETCH_OBJ)) {
			$prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");
			$prompt->buildFromObject($result);

			$data->prompts[$prompt->getValue("guid")] = $prompt->returnFieldValues();
			$data->order[] = $prompt->getValue("guid");
		}
		$cursor->closeCursor();

		return $data;
	}

	/**
	 * Retrieve prompts by mi_technique_slug
	 *
	 * @param string $mi_technique_slug		mi technique to get results for
	 * @param string $sort_by			What order to sort the results in
	 * @param bool $show_deleted		Whether or not to returned deleted results
	 *
	 * @return \StdClass
	 */
	public function getByMITechnique(
		string $mi_technique_slug,
		string $sort_by,
		bool $show_deleted
	): \StdClass {
		global $app;

		if (!isset($mi_technique_slug) || $mi_technique_slug == "") {
			throw (\LC\Factory::me())->build("\Exception", "invalid mi_technique_slug, must load MockClientPrompt first");
		}

		$data = new \StdClass;
		$data->prompts = array();
		$data->order = array();

		if (!isset($sort_by, $this->valid_prompt_sql_sortby_values[$sort_by])) {
			throw (\LC\Factory::me())->build("\Exception", "invalid sort by option");
		}

		$sql = "
			SELECT
				p.*,
				b.client_name,
				b.profile_picture,
				b.background_info,
				b.other_data AS background_other_data,
				b.create_date AS background_create_date,
				b.delete_date AS background_delete_date
			FROM
				" . \Plugin\AIChat\Helpers::$mock_client_prompts_tbl . " p
			LEFT JOIN (
				SELECT * FROM " . \Plugin\AIChat\Helpers::$mock_client_backgrounds_tbl . " ba
				WHERE ba.delete_date < 0
				ORDER BY ba.create_date DESC
			) b ON p.background_guid = b.guid
			WHERE
				p.mi_technique_slug = ? AND
				(? = '1' OR p.delete_date < 0)
			ORDER BY
				" . $sort_by;
		$params = array($mi_technique_slug, $show_deleted);

		$cursor = $app->db->query($sql, $params);
		while ($result = $cursor->fetch(\PDO::FETCH_OBJ)) {
			$prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");
			$prompt->buildFromObject($result);

			$data->prompts[$prompt->getValue("guid")] = $prompt->returnFieldValues();
			$data->order[] = $prompt->getValue("guid");
		}
		$cursor->closeCursor();

		return $data;
	}

	/**
	 * Return the fields and values as a dictionary
	 *
	 * @param mixed $null_replacement_value	Value to use when replacing null values
	 *
	 * @return array
	 */
	public function returnFieldValues(mixed $null_replacement_value = ""): array
	{
		$fields = parent::returnFieldValues($null_replacement_value);

		if (isset($this->background)) {
			$fields["background"] = $this->background->returnFieldValues();
		}

		return $fields;
	}
}