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
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

$_->page->override = true;

$login = new module('com_timeoutnotice', 'login');
$loginhtml = $login->render('module_head');
$_->page->override_doc($loginhtml);