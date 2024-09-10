/**
 * Search Document Element class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
interface SearchDocumentElement {
	guid: string,
	document_guid: string,
	text: string,
	embedding: Array<number>,
	create_date: number,
	delete_date: number
}