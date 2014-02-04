<?php
/**
 * Load the clockin module.
 *
 * @package Components\fortune
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_fortune->show)
	$_->com_fortune->print_fortune();