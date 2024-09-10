<?php
/**
 * Class for handling API functions for working with MI technique version objects
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class MITechniqueVersion
{
	/**
	 * Delete MI Technique Version
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

		// Check if mi technique version exists
		$exists = false;
		$mi_technique_version = (\LC\Factory::me())->build("\Plugin\AIChat\MITechniqueVersion");

		$slug = isset($args["slug"]) ? $args["slug"] : "";
		if ($slug != "") {
			try {
				$exists = $mi_technique_version->load($slug);

			} catch(\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique_version"));
		}

		// Delete the mi technique version
		try {
			$mi_technique_version->delete();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Retrieve a single MI Technique Version by its slug
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

		// Check if mi technique version exists
		$exists = false;
		$mi_technique_version = (\LC\Factory::me())->build("\Plugin\AIChat\MITechniqueVersion");

		$slug = isset($args["slug"]) ? $args["slug"] : "";

		if ($slug != "") {
			try {
				$exists = $mi_technique_version->load($slug);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique_version"));
		}

		return HttpHelpers::me()->returnStatus($response, 200, array("mi_technique_version" => $mi_technique_version->returnFieldValues()));
	}

	/**
	 * Retrieve all MI Technique Version
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			The array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function getTechniqueVersions(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$show_deleted = (strtolower(HttpHelpers::me()->getGET($request, "show_deleted", "false")) == "true");
		$sort_by = HttpHelpers::me()->getGET($request, "sort_by", "create_date asc");
		$mi_technique_slug = HttpHelpers::me()->getGET($request, "technique_slug", "");

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
		}

		$mi_technique_version = (\LC\Factory::me())->build("\Plugin\AIChat\MITechniqueVersion");

		try {
			$data = $mi_technique_version->getTechniqueVersions($mi_technique_slug, $sort_by, $show_deleted);
			return HttpHelpers::me()->returnStatus($response, 200, array("versions" => $data->versions, "order" => $data->order));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Restore MI Technique Version
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

		// Check if mi technique version exists
		$exists = false;
		$mi_technique_version = (\LC\Factory::me())->build("\Plugin\AIChat\MITechniqueVersion");

		$slug = isset($args["slug"]) ? $args["slug"] : "";
		if ($slug != "") {
			try {
				$exists = $mi_technique_version->load($slug);

			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		if (!$exists) {
			return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique_version"));
		}

		// Restore the mi_technique_version
		try {
			$mi_technique_version->restore();
			return HttpHelpers::me()->returnStatus($response, 200);

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save or update an mi technique version
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
		$technique_slug = HttpHelpers::me()->getPOST($request, "technique_slug", "");
		$name = HttpHelpers::me()->getPOST($request, "name", "");
		$definition = HttpHelpers::me()->getPOST($request, "definition", "");
		$user_instruction = HttpHelpers::me()->getPOST($request, "user_instruction", "");
		$ai_instruction = HttpHelpers::me()->getPOST($request, "ai_instruction", "");
		$version = HttpHelpers::me()->getPOST($request, "version", "");
		$other_data = HttpHelpers::me()->getPOST($request, "other_data", "");
		$create_date = HttpHelpers::me()->getPOST($request, "create_date", microtime(true));
		$delete_date = HttpHelpers::me()->getPOST($request, "delete_date", -1);

		$mi_technique_version = (\LC\Factory::me())->build("\Plugin\AIChat\MITechniqueVersion");

		if ($slug != "") {
			try {
				$mi_technique_version->load($slug);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}
		}

		$mi_technique = (\LC\Factory::me())->build("\Plugin\AIChat\MITechnique");

		$technique_exists = false;
		if ($technique_slug != "") {
			try {
				$technique_exists = $mi_technique->load($technique_slug);
			} catch (\Exception $error) {
				return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
			}

			if (!$technique_exists) {
				return HttpHelpers::me()->returnStatus($response, 404, array("error" => "invalid mi_technique"));
			}

		} else {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid technique_slug"));
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

		// Save MI Technique Version
		try {
			$mi_technique_version->setValue("technique_slug", $technique_slug);
			$mi_technique_version->setValue("name", $name);
			$mi_technique_version->setValue("definition", $definition);
			$mi_technique_version->setValue("user_instruction", $user_instruction);
			$mi_technique_version->setValue("ai_instruction", $ai_instruction);
			$mi_technique_version->setValue("version", $version);
			$mi_technique_version->setValue("other_data", $other_data);
			$mi_technique_version->setValue("create_date", $create_date);
			$mi_technique_version->setValue("delete_date", $delete_date);

			$mi_technique_version->save();

			return HttpHelpers::me()->returnStatus($response, 200, array("slug" => $mi_technique_version->getValue("slug")));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}
}