<?php
/**
 * List loans.
 *
 * @package Components\loan
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_loan/listloans') )
	punt_user(null, pines_url('com_loan', 'loan/list'));

$_->com_loan->list_loans();