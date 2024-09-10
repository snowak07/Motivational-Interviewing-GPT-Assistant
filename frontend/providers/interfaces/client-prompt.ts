/**
 * client prompt interface class
 *
 * @copyright Center for Health Enhancement Systems Studies
 */
import { Client } from "../client";
import { Prompt } from "../prompt";

export interface ClientPrompt {
	client: Client,
	prompt: Prompt
}