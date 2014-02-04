<?php
/**
 * Determine whether to integrate with com_calendar.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_customer->com_calendar && !$_->depend->check('component', 'com_calendar'))
	$_->config->com_customer->com_calendar = false;