<?php
/**
 * Code that runs on a certain schedule
 *)
 * @copyright Center for Health Enhancement Systems Studies
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This file is called every minute when Labcoat runs cron jobs.
 *
 * This uses crontab expressions, for examples:
 *		https://crontab-generator.org/			Very helpful for writing crontab expressions
 *		https://github.com/poliander/cron		Library being used
 */

/*
// EXAMPLE 1: This will run every minute
// Constructs an ExampleData object
$this->addCronJob("construct_data", "* * * * *", (function() {
	$example = new \Plugin\Example\ExampleData("foo", "bar");
}));
*/

// EXAMPLE 2: This will run every 5 minutes
// Saves a row to the database
//$this->addCronJob("save_row", "*/5 * * * *", (function() {
//    $app->db->query("INSERT INTO {example_example_data} (name, value, create_date) VALUES (?, ?, ?);", array("foo", "bar", microtime(true)));
//}));

/*
// EXAMPLE 3: This will run every day at 12:05pm on the 1st of every month
// Do Nothing
$this->addCronJob("do_nothing_1", "5 12 1 * *", (function() {
	// Do nothing
}));
*/

/*
// EXAMPLE 4: This will run every weekday at 4am
// Do Nothing
$this->addCronJob("do_nothing_2", "0 4 * * 1-5", (function() {
	// Do nothing
}));
*/