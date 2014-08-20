<?php
/**
 * Save side menu items
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Grey Vugrin <greyvugrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$guid_order = $_REQUEST['guid_order'];
$tag = $_REQUEST['tag'];
$order_array = explode(',', $guid_order);

$result = $_->com_content->save_sidemenu($order_array, $tag);


$_->page->ajax(json_encode($result));
