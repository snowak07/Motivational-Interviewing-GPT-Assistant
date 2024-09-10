<?php
/**
 * Class for handling API functions for working with Search Document Element objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class SearchDocumentElement
{
	/**
	 * Delete Search Document Element
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

		// Check if search document element exists
		$exists = false;
		$search_document_element = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocumentElement");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $search_document_element->load($guid);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document element"));
		}

		// Delete the search_document_element
		try {
			$search_document_element->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single Search Document Element by its guid
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

		// Check if the search document element exists
		$exists = false;
		$search_document_element = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocumentElement");

		$guid = isset($args["guid"]) ? $args["guid"] : "";

		if ($guid != "") {
			try {
				$exists = $search_document_element->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document element"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("search_document_element" => $search_document_element->returnFieldValues()));
	}

	/**
	 * Restore Search Document Element
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

		// Check if the search document element exists
		$exists = false;
		$search_document_element = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocumentElement");

		$guid = isset($args["guid"]) ? $args["guid"] : "";
		if ($guid != "") {
			try {
				$exists = $search_document_element->load($guid);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search document element"));
		}

		// Restore the search document element
		try {
			$search_document_element->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an AIChat search document element
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
		$document_guid = HttpHelpers::me()->getPOST($request, "document_guid", "");
		$text = HttpHelpers::me()->getPOST($request, "text", "");
		$embedding = HttpHelpers::me()->getPOST($request, "embedding", "");
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$search_document_element = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocumentElement");

		if ($guid != "") {
			try {
				$search_document_element->load($guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		$search_document = (\LC\Factory::me())->build("\Plugin\AIChat\SearchDocument");

		$document_exists = false;
		if ($document_guid != "") {
			try {
				$document_exists = $search_document->load($document_guid);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$document_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid search_document"));
			}

		} else {
			try {
				// Save new search_document
				$search_document->setValue("other_data", "");
				$search_document->setValue("create_date", microtime(true));
				$search_document->setValue("delete_date", -1);
				$search_document->save();

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			$document_guid = $search_document->getValue("guid");
		}

		// Validate fields
		if (empty($text)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid text"));
		}

		if (empty($embedding)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid embedding"));
		}

		if (!filter_var($create_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid create_date, must be a float"));
		}

		if (!filter_var($delete_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid delete_date, must be a float"));
		}

		// Save search_document_element
		try {
			$search_document_element->setValue("document_guid", $document_guid);
			$search_document_element->setValue("text", $text);
			$search_document_element->setValue("embedding", $embedding);
			$search_document_element->setValue("other_data", $other_data);
			$search_document_element->setValue("create_date", $create_date);
			$search_document_element->setValue("delete_date", $delete_date);

			$search_document_element->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("guid" => $search_document_element->getValue("guid")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}