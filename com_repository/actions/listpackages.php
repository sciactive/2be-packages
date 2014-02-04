<?php
/**
 * List packages.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_repository/listpackages') && !gatekeeper('com_repository/listallpackages') )
	punt_user(null, pines_url('com_repository', 'listpackages'));

if ($_REQUEST['all'] == 'true' && gatekeeper('com_repository/listallpackages')) {
	$_->com_repository->list_packages();
} else {
	$_->com_repository->list_packages($_SESSION['user']);
}