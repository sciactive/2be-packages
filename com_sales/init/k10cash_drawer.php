<?php
/**
 * Include the cash drawer JavaScript.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_sales->cash_drawer) {
	$com_sales_cash_drawer = new module('com_sales', 'cash_drawer', 'head');
	unset($com_sales_cash_drawer);
}