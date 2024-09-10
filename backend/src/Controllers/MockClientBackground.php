<?php
/**
 * Class for handling API functions for working with Mock Client Background objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class MockClientBackground
{
	/**
	 * Delete Mock Client Background
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

		// Check if mock client background exists
		$exists = false;
		$mock_client_background = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientBackground");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $mock_client_background->load($guid);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mock_client_background"));
		}

		// Delete the mock client background
		try {
			$mock_client_background->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single Mock Client Background by its guid
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

		// Check if mock client background exists
		$exists = false;
		$mock_client_background = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientBackground");

		$guid = isset($args["guid"]) ? $args["guid"] : "";

		if ($guid != "") {
			try {
				$exists = $mock_client_background->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mock_client_background"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("mock_client_background" => $mock_client_background->returnFieldValues()));
	}

	/**
	 * Restore Mock Client Background
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return void
	 */
	public function restore(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		// Check if mock client background exists
		$exists = false;
		$mock_client_background = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientBackground");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $mock_client_background->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mock_client_background"));
		}

		// Restore the mock_client_background
		try {
			$mock_client_background->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an AIChat mock client background
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
		$client_name = HttpHelpers::me()->getPOST($request, "client_name", "");
		$profile_picture = HttpHelpers::me()->getPOST($request, "profile_picture", "");
		$background_info = HttpHelpers::me()->getPOST($request, "background_info", "");
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$mock_client_background = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientBackground");

		if ($guid != "") {
			try {
				$mock_client_background->load($guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		// Validate fields
		if (empty($client_name)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid client_name"));
		}

		if (empty($profile_picture)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid profile_picture"));
		}

		if (empty($background_info)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid background_info"));
		}

		if (!filter_var($create_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid create_date, must be a float"));
		}

		if (!filter_var($delete_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid delete_date, must be a float"));
		}

		// Save Mock Client Background
		try {
			$mock_client_background->setValue("client_name", $client_name);
			$mock_client_background->setValue("profile_picture", $profile_picture);
			$mock_client_background->setValue("background_info", $background_info);
			$mock_client_background->setValue("other_data", $other_data);
			$mock_client_background->setValue("create_date", $create_date);
			$mock_client_background->setValue("delete_date", $delete_date);

			$mock_client_background->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("guid" => $mock_client_background->getValue("guid")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}