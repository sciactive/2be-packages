<?php
/**
 * List raffles.
 *
 * @package Components\raffle
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_raffle/listraffles') )
	punt_user(null, pines_url('com_raffle', 'raffle/list'));

$_->com_raffle->list_raffles();