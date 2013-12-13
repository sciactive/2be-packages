<?php
/**
 * Provide a form to edit a floor.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	if ( !gatekeeper('com_customertimer/editfloor') )
		punt_user(null, pines_url('com_customertimer', 'editfloor', array('id' => $_REQUEST['id'])));
} else {
	if ( !gatekeeper('com_customertimer/newfloor') )
		punt_user(null, pines_url('com_customertimer', 'editfloor'));
}

$entity = com_customertimer_floor::factory((int) $_REQUEST['id']);
$entity->print_form();