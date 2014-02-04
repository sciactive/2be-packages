<?php
/**
 * List customers.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customer/listcompanies') )
	punt_user(null, pines_url('com_customer', 'company/list'));

$_->com_customer->list_companies();