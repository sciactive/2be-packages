<?php
/**
 * List employees.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_hrm/listemployees') )
	punt_user(null, pines_url('com_hrm', 'employee/list'));

$employed = ($_REQUEST['employed'] == 'false') ? false : true;
$_->com_hrm->list_employees($employed);