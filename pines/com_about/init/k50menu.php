<?php
/**
 * Add menu entries.
 *
 * @package Pines
 * @subpackage com_about
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( gatekeeper('com_about/show') )
	$com_about_menu_id = $pines->page->main_menu->add('About', pines_url('com_about'));

?>