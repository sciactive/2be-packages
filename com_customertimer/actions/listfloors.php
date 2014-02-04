<?php
/**
 * List floors.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customertimer/listfloors') )
	punt_user(null, pines_url('com_customertimer', 'listfloors'));

$_->com_customertimer->list_floors();