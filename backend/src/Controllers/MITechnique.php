<?php
/**
 * Class for handling API functions for working with MI technique objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class MITechnique
{
	/**
	 * Delete MI Technique
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

		// Check if mi technique exists
		$exists = false;
		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		$slug = isset($args["slug"]) ? $args["slug"] : "";
		if ($slug != "") {
			try {
				$exists = $mi_technique->load($slug);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique"));
		}

		// Delete the mi technique
		try {
			$mi_technique->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single MI Technique by its slug
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

		// Check if mi technique exists
		$exists = false;
		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		$slug = isset($args["slug"]) ? $args["slug"] : "";

		if ($slug != "") {
			try {
				$exists = $mi_technique->load($slug);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("mi_technique" => $mi_technique->returnFieldValues()));
	}

	/**
	 * Retrieve all MI Technique
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getTechniques(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$show_deleted = (strtolower(HttpHelpers::me()->getGET($request, "show_deleted", "false")) == "true");
		$sort_by = HttpHelpers::me()->getGET($request, "sort_by", "create_date asc");

		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		try {
			$data = $mi_technique->getTechniques($sort_by, $show_deleted);
			return HttpHelpers::me()->returnStatus($response, 200, array("techniques" => $data->techniques, "order" => $data->order));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Restore MI Technique
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

		// Check if mi technique exists
		$exists = false;
		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		$slug = isset($args["slug"]) ? $args["slug"] : "";
		if ($slug != "") {
			try {
				$exists = $mi_technique->load($slug);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique"));
		}

		// Restore the mi_technique
		try {
			$mi_technique->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an mi technique
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

		$slug = HttpHelpers::me()->getPOST($request, "slug", "");
		$name = HttpHelpers::me()->getPOST($request, "name", "");
		$definition = HttpHelpers::me()->getPOST($request, "definition", "");
		$user_instruction = HttpHelpers::me()->getPOST($request, "user_instruction", "");
		$ai_instruction = HttpHelpers::me()->getPOST($request, "ai_instruction", "");
		$version = HttpHelpers::me()->getPOST($request, "version", "");
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		if ($slug != "") {
			try {
				$mi_technique->load($slug);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		// Validate fields
		if (empty($name)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid name"));
		}

		if (empty($definition)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid definition"));
		}

		if (empty($user_instruction)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid user_instruction"));
		}

		if (empty($ai_instruction)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid ai_instruction"));
		}

		if (empty($version)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid version"));
		}

		if (!filter_var($create_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid create_date, must be a float"));
		}

		if (!filter_var($delete_date, FILTER_VALIDATE_FLOAT)) {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid delete_date, must be a float"));
		}

		// Save MI Technique
		try {
			$mi_technique->setValue("name", $name);
			$mi_technique->setValue("definition", $definition);
			$mi_technique->setValue("user_instruction", $user_instruction);
			$mi_technique->setValue("ai_instruction", $ai_instruction);
			$mi_technique->setValue("version", $version);
			$mi_technique->setValue("other_data", $other_data);
			$mi_technique->setValue("create_date", $create_date);
			$mi_technique->setValue("delete_date", $delete_date);

			$mi_technique->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("slug" => $mi_technique->getValue("slug")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}