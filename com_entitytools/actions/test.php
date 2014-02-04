<?php
/**
 * Test an entity manager for compliance with WonderPHP's entity management standard.
 *
 * Add a fourth entry to a test's array to show more detail if it fails. (This
 * entry is var_dump()ed in a dialog.)
 *
 * @package Components\entitytools
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * @todo Add tests for custom entity extended classes.
 */

if ( !gatekeeper('com_entitytools/test') )
	punt_user(null, pines_url('com_entitytools', 'test'));

$test = new module('com_entitytools', 'test', 'content');

if (!($_->entity_manager)) {
	$test->error = true;
	return;
}

$extra_entities = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'))
	);
foreach ($extra_entities as $cur_entity) {
	$cur_entity->delete();
}

// Creating entity...
$entity_test = entity::factory();
$entity_test->add_tag('com_entitytools', 'test');
//$test->tests['create'][0] = (!isset($entity_test->guid));
//$test->tests['create'][1] = microtime(true);
//$test->tests['create'][2] = 'Creating entity...';

// Saving entity...
$entity_test->name = 'Entity Test '.time();
$entity_test->test_null = null;
$entity_test->test_value = 'test';
$entity_test->test_array = array('full', 'of', 'values', 500);
$entity_test->test_match = "Hello, my name is Edward McCheese. It is a pleasure to meet you. As you can see, I have several hats of the most pleasant nature.

This one's email address is nice_hat-wednesday+newyork@im-a-hat.hat.
This one's phone number is (555) 555-1818.
This one's zip code is 92064.";
$entity_test->test_number = 30;
$entity_test->save();
//$test->tests['save'][0] = ($entity_test->save() && isset($entity_test->guid));
//$test->tests['save'][1] = microtime(true);
//$test->tests['save'][2] = 'Saving entity...';
//$test->tests['save'][3] = $entity_test;
$entity_guid = $entity_test->guid;

$entity_reference_test = new entity('com_entitytools', 'test');
$entity_reference_test->test_value = 'wrong';
$entity_reference_test->save();
$entity_reference_guid = $entity_reference_test->guid;
$entity_test->reference = $entity_reference_test;
$entity_test->ref_array = array(0 => array('entity' => $entity_reference_test));
$entity_test->save();
unset($entity_test);
$entity_reference_test->test = 'good';
$entity_reference_test->save();
unset($entity_reference_test);
$entity_test = $_->entity_manager->get_entity($entity_guid);

$entity_start_time = microtime(true);
$test->time_start = $entity_start_time;

// Retrieving entity by GUID...
$entity_result = $_->entity_manager->get_entity($entity_test->guid);
$test->tests['by_guid'][0] = isset($entity_test->guid) && $entity_test->is($entity_result);
$test->tests['by_guid'][1] = microtime(true);
$test->tests['by_guid'][2] = 'Retrieving entity by GUID...';

// Testing wrong GUID...
$entity_result = $_->entity_manager->get_entity($entity_test->guid + 1);
$test->tests['wrong_guid'][0] = (empty($entity_result) ? true : !$entity_test->is($entity_result));
$test->tests['wrong_guid'][1] = microtime(true);
$test->tests['wrong_guid'][2] = 'Testing wrong GUID...';

// Testing entity order, offset, limit...
$entity_result = $_->entity_manager->get_entities(
		array('reverse' => true, 'offset' => 1, 'limit' => 1),
		array('&', 'tag' => array('com_entitytools', 'test'))
	);
$test->tests['options'][0] = $entity_test->is($entity_result[0]);
$test->tests['options'][1] = microtime(true);
$test->tests['options'][2] = 'Testing entity order, offset, limit...';

// Retrieving entity by GUID and tags...
$entity_result = $_->entity_manager->get_entity(
		array(),
		array('&', 'guid' => $entity_test->guid, 'tag' => array('com_entitytools', 'test'))
	);
$test->tests['guid_tags'][0] = $entity_test->is($entity_result);
$test->tests['guid_tags'][1] = microtime(true);
$test->tests['guid_tags'][2] = 'Retrieving entity by GUID and tags...';

