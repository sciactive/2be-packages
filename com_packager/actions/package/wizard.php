<?php
/**
 * Provide a wizard to create packages.
 *
 * @package Components\packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_packager/newpackage') )
	punt_user(null, pines_url('com_packager', 'package/wizard'));

$_->com_packager->package_wizard();