<?php
/**
 * Determine whether to integrate with other components.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_sales->com_customer && !$_->depend->check('component', 'com_customer'))
	$_->config->com_sales->com_customer = false;

if ($_->config->com_sales->com_esp && !$_->depend->check('component', 'com_esp'))
	$_->config->com_sales->com_esp = false;

if ($_->config->com_sales->com_hrm && !$_->depend->check('component', 'com_hrm'))
	$_->config->com_sales->com_hrm = false;

if ($_->config->com_sales->per_item_salesperson && !$_->config->com_sales->com_hrm)
	$_->config->com_sales->per_item_salesperson = false;

if ($_->config->com_sales->com_storefront && !$_->depend->check('component', 'com_storefront&com_content'))
	$_->config->com_sales->com_storefront = false;