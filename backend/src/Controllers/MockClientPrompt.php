<?php
/**
 * Class for handling API functions for working with Mock Client Prompt objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class MockClientPrompt
{
	/**
	 * Delete Mock Client Prompt
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

		// Check if mock client prompt exists
		$exists = false;
		$mock_client_prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $mock_client_prompt->load($guid);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mock_client_prompt"));
		}

		// Delete the mock client prompt
		try {
			$mock_client_prompt->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single Mock Client Prompt by its guid
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

		// Check if mock client prompt exists
		$exists = false;
		$mock_client_prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");

		$guid = isset($args["guid"]) ? $args["guid"] : "";

		if ($guid != "") {
			try {
				$exists = $mock_client_prompt->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mock_client_prompt"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("mock_client_prompt" => $mock_client_prompt->returnFieldValues()));
	}

	/**
	 * Retrieve an array of Mock Client Prompts by a background guid
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getByBackgroundGuid(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$show_deleted = (strtolower(HttpHelpers::me()->getGET($request, "show_deleted", "false")) == "true");
		$sort_by = HttpHelpers::me()->getGET($request, "sort_by", "create_date asc");

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

		$mock_client_prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");

		try {
			$data = $mock_client_prompt->getByBackgroundGuid($guid, $sort_by, $show_deleted);
			return HttpHelpers::me()->returnStatus($response, 200, array("mock_client_background" => $mock_client_background->returnFieldValues(), "prompts" => $data->prompts, "order" => $data->order));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve an array of Mock Client Prompts by their mi_technique_slug
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getByMITechnique(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$show_deleted = (strtolower(HttpHelpers::me()->getGET($request, "show_deleted", "false")) == "true");
		$sort_by = HttpHelpers::me()->getGET($request, "sort_by", "create_date asc");
		$mi_technique_slug = HttpHelpers::me()->getGET($request, "mi_technique_slug", "");

		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		$technique_exists = false;
		if ($mi_technique_slug != "") {
			try {
				$technique_exists = $mi_technique->load($mi_technique_slug);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$technique_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique_slug"));
			}

		} else {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid mi_technique_slug"));
		}

		$mock_client_prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");

		try {
			$data = $mock_client_prompt->getByMITechnique($mi_technique_slug, $sort_by, $show_deleted);
			return HttpHelpers::me()->returnStatus($response, 200, array("prompts" => $data->prompts, "order" => $data->order));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Restore Mock Client Prompt
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

		// Check if mock client prompt exists
		$exists = false;
		$mock_client_prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $mock_client_prompt->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mock_client_prompt"));
		}

		// Restore the mock_client_prompt
		try {
			$mock_client_prompt->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an AIChat mock client prompt
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
		$background_guid = HttpHelpers::me()->getPOST($request, "background_guid", "");
		$mi_technique_slug = HttpHelpers::me()->getPOST($request, "mi_technique_slug", "");
		$content = HttpHelpers::me()->getPOST($request, "content", "");
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$mock_client_prompt = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientPrompt");

		if ($guid != "") {
			try {
				$mock_client_prompt->load($guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		$mock_client_background = (\LC\Factory::me())->build("\Plugin\AIChat\MockClientBackground");

		$background_exists = false;
		if ($background_guid != "") {
			try {
				$background_exists = $mock_client_background->load($background_guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$background_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid background"));
			}

		} else {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid background_guid"));
		}

		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		$technique_exists = false;
		if ($mi_technique_slug != "") {
			try {
				$technique_exists = $mi_technique->load($mi_technique_slug);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$technique_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique"));
			}

		} else {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid mi_technique_slug"));
		}

		// Validate fields
		if (empty($content)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid content"));
		}

		if (!filter_var($create_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid create_date, must be a float"));
		}

		if (!filter_var($delete_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid delete_date, must be a float"));
		}

		// Save Mock Client Prompt
		try {
			$mock_client_prompt->setValue("background_guid", $background_guid);
			$mock_client_prompt->setValue("content", $content);
			$mock_client_prompt->setValue("mi_technique_slug", $mi_technique_slug);
			$mock_client_prompt->setValue("other_data", $other_data);
			$mock_client_prompt->setValue("create_date", $create_date);
			$mock_client_prompt->setValue("delete_date", $delete_date);

			$mock_client_prompt->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("guid" => $mock_client_prompt->getValue("guid")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}