// Retrieving entity by !GUID...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', '!guid' => ($entity_test->guid + 1), 'tag' => array('com_entitytools', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['guid_n_'][0] = $found_match;
$test->tests['guid_n_'][1] = microtime(true);
$test->tests['guid_n_'][2] = 'Retrieving entity by !GUID...';

// Retrieving entity by !tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'guid' => $entity_test->guid, '!tag' => array('barbecue', 'pickles'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['tags_n'][0] = $found_match;
$test->tests['tags_n'][1] = microtime(true);
$test->tests['tags_n'][2] = 'Retrieving entity by !tags...';

// Testing GUID and wrong tags...
$entity_result = $_->entity_manager->get_entity(
		array(),
		array('&', 'guid' => $entity_test->guid, 'tag' => array('com_entitytools', 'pickles'))
	);
$test->tests['guid_wr_tags'][0] = empty($entity_result);
$test->tests['guid_wr_tags'][1] = microtime(true);
$test->tests['guid_wr_tags'][2] = 'Testing GUID and wrong tags...';

// Retrieving entity by tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['tags'][0] = $found_match;
$test->tests['tags'][1] = microtime(true);
$test->tests['tags'][2] = 'Retrieving entity by tags...';

// Testing wrong tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'pickles')
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_tags'][0] = !$found_match;
$test->tests['wr_tags'][1] = microtime(true);
$test->tests['wr_tags'][2] = 'Testing wrong tags...';

// Retrieving entity by tags inclusively...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'tag' => array('pickles', 'test', 'barbecue'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['tags_inc'][0] = $found_match;
$test->tests['tags_inc'][1] = microtime(true);
$test->tests['tags_inc'][2] = 'Retrieving entity by tags inclusively...';

// Testing wrong inclusive tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'tag' => array('pickles', 'barbecue'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_tags_inc'][0] = !$found_match;
$test->tests['wr_tags_inc'][1] = microtime(true);
$test->tests['wr_tags_inc'][2] = 'Testing wrong inclusive tags...';

