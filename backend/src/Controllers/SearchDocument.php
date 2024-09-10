<?php
/**
 * Class for handling API functions for working with Search Document objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class SearchDocument
{
	/**
	 * Delete Search Document
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ServerRequestInterface
	 */
	public function delete(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		// Check if search document exists
		$exists = false;
		$search_document = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocument");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $search_document->load($guid);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document"));
		}

		// Delete the search_document
		try {
			$search_document->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single Search Document by its guid
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

		// Check if the search document exists
		$exists = false;
		$search_document = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocument");

		$guid = isset($args["guid"]) ? $args["guid"] : "";

		if ($guid != "") {
			try {
				$exists = $search_document->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("search_document" => $search_document->returnFieldValues()));
	}

	/**
	 * Retrieve a Search Document and all its elements by its guid
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getElements(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		// Check if the search document exists
		$exists = false;
		$search_document = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocument");

		$show_deleted = (strtolower(HttpHelpers::me()->getGET($request, "show_deleted", "false")) == "true");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $search_document->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document"));
		}

		try {
			$data = $search_document->getElements($guid, $show_deleted);

			return HttpHelpers::me()->returnStatus($response, 200, array("elements" => $data->elements, "order" => $data->order, "document" => $search_document->returnFieldValues()));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Restore Search Document
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

		// Check if the search document exists
		$exists = false;
		$search_document = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocument");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $search_document->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document"));
		}

		// Restore the search document
		try {
			$search_document->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an AIChat search document
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
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$search_document = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocument");

		if ($guid != "") {
			try {
				$search_document->load($guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!filter_var($create_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid create_date, must be a float"));
		}

		if (!filter_var($delete_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid delete_date, must be a float"));
		}

		// Save search_document
		try {
			$search_document->setValue("other_data", $other_data);
			$search_document->setValue("create_date", $create_date);
			$search_document->setValue("delete_date", $delete_date);

			$search_document->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("guid" => $search_document->getValue("guid")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}