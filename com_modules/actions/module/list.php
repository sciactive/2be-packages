<?php
/**
 * List modules.
 *
 * @package Components\modules
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_modules/listmodules') )
	punt_user(null, pines_url('com_modules', 'module/list'));

$_->com_modules->list_modules();