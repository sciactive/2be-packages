<?php
/**
 * Take over the notice functions to log them.
 *
 * @package Components\logger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_logger->log_errors) {
	/**
	 * Log a displayed error.
	 *
	 * @param string &$args The error text.
	 */
	function com_logger__log_error(&$args) {
		global $_;
		$_->log_manager->log($args[0], 'error');
	}
	$_->hook->add_callback('$_->page->error', -100, 'com_logger__log_error');
}

if ($_->config->com_logger->log_notices) {
	/**
	 * Log a displayed notice.
	 *
	 * @param string &$args The notice text.
	 */
	function com_logger__log_notice(&$args) {
		global $_;
		$_->log_manager->log($args[0], 'notice');
	}
	$_->hook->add_callback('$_->page->notice', -100, 'com_logger__log_notice');
}