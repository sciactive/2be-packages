<?php
/**
 * Generate indices.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_repository/makeindices') &&  !gatekeeper('com_repository/makeallindices') )
	punt_user(null, pines_url('com_repository', 'makeindices'));

if ($_REQUEST['all'] == 'true' && gatekeeper('com_repository/makeallindices')) {
	$_->com_repository->make_index_main();
	pines_redirect(pines_url('com_repository', 'listpackages', array('all' => 'true')));
} else {
	$_->com_repository->make_index($_SESSION['user']);
	pines_redirect(pines_url('com_repository', 'listpackages'));
}