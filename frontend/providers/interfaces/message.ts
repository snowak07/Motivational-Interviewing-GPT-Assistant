/**
 * Message object class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
interface Message {
	interaction_guid: string,
	user_type: "user"|"ai",
	content: string,
	create_date: number
}