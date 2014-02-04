<?php
/**
 * List adjustment types.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_hrm/listadjustments') )
	punt_user(null, pines_url('com_hrm', 'adjustment/list'));

$_->com_hrm->list_adjustments();