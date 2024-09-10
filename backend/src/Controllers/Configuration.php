<?php
/**
 * Class for handling API functions for working with the AIChat Configuration
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat\Controllers;

use \LC\Http\HttpHelpers as HttpHelpers;

class Configuration
{
	/**
	 * Get configuration for the site
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			Array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function get(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$config_object = (\LC\Factory::me())->build("\Plugin\AIChat\Configuration");

		try {
			$config_object->load();
		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}

		try {
			$data = $config_object->get();
			return HttpHelpers::me()->returnStatus($response, 200, array("configuration" => $data));

		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}
	}

	/**
	 * Save configuration for the site
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface 	$request	 	PSR7 request
	 * @param \Psr\Http\Message\ResponseInterface 		$response		PSR7 response
	 * @param array 									$args			Array of arguments passed to the page
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function save(
		\Psr\Http\Message\ServerRequestInterface $request,
		\Psr\Http\Message\ResponseInterface $response,
		array $args
	): \Psr\Http\Message\ResponseInterface {
		global $app;

		$config_statement = HttpHelpers::me()->getPOST($request, "config_statement", "");

		if ($config_statement == "") {
			return HttpHelpers::me()->returnStatus($response, 400, array("error" => "invalid config_statement"));
		}

		$config_object = (\LC\Factory::me())->build("\Plugin\AIChat\Configuration");
		$config_object->config_statement = $config_statement;

		try {
			$config_object->save();
		} catch (\Exception $error) {
			return HttpHelpers::me()->returnStatus($response, 500, array("error" => $error));
		}

		return HttpHelpers::me()->returnStatus($response, 200);
	}
}