<?php
/**
 * Class for handling API functions for working with Interaction objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class Interaction
{
	/**
	 * Delete Interaction
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function delete(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		// Check if interaction exists
		$exists = false;
		$interaction = (\LC\Factory::me())->build("\Plugin\AIChat\Interaction");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $interaction->load($guid);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid interaction"));
		}

		// Delete the interaction
		try {
			$interaction->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single Interaction by its guid
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function get(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		// Check if interaction exists
		$exists = false;
		$interaction = (\LC\Factory::me())->build("\Plugin\AIChat\Interaction");

		$guid = isset($args["guid"]) ? $args["guid"] : "";

		if ($guid != "") {
			try {
				$exists = $interaction->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid interaction"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("interaction" => $interaction->returnFieldValues()));
	}

	/**
	 * Restore Interaction
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function restore(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		// Check if interaction exists
		$exists = false;
		$interaction = (\LC\Factory::me())->build("\Plugin\AIChat\Interaction");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $interaction->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid interaction"));
		}

		// Restore the interaction
		try {
			$interaction->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an AIChat interaction
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function save(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$guid = HttpHelpers::me()->getPOST($request, "guid", "");
		$client_prompt_guid = HttpHelpers::me()->getPOST($request, "client_prompt_guid", "");
		$user_guid = HttpHelpers::me()->getPOST($request, "user_guid", "");
		$user_message = HttpHelpers::me()->getPOST($request, "user_message", "");
		$system_message = HttpHelpers::me()->getPOST($request, "system_message", "");
		$system_response = HttpHelpers::me()->getPOST($request, "system_response", "");
		$system_information = HttpHelpers::me()->getPOST($request, "system_information", "");
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$interaction = (\LC\Factory::me())->build("\Plugin\AIChat\Interaction");

		if ($guid != "") {
			try {
				$interaction->load($guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		$user = (\LC\Factory::me())->build("\LC\User");

		$user_exists = false;
		if ($user_guid != "") {
			try {
				$user_exists = $user->load($user_guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$user_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid user"));
			}

		} else {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid user_guid"));
		}

		// Validate fields
		if (empty($client_prompt_guid)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid client_prompt_guid"));
		}

		if (empty($user_message)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid user_message"));
		}

		if (empty($system_message)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid system_message"));
		}

		if (empty($system_response)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid system_response"));
		}

		if (empty($system_information)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid system_information"));
		}

		if (!filter_var($create_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid create_date, must be a float"));
		}

		if (!filter_var($delete_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid delete_date, must be a float"));
		}

		// Save Interaction
		try {
			$interaction->setValue("client_prompt_guid", $client_prompt_guid);
			$interaction->setValue("user_guid", $user_guid);
			$interaction->setValue("user_message", $user_message);
			$interaction->setValue("system_message", $system_message);
			$interaction->setValue("system_response", $system_response);
			$interaction->setValue("system_information", $system_information);
			$interaction->setValue("other_data", $other_data);
			$interaction->setValue("create_date", $create_date);
			$interaction->setValue("delete_date", $delete_date);

			$interaction->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("guid" => $interaction->getValue("guid")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}
