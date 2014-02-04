<?php
/**
 * Create side menu items
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Grey Vugrin <greyvugrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

$_->page->override = true;
header('Content-Type: application/json');

$tag = $_REQUEST['tag'];
json_decode($tag);

$result = $_->com_content->create_sidemenu($tag);

$_->page->override_doc(json_encode($result));