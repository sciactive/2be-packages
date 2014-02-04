<?php
/**
 * Determine whether to integrate with com_sales.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_hrm->com_sales && !$_->depend->check('component', 'com_sales'))
	$_->config->com_hrm->com_sales = false;

if ($_->config->com_hrm->com_calendar && !$_->depend->check('component', 'com_calendar'))
	$_->config->com_hrm->com_calendar = false;

if ($_->config->com_hrm->com_reports && !$_->depend->check('component', 'com_reports'))
	$_->config->com_hrm->com_reports = false;