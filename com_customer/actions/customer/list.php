<?php
/**
 * List customers.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customer/listcustomers') )
	punt_user(null, pines_url('com_customer', 'customer/list'));

$_->com_customer->list_customers();