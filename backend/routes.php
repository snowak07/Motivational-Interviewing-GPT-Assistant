<?php
/**
 * Routes for the plugin
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Routes for the plugin
 * NOTE: Be careful of overwriting app routes
 *
 * $this->routes[] = new \LC\Route(url, callback, methods);
 *
 * General Structure of a Route:
 * 	url: Url of the route after the domain name (string)
 *		http://localhost/labcoat/home => /home
 *
 *  callback: Function to call to process the page (function|string)
 *
 *	methods: HTTP verbs allowed to access the route (if unsure, use "GET" or "POST")
 *
 *  check_access_token: Indicates if the access token should be checked
 *
 *  permissions_array: Array of permissions to check for the user
 *
 * Recommendation: Make routes lowercase and separate words with "-" to make them more friendly for other developers to use.
 */

// Search Documents
$this->routes[] = new \LC\Route("/aichat/document/delete/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocument(), "delete"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/document/get/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocument(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/document/restore/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocument(), "restore"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/document/save", array(new \Plugin\AIChat\Controllers\SearchDocument(), "save"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/document/elements/get/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocument(), "getElements"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/document/element/delete/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), "delete"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/document/element/get/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/document/element/restore/{guid}", array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), "restore"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/document/element/save", array(new \Plugin\AIChat\Controllers\SearchDocumentElement(), "save"), array("POST"), true, array("administrates-ai-assistant"));

// User interactions
$this->routes[] = new \LC\Route("/aichat/interactions/get-user-interactions/{user_guid}", array(new \Plugin\AIChat\Controllers\InteractionCollection(), "getByUserGuid"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/interaction/delete/{guid}", array(new \Plugin\AIChat\Controllers\Interaction(), "delete"), array("POST"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/interaction/get/{guid}", array(new \Plugin\AIChat\Controllers\Interaction(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/interaction/restore/{guid}", array(new \Plugin\AIChat\Controllers\Interaction(), "restore"), array("POST"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/interaction/save", array(new \Plugin\AIChat\Controllers\Interaction(), "save"), array("POST"), true, array("has-ai-assistant-access"));

// Configuration
$this->routes[] = new \LC\Route("/aichat/config/save", array(new \Plugin\AIChat\Controllers\Configuration(), "save"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/config/get", array(new \Plugin\AIChat\Controllers\Configuration(), "get"), array("GET"), true, array("has-ai-assistant-access"));

// Mock Patient Prompts
$this->routes[] = new \LC\Route("/aichat/prompt/delete", array(new \Plugin\AIChat\Controllers\MockClientPrompt(), "delete"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/prompt/get/{guid}", array(new \Plugin\AIChat\Controllers\MockClientPrompt(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/prompt/restore", array(new \Plugin\AIChat\Controllers\MockClientPrompt(), "restore"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/prompt/save", array(new \Plugin\AIChat\Controllers\MockClientPrompt(), "save"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/prompts/background/get/{guid}", array(new \Plugin\AIChat\Controllers\MockClientPrompt(), "getByBackgroundGuid"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/prompts/technique/get", array(new \Plugin\AIChat\Controllers\MockClientPrompt(), "getByMITechnique"), array("GET"), true, array("has-ai-assistant-access"));

// Mock Patient Backgrounds
$this->routes[] = new \LC\Route("/aichat/background/delete", array(new \Plugin\AIChat\Controllers\MockClientBackground(), "delete"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/background/get/{guid}", array(new \Plugin\AIChat\Controllers\MockClientBackground(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/background/restore", array(new \Plugin\AIChat\Controllers\MockClientBackground(), "restore"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/background/save", array(new \Plugin\AIChat\Controllers\MockClientBackground(), "save"), array("POST"), true, array("administrates-ai-assistant"));

// MI Techniques
$this->routes[] = new \LC\Route("/aichat/techniques/get", array(new \Plugin\AIChat\Controllers\MITechnique(), "getTechniques"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/technique/delete/{slug}", array(new \Plugin\AIChat\Controllers\MITechnique(), "delete"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/technique/get/{slug}", array(new \Plugin\AIChat\Controllers\MITechnique(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/technique/restore/{slug}", array(new \Plugin\AIChat\Controllers\MITechnique(), "restore"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/technique/save", array(new \Plugin\AIChat\Controllers\MITechnique(), "save"), array("POST"), true, array("administrates-ai-assistant"));

// MI Technique Versions
$this->routes[] = new \LC\Route("/aichat/technique/versions/get", array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), "getTechniques"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/technique/version/delete/{guid}", array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), "delete"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/technique/version/get/{slug}", array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), "get"), array("GET"), true, array("has-ai-assistant-access"));
$this->routes[] = new \LC\Route("/aichat/technique/version/restore/{slug}", array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), "restore"), array("POST"), true, array("administrates-ai-assistant"));
$this->routes[] = new \LC\Route("/aichat/technique/version/save", array(new \Plugin\AIChat\Controllers\MITechniqueVersion(), "save"), array("POST"), true, array("administrates-ai-assistant"));