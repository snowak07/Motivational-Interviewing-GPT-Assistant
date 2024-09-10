<?php
/**
 * Helpers functions and variables
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
namespace Plugin\AIChat;

class Helpers extends \LC\Generic\Singleton
{
	/**
	 * AIChat Interactions table
	 *
	 * @var string
	 */
	public static string $interactions_tbl = "{aichat_interactions}";

	/**
	 * AIChat MI Technique Versions table
	 *
	 * @var string
	 */
	public static $mi_technique_versions_tbl = "{aichat_mi_technique_versions}";

	/**
	 * AIChat MI Techniques table
	 *
	 * @var string
	 */
	public static $mi_techniques_tbl = "{aichat_mi_techniques}";

	/**
	 * AIChat Mock Client Backgrounds table
	 *
	 * @var string
	 */
	public static string $mock_client_backgrounds_tbl = "{aichat_mock_client_backgrounds}";

	/**
	 * AIChat Mock Patient Backgrounds table
	 * @NOTE kept for backwards compatibility.
	 *
	 * @var string
	 */
	public static string $mock_patient_backgrounds_tbl = "{aichat_mock_patient_backgrounds}";

	/**
	 * AIChat Mock Client Prompts table
	 * @NOTE kept for backwards compatibility.
	 *
	 * @var string
	 */
	public static string $mock_client_prompts_tbl = "{aichat_mock_client_prompts}";

	/**
	 * AIChat Mock Patient Prompts table
	 * @NOTE kept for backwards compatibility.
	 *
	 * @var string
	 */
	public static string $mock_patient_prompts_tbl = "{aichat_mock_patient_prompts}";

	/**
	 * AIChat Search document elements table
	 *
	 * @var string
	 */
	public static string $search_document_elements_tbl = "{aichat_search_document_elements}";

	/**
	 * AIChat Search documents table
	 *
	 * @var string
	 */
	public static string $search_documents_tbl = "{aichat_search_documents}";
}
