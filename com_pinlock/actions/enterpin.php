<?php
/**
 * Require the user to enter their PIN.
 *
 * @package Components\pinlock
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

$module = new module('com_pinlock', 'enterpin', 'content');
$module->orig_component = $_->com_pinlock->component;
$module->orig_action = $_->com_pinlock->action;
$module->orig_sessionid = $_->com_pinlock->sessionid;