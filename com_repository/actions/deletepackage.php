<?php
/**
 * Delete a package.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_repository/deletepackage') && !gatekeeper('com_repository/deleteallpackage') )
	punt_user(null, pines_url('com_repository', 'listpackages'));

$publisher = $_REQUEST['pub'];
$package = $_REQUEST['p'];
$version = $_REQUEST['v'];

if (empty($publisher) || empty($package) || empty($version))
	return;

$user = user::factory($publisher);
if (!isset($user->guid))
	return;
if (!gatekeeper('com_repository/deleteallpackage') && !$user->is($_SESSION['user']))
	return;

$file = clean_filename("{$_->config->com_repository->repository_path}{$user->guid}/{$package}/{$version}/{$package}-{$version}.slm");
$sigfile = clean_filename("{$_->config->com_repository->repository_path}{$user->guid}/{$package}/{$version}/{$package}-{$version}.sig");

if (!file_exists($file)) {
	pines_notice('Package not found. It may have already been removed. Please refresh your index.');
} else {
	if (unlink($file) && (!file_exists($sigfile) || unlink($sigfile))) {
		pines_notice('Selected package deleted successfully. Now you should refresh your index to see the change.');
	} else {
		pines_error('Could not delete package.');
	}
}

pines_redirect(pines_url('com_repository', 'listpackages'));