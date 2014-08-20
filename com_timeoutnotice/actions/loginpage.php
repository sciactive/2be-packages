<?php
/**
 * Provide the HTML of a login page.
 *
 * @package Components\timeoutnotice
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$login = new module('com_timeoutnotice', 'login');
$loginhtml = $login->render('module_head');
$_->page->ajax($loginhtml, 'text/html');