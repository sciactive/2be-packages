<?php
/**
 * Check the user's remaining session time.
 *
 * @package Components\timeoutnotice
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (isset($_SESSION['com_timeoutnotice__last_access'])) {
	// Print the amount of time remaining in seconds.
	$_->page->ajax(json_encode($_->config->com_timeoutnotice->timeout - (time() - $_SESSION['com_timeoutnotice__last_access'])));
} else {
	$_->page->ajax('false');
}