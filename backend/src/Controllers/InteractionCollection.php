<?php
/**
 * Class for handling API functions for working with Interaction objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;
use \LC\PhpHelpers as PhpHelpers;

class InteractionCollection
{
	/**
	 * Retrieve Interactions by user_guid
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getByUserGuid(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$show_deleted = (strtolower(HttpHelpers::me()->getGET($request, "show_deleted", "false")) == "true");
		$sort_by = HttpHelpers::me()->getGET($request, "sort_by", "create_date asc");

		$page_number = HttpHelpers::me()->getGET($request, "page_num", -1);
		if (!PhpHelpers::me()->isInt($page_number)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid page"));
		}

		$page_number = ((int)$page_number);

		$number_per_page = HttpHelpers::me()->getGET($request, "num_per_page", -1);
		if (!PhpHelpers::me()->isInt($number_per_page)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid num_per_page"));
		}

		$number_per_page = ((int)$number_per_page);

		$client_prompt_guid = HttpHelpers::me()->getGET($request, "client_prompt_guid", "");

		$user_exists = false;
		$user = (\LC\Factory::me())->build("\LC\User");

		$user_guid = isset($args["user_guid"]) ? $args["user_guid"] : "";
		if ($user_guid != "") {
			try {
				$user_exists = $user->load($user_guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$user_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid user"));
			}
		}

		$interaction_collection = (\LC\Factory::me())->build("\Plugin\AIChat\InteractionCollection");

		try {
			$data = $interaction_collection->getByUserGuid($user_guid, $client_prompt_guid, $sort_by, $page_number, $number_per_page, $show_deleted);
			return HttpHelpers::me()->returnStatus($response, 200, array("interactions" => $data->interactions, "order" => $data->order));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}