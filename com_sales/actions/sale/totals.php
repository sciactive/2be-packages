<?php
/**
 * Display sales total page.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/totalsales') )
	punt_user(null, pines_url('com_sales', 'sale/totals'));

$_->com_sales->print_sales_total();