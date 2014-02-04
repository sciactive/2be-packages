<?php
/**
 * Test an entity manager's UID functions.
 *
 * @package Components\entitytools
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_entitytools/test') )
	punt_user(null, pines_url('com_entitytools', 'testuid'));

$name = 'com_entitytools/uid_test_'.time();
$id = $_->entity_manager->new_uid($name);
var_dump($id);
$id = $_->entity_manager->get_uid($name);
var_dump($id);
$_->entity_manager->set_uid($name, 12);
$id = $_->entity_manager->get_uid($name);
var_dump($id);
$id = $_->entity_manager->new_uid($name);
var_dump($id);
$id = $_->entity_manager->new_uid($name.'a');
var_dump($id);
$id = $_->entity_manager->new_uid($name.'b');
var_dump($id);
$_->entity_manager->rename_uid($name, $name.'c');
$id = $_->entity_manager->get_uid($name);
var_dump($id);
$id = $_->entity_manager->get_uid($name.'c');
var_dump($id);

$_->entity_manager->delete_uid($name.'a');
$_->entity_manager->delete_uid($name.'b');
$_->entity_manager->delete_uid($name.'c');

$id = $_->entity_manager->get_uid($name);
var_dump($id);
$id = $_->entity_manager->get_uid($name.'a');
var_dump($id);
$id = $_->entity_manager->get_uid($name.'b');
var_dump($id);
$id = $_->entity_manager->get_uid($name.'c');
var_dump($id);

$_->page->override = true;