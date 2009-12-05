<?php
/**
 * Test an entity manager for compliance with Pines' entity management
 * system.
 *
 * @package Pines
 * @subpackage com_entity
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * @todo Add tests for custom entity extended classes.
 */

if ( !gatekeeper('system/all') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_entity', 'test', null, false));
	return;
}

$test = new module('com_entity', 'test', 'content');

if (!($config->entity_manager)) {
	$test->error = true;
	return;
}

$entity_start_time = microtime(true);

// Creating entity...
$entity_test = new entity('com_entity', 'test');
$test->tests[0] = (is_null($entity_test->guid));

// Saving entity...
$entity_test->name = "Entity Test ".time();
$entity_test->parent = 0;
$entity_test->test_value = 'test';
$entity_test->wilcard_test = '"quotes" %percents% _underscores_ \'single quotes\' /slashes/ \backslashes\ ;semicolons;';
$test->tests[1] = ($entity_test->save() && !is_null($entity_test->guid));
$entity_guid = $entity_test->guid;

// Checking entity's has_tag method...
$test->tests[2] = ($entity_test->has_tag('com_entity', 'test') && !$entity_test->has_tag('pickles'));

// Retrieving entity by GUID...
$entity_result = new entity;
$entity_result = $config->entity_manager->get_entity($entity_test->guid);
$test->tests[3] = ($entity_result->name == $entity_test->name);
unset($entity_result);

// Testing wrong GUID...
$entity_result = new entity;
$entity_result = $config->entity_manager->get_entity($entity_test->guid + 1);
$test->tests[4] = (empty($entity_result) ? true : ($entity_result->name != $entity_test->name));
unset($entity_result);

// Retrieving entity by GUID and tags...
$entity_result = new entity;
$entity_result = $config->entity_manager->get_entity($entity_test->guid, array('com_entity', 'test'));
$test->tests[5] = ($entity_result->name == $entity_test->name);
unset($entity_result);

// Testing GUID and wrong tags...
$entity_result = new entity;
$entity_result = $config->entity_manager->get_entity($entity_test->guid, array('com_entity', 'pickles'));
$test->tests[6] = empty($entity_result);
unset($entity_result);

// Retrieving entity by parent...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_parent($entity_test->parent);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[7] = ($found_match);
unset($entity_result);

// Testing wrong parent...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_parent(1);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[8] = (!$found_match);
unset($entity_result);

// Retrieving entity by tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags('com_entity', 'test');
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[9] = ($found_match);
unset($entity_result);

// Testing wrong tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags('pickles');
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[10] = (!$found_match);
unset($entity_result);

// Retrieving entity by tags exclusively...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_exclusive('com_entity', 'test');
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[11] = ($found_match);
unset($entity_result);

// Testing wrong exclusive tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_exclusive('pickles');
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[12] = (!$found_match);
unset($entity_result);

// Retrieving entity by tags inclusively...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_inclusive('pickles', 'test', 'barbecue');
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[13] = ($found_match);
unset($entity_result);

// Testing wrong inclusive tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_inclusive('pickles', 'barbecue');
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[14] = (!$found_match);
unset($entity_result);

// Retrieving entity by mixed tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_mixed(array('com_entity'), array('pickles', 'test', 'barbecue'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[15] = ($found_match);
unset($entity_result);

// Testing wrong inclusive mixed tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_mixed(array('com_entity'), array('pickles', 'barbecue'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[16] = (!$found_match);
unset($entity_result);

// Testing wrong exclusive mixed tags...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_tags_mixed(array('pickles'), array('test', 'barbecue'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[17] = (!$found_match);
unset($entity_result);

// Retrieving entity by data...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('test_value' => 'test'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[18] = ($found_match);
unset($entity_result);

// Testing wrong data...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('test_value' => 'pickles'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[19] = (!$found_match);
unset($entity_result);

// Retrieving entity by data wildcards...
$entity_result = array();
$passed_all = true;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '%'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '%'), array('test'), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '______________________________________________________________________________________'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '"%" \%________\% \_underscores\_ \'single quotes\' /%/ \\backslashes\\ ;semicolons;'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all && $found_match;
/* base tests on this:
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '"quotes" %percents% _underscores_ \'single quotes\' /slashes/ \backslashes\ ;semicolons;'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all && $found_match;
 */
$test->tests[20] = ($passed_all);
unset($entity_result);

// Testing wrong data wildcards...
$entity_result = array();
$passed_all = false;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '%z%'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all || $found_match;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '\\\\\\\\'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all || $found_match;
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '%'), array('pickle'), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all || $found_match;
/* base tests on this:
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('wilcard_test' => '"quotes" %percents% _underscores_ \'single quotes\' /slashes/ \backslashes\ ;semicolons;'), array(), true);
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$passed_all = $passed_all || $found_match;
 */
$test->tests[21] = (!$passed_all);
unset($entity_result);

// Retrieving entity by tags and data...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('test_value' => 'test'), array('com_entity', 'test'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[22] = ($found_match);
unset($entity_result);

// Testing wrong tags and right data...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('test_value' => 'test'), array('pickles'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[23] = (!$found_match);
unset($entity_result);

// Testing right tags and wrong data...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('test_value' => 'pickles'), array('com_entity', 'test'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[24] = (!$found_match);
unset($entity_result);

// Testing wrong tags and wrong data...
$entity_result = array();
$found_match = false;
$entity_result = $config->entity_manager->get_entities_by_data(array('test_value' => 'pickles'), array('pickles'));
foreach ($entity_result as $cur_entity) {
	if ($cur_entity->name == $entity_test->name)
		$found_match = true;
}
$test->tests[25] = (!$found_match);
unset($entity_result);

// Testing referenced entities.
$entity_reference_test = new entity('com_entity', 'test');
$entity_reference_test->save();
$entity_reference_guid = $entity_reference_test->guid;
$entity_test->reference = $entity_reference_test;
$entity_test->save();
unset($entity_test);
$entity_reference_test->test = 'good';
$entity_reference_test->save();
unset($entity_reference_test);
$entity_test = $config->entity_manager->get_entity($entity_guid);
$test->tests[26] = ($entity_test->reference->test == 'good');

// Deleting referenced entities.
$test->tests[27] = ($entity_test->reference->delete() && is_null($entity_test->reference->guid));

// Deleting entity...
$test->tests[28] = ($entity_test->delete() && is_null($entity_test->guid));

// Resaving entity...
$test->tests[29] = ($entity_test->save() && !is_null($entity_test->guid));

// Deleting entity by GUID...
$test->tests[30] = ($config->entity_manager->delete_entity_by_id($entity_test->guid));

$test->time = microtime(true) - $entity_start_time;

?>