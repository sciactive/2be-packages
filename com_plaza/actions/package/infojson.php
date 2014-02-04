<?php
/**
 * Return a JSON object of package info.
 *
 * @package Components\plaza
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_plaza/listpackages') )
	punt_user(null, pines_url('com_plaza', 'package/list'));

$_->page->override = true;
header('Content-Type: application/json');
if ($_REQUEST['local'] == 'true') {
	$package = $_->com_package->db['packages'][$_REQUEST['name']];
} else {
	$index = $_->com_plaza->get_index(null, $_REQUEST['publisher']);
	$package = $index['packages'][$_REQUEST['name']];
}
if (isset($package))
	$_->page->override_doc(json_encode($package));