// Retrieving entity by mixed tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'com_entitytools'),
		array('|', 'tag' => array('pickles', 'test', 'barbecue'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['mixed_tags'][0] = $found_match;
$test->tests['mixed_tags'][1] = microtime(true);
$test->tests['mixed_tags'][2] = 'Retrieving entity by mixed tags...';

// Testing wrong inclusive mixed tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'com_entitytools'),
		array('|', 'tag' => array('pickles', 'barbecue'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_inc_mx_tags'][0] = !$found_match;
$test->tests['wr_inc_mx_tags'][1] = microtime(true);
$test->tests['wr_inc_mx_tags'][2] = 'Testing wrong inclusive mixed tags...';

// Testing wrong exclusive mixed tags...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'pickles'),
		array('|', 'tag' => array('test', 'barbecue'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_exc_mx_tags'][0] = !$found_match;
$test->tests['wr_exc_mx_tags'][1] = microtime(true);
$test->tests['wr_exc_mx_tags'][2] = 'Testing wrong exclusive mixed tags...';

// Retrieving entity by isset...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'isset' => array('test_value'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['isset'][0] = $found_match;
$test->tests['isset'][1] = microtime(true);
$test->tests['isset'][2] = 'Retrieving entity by isset...';

// Retrieving entity by !isset...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), '!isset' => array('test_null'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['isset_n'][0] = $found_match;
$test->tests['isset_n'][1] = microtime(true);
$test->tests['isset_n'][2] = 'Retrieving entity by !isset...';

// Retrieving entity by !isset on unset var...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('!&', 'isset' => array('pickles')),
		array('&', 'tag' => array('com_entitytools', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['isset_n'][0] = $found_match;
$test->tests['isset_n'][1] = microtime(true);
$test->tests['isset_n'][2] = 'Retrieving entity by !isset on unset var...';

// Retrieving entity by strict...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'strict' => array('test_value', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['strict'][0] = $found_match;
$test->tests['strict'][1] = microtime(true);
$test->tests['strict'][2] = 'Retrieving entity by strict...';

// Retrieving entity by !strict...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), '!strict' => array('test_value', 'wrong'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity))
		$found_match = true;
	if ($cur_entity->guid == $entity_reference_guid) {
		$found_match = false;
		$test->tests['strict_n'][3] = $cur_entity;
		break;
	}
}
$test->tests['strict_n'][0] = $found_match;
$test->tests['strict_n'][1] = microtime(true);
$test->tests['strict_n'][2] = 'Retrieving entity by !strict...';

// Retrieving entity by data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'data' => array('test_value', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['data'][0] = $found_match;
$test->tests['data'][1] = microtime(true);
$test->tests['data'][2] = 'Retrieving entity by data...';

// Retrieving entity by !data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), '!data' => array('test_value', 'wrong'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity))
		$found_match = true;
	if ($cur_entity->guid == $entity_reference_guid) {
		$found_match = false;
		$test->tests['data_n'][3] = $cur_entity;
		break;
	}
}
$test->tests['data_n'][0] = $found_match;
$test->tests['data_n'][1] = microtime(true);
$test->tests['data_n'][2] = 'Retrieving entity by !data...';

// Retrieving entity by data inclusively...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'data' => array(array('test_value', 'test'), array('test_value', 'pickles')))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['data_i'][0] = $found_match;
$test->tests['data_i'][1] = microtime(true);
$test->tests['data_i'][2] = 'Retrieving entity by data inclusively...';

// Retrieving entity by !data inclusively...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test')),
		array('!|', 'data' => array(array('name', $entity_test->name), array('test_value', 'pickles'))),
		array('|', '!data' => array(array('name', $entity_test->name), array('test_value', 'pickles')))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['data_n_i'][0] = $found_match;
$test->tests['data_n_i'][1] = microtime(true);
$test->tests['data_n_i'][2] = 'Retrieving entity by !data inclusively...';

// Testing wrong data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'data' => array('test_value', 'pickles'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_data'][0] = !$found_match;
$test->tests['wr_data'][1] = microtime(true);
$test->tests['wr_data'][2] = 'Testing wrong data...';

// Retrieving entity by tags and data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'data' => array('test_value', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['tags_data'][0] = $found_match;
$test->tests['tags_data'][1] = microtime(true);
$test->tests['tags_data'][2] = 'Retrieving entity by tags and data...';

// Testing wrong tags and right data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'pickles', 'data' => array('test_value', 'test'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_tags_data'][0] = !$found_match;
$test->tests['wr_tags_data'][1] = microtime(true);
$test->tests['wr_tags_data'][2] = 'Testing wrong tags and right data...';

// Testing right tags and wrong data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'data' => array('test_value', 'pickles'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['tags_wr_data'][0] = !$found_match;
$test->tests['tags_wr_data'][1] = microtime(true);
$test->tests['tags_wr_data'][2] = 'Testing right tags and wrong data...';

// Testing wrong tags and wrong data...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'pickles', 'data' => array('test_value', 'pickles'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_tags_wr_data'][0] = !$found_match;
$test->tests['wr_tags_wr_data'][1] = microtime(true);
$test->tests['wr_tags_wr_data'][2] = 'Testing wrong tags and wrong data...';

// Retrieving entity by array value...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'array' => array('test_array', 'values'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['array'][0] = $found_match;
$test->tests['array'][1] = microtime(true);
$test->tests['array'][2] = 'Retrieving entity by array value...';

// Retrieving entity by !array value...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test')),
		array('!&', 'array' => array('test_array', 'pickles'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['n_array'][0] = $found_match;
$test->tests['n_array'][1] = microtime(true);
$test->tests['n_array'][2] = 'Retrieving entity by !array value...';

// Testing wrong array value...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'array' => array('test_array', 'pickles'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_array'][0] = !$found_match;
$test->tests['wr_array'][1] = microtime(true);
$test->tests['wr_array'][2] = 'Testing wrong array value...';

// Retrieving entity by regex match...
$passed_all = true;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'match' => array('test_match', '/.*/')) // anything
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'match' => array('test_match', '/Edward McCheese/')) // a substring
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test')),
		array('|', 'match' => array(array('test_value', '/\d/'), array('test_match', '/Edward McCheese/'))) // inclusive test
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'match' => array('test_match', '/\b[\w\-+]+@[\w-]+\.\w{2,4}\b/')) // a simple email
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all && $found_match;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'match' => array('test_match', '/\(\d{3}\)\s\d{3}-\d{4}/')) // a phone number
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all && $found_match;
$test->tests['match'][0] = ($passed_all);
$test->tests['match'][1] = microtime(true);
$test->tests['match'][2] = 'Retrieving entity by regex match...';

// Testing wrong regex match...
$passed_all = false;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'match' => array('test_match', '/Q/'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all || $found_match;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => 'pickle', 'match' => array('test_match', '/.*/'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all || $found_match;
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'match' => array(array('test_value', '/\d/'), array('test_match', '/,,/')))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$passed_all = $passed_all || $found_match;
$test->tests['wr_match'][0] = (!$passed_all);
$test->tests['wr_match'][1] = microtime(true);
$test->tests['wr_match'][2] = 'Testing wrong regex match...';

// Retrieving entity by regex + data inclusively...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test')),
		array('|', 'data' => array('test_value', 'pickles'), 'match' => array('test_value', '/test/'))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['match_data_i'][0] = $found_match;
$test->tests['match_data_i'][1] = microtime(true);
$test->tests['match_data_i'][2] = 'Retrieving entity by regex + data inclusively...';

// Retrieving entity by inequality...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'gte' => array(array('test_number', 30), array('pickles', 100)))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ineq'][0] = $found_match;
$test->tests['ineq'][1] = microtime(true);
$test->tests['ineq'][2] = 'Retrieving entity by inequality...';

// Retrieving entity by !inequality...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test')),
		array('!&', 'gte' => array('test_number', 60))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['n_ineq'][0] = $found_match;
$test->tests['n_ineq'][1] = microtime(true);
$test->tests['n_ineq'][2] = 'Retrieving entity by !inequality...';

// Testing wrong inequality...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'lte' => array('test_number', 29.99))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_ineq'][0] = !$found_match;
$test->tests['wr_ineq'][1] = microtime(true);
$test->tests['wr_ineq'][2] = 'Testing wrong inequality...';

// Retrieving entity by time...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'gt' => array('p_cdate', $entity_test->p_cdate - 120))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['time'][0] = $found_match;
$test->tests['time'][1] = microtime(true);
$test->tests['time'][2] = 'Retrieving entity by time...';

// Testing wrong time...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test'), 'gte' => array('p_cdate', $entity_test->p_cdate + 1))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['wr_time'][0] = !$found_match;
$test->tests['wr_time'][1] = microtime(true);
$test->tests['wr_time'][2] = 'Testing wrong time...';

// Testing referenced entities...
$test->tests['ref'][0] = ($entity_test->reference->test == 'good');
$test->tests['ref'][1] = microtime(true);
$test->tests['ref'][2] = 'Testing referenced entities...';

// Testing referenced entity arrays...
$test->tests['ref_array'][0] = ($entity_test->ref_array[0]['entity']->test == 'good');
$test->tests['ref_array'][1] = microtime(true);
$test->tests['ref_array'][2] = 'Testing referenced entity arrays...';

// Retrieving entity by reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'ref' => array('reference', $entity_reference_guid))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_get'][0] = $found_match;
$test->tests['ref_get'][1] = microtime(true);
$test->tests['ref_get'][2] = 'Retrieving entity by reference...';

// Retrieving entity by !reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'tag' => array('com_entitytools', 'test')),
		array('!&', 'ref' => array('reference', $entity_reference_guid + 1))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['n_ref_get'][0] = $found_match;
$test->tests['n_ref_get'][1] = microtime(true);
$test->tests['n_ref_get'][2] = 'Retrieving entity by !reference...';

// Testing wrong reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'ref' => array('reference', $entity_reference_guid + 1))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_wr_get'][0] = !$found_match;
$test->tests['ref_wr_get'][1] = microtime(true);
$test->tests['ref_wr_get'][2] = 'Testing wrong reference...';

// Testing non-existent reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'ref' => array('pickle', $entity_reference_guid))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_ne_get'][0] = !$found_match;
$test->tests['ref_ne_get'][1] = microtime(true);
$test->tests['ref_ne_get'][2] = 'Testing non-existent reference...';

// Retrieving entity by inclusive reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'ref' => array(array('reference', $entity_reference_guid), array('reference', $entity_reference_guid + 1)))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_i_get'][0] = $found_match;
$test->tests['ref_i_get'][1] = microtime(true);
$test->tests['ref_i_get'][2] = 'Retrieving entity by inclusive reference...';

// Testing wrong inclusive reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('|', 'ref' => array(array('reference', $entity_reference_guid + 2), array('reference', $entity_reference_guid + 1)))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_wr_i_get'][0] = !$found_match;
$test->tests['ref_wr_i_get'][1] = microtime(true);
$test->tests['ref_wr_i_get'][2] = 'Testing wrong inclusive reference...';

// Retrieving entity by array reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'ref' => array('ref_array', $entity_reference_guid))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_a_get'][0] = $found_match;
$test->tests['ref_a_get'][1] = microtime(true);
$test->tests['ref_a_get'][2] = 'Retrieving entity by array reference...';

// Testing wrong array reference...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&', 'ref' => array(array('ref_array', $entity_reference_guid), array('ref_array', $entity_reference_guid + 1)))
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['ref_wr_a_get'][0] = !$found_match;
$test->tests['ref_wr_a_get'][1] = microtime(true);
$test->tests['ref_wr_a_get'][2] = 'Testing wrong array reference...';

// Testing logic operations...
$found_match = false;
$entity_result = $_->entity_manager->get_entities(
		array(),
		array('&',
			'!ref' => array(
				array('ref_array', $entity_reference_guid + 1),
				array('ref_array', $entity_reference_guid + 2)
			),
			'!lte' => array('test_number', 29.99)
		),
		array('|',
			'!lte' => array(
				array('test_number', 29.99),
				array('test_number', 30)
			)
		),
		array('!&',
			'!strict' => array('test_value', 'test'),
			'!array' => array(
				array('test_array', 'full'),
				array('test_array', 'of'),
				array('test_array', 'values'),
				array('test_array', 500)
			)
		),
		array('!|',
			'!strict' => array('test_value', 'test'),
			'array' => array(
				array('test_array', 'full'),
				array('test_array', 'of'),
				array('test_array', 'values'),
				array('test_array', 500)
			)
		)
	);
foreach ($entity_result as $cur_entity) {
	if ($entity_test->is($cur_entity)) {
		$found_match = true;
		break;
	}
}
$test->tests['logic'][0] = $found_match;
$test->tests['logic'][1] = microtime(true);
$test->tests['logic'][2] = 'Testing logic operations...';

// Deleting referenced entities...
$test->tests['del_ref'][0] = ($entity_test->reference->delete() && !isset($entity_test->reference->guid));
$test->tests['del_ref'][1] = microtime(true);
$test->tests['del_ref'][2] = 'Deleting referenced entities...';

// Deleting entity...
$test->tests['del'][0] = ($entity_test->delete() && !isset($entity_test->guid));
$test->tests['del'][1] = microtime(true);
$test->tests['del'][2] = 'Deleting entity...';

//// Resaving entity...
//$test->tests['resave'][0] = ($entity_test->save() && isset($entity_test->guid));
//$test->tests['resave'][1] = microtime(true);
//$test->tests['resave'][2] = 'Resaving entity...';
//
//// Deleting entity by GUID...
//// This shouldn't be used in regular code. Instead, call the entity's delete() method.
//$test->tests['del_guid'][0] = ($_->entity_manager->delete_entity_by_id($entity_test->guid));
//$test->tests['del_guid'][1] = microtime(true);
//$test->tests['del_guid'][2] = 'Deleting entity by GUID...';

$test->time_end = microtime(true);