<?php
/**
 * Reset the timer.
 *
 * @package Components\timeoutnotice
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( gatekeeper() ) {
	pines_session('write');
	if (
			($_->request_component != 'com_timeoutnotice' || $_->request_action != 'check') &&
			($_->request_component != 'com_messenger' || $_->request_action != 'xmpp_proxy')
		)
		$_SESSION['com_timeoutnotice__last_access'] = time();
	// This stores any custom config value.
	$_SESSION['com_timeoutnotice__timeout'] = $_->config->com_timeoutnotice->timeout;
	pines_session('close');
}