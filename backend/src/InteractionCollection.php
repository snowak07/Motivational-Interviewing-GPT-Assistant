<?php
/**
 * Retrieve functions for an AIChat interaction collection
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class InteractionCollection
{
	/**
	 * Possible ways to sort the interactions
	 *
	 * @var array
	 */
	protected array $valid_interaction_sql_sortby_values = array(
		"" => "i.create_date ASC",
		"create_date asc" => "i.create_date ASC",
		"create_date desc" => "i.create_date DESC"
	);

	/**
	 * Return a list of Interactions with the same user_guid
	 *
	 * @param string $user_guid		Identifier of user to get interactions for
	 * @param string $sort_by		SQL sort by string
	 * @param int $page_num			Page number of interactions to get
	 * @param int $num_per_page 	Number of items to show per page
	 * @param bool $show_deleted
	 *
	 * @return \StdClass
	 */
	public function getByUserGuid(
		string $user_guid,
		string $client_prompt_guid = "",
		string $sort_by = "",
		int $page_num = 0,
		int $num_per_page = -1,
		bool $show_deleted = false
	): \StdClass {
		global $app;

		$data = new \StdClass;
		$data->interactions = array();
		$data->order = array();

		if (!isset($sort_by, $this->valid_interaction_sql_sortby_values[$sort_by])) {
			throw (\LC\Factory::me())->build("\Exception", "invalid sort by option");
		}

		$sort_by = $this->valid_interaction_sql_sortby_values[$sort_by];

		$limit_sql = "";
		if ($num_per_page > 0 && $page_num >= 0) {
			$limit_sql = " LIMIT " . $page_num * $num_per_page . "," . $num_per_page;
		}

		$sql = "
			SELECT
				i.*
			FROM
				" . \Plugin\AIChat\Helpers::$interactions_tbl . " i
			WHERE
				i.user_guid = ? AND
				(? = '1' OR i.delete_date < 0) ";

		$params = array($user_guid, (int)$show_deleted);

		// Add optional where clause
		if ($client_prompt_guid != "") {
			$sql = $sql . " AND i.client_prompt_guid = ? ";
			array_push($params, $client_prompt_guid);
		}

		// Add sort and limit options
		$sql = $sql . " ORDER BY " . $sort_by . $limit_sql;

		$cursor = $app->db->query($sql, $params);
		while ($result = $cursor->fetch(\PDO::FETCH_OBJ)) {
			$interaction = (\LC\Factory::me())->build("\Plugin\AIChat\Interaction");
			$interaction->buildFromObject($result);

			$data->interactions[$interaction->getValue("guid")] = $interaction->returnFieldValues();
			$data->order[] = $interaction->getValue("guid");
		}
		$cursor->closeCursor();

		return $data;
	}
}