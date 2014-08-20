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
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$tag = $_REQUEST['tag'];
json_decode($tag);

$result = $_->com_content->create_sidemenu($tag);

$_->page->ajax(json_encode($result));