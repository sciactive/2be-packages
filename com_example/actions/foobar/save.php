<?php
/**
 * Save changes to a foobar.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_example/editfoobar') )
		punt_user(null, pines_url('com_example', 'foobar/list'));
	$foobar = com_example_foobar::factory((int) $_REQUEST['id']);
	if (!isset($foobar->guid)) {
		pines_error('Requested foobar id is not accessible.');
		return;
	}
} else {
	if ( !gatekeeper('com_example/newfoobar') )
		punt_user(null, pines_url('com_example', 'foobar/list'));
	$foobar = com_example_foobar::factory();
}

// General
$foobar->name = $_REQUEST['name'];
$foobar->enabled = ($_REQUEST['enabled'] == 'ON');
if (is_callable($_->editor, 'parse_input')) {
	$foobar->description_pesource = $_REQUEST['description'];
	$foobar->description = $_->editor->parse_input($_REQUEST['description']);
} else {
	$foobar->description = $_REQUEST['description'];
}
if (is_callable($_->editor, 'parse_input')) {
	$foobar->short_description_pesource = $_REQUEST['short_description'];
	$foobar->short_description = $_->editor->parse_input($_REQUEST['short_description']);
} else {
	$foobar->short_description = $_REQUEST['short_description'];
}

// Attributes
$foobar->attributes = (array) json_decode($_REQUEST['attributes']);
foreach ($foobar->attributes as &$cur_attribute) {
	$array = array(
		'name' => $cur_attribute->values[0],
		'value' => $cur_attribute->values[1]
	);
	$cur_attribute = $array;
}
unset($cur_attribute);

if (empty($foobar->name)) {
	$foobar->print_form();
	pines_notice('Please specify a name.');
	return;
}
$test = $_->nymph->getEntity(array('class' => com_example_foobar, 'skip_ac' => true), array('&', 'tag' => array('com_example', 'foobar'), 'data' => array('name', $foobar->name)));
if (isset($test) && $test->guid != $_REQUEST['id']) {
	$foobar->print_form();
	pines_notice('There is already a foobar with that name. Please choose a different name.');
	return;
}

if ($_->config->com_example->global_foobars)
	$foobar->ac->other = 1;

if ($foobar->save()) {
	pines_notice('Saved foobar ['.$foobar->name.']');
	$_->com_mailer->send_mail('com_example/save_foobar', array('foobar_name' => h($foobar->name)));
} else {
	pines_error('Error saving foobar. Do you have permission?');
}

pines_redirect(pines_url('com_example', 'foobar/list'));