<?php
/**
 * com_messenger's information.
 *
 * @package Components\messenger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => '2be Messenger',
	'author' => 'SciActive (Component, PChat), Jack Moffitt (Strophe.js)',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'An instant messenger',
	'description' => 'An instant messenger that works within the website. Designed to use an XMPP (Jabber) server. Includes a script to authenticate users for the ejabberd server.',
	'depend' => array(
		'core' => '<3',
		'service' => 'user_manager&icons',
		'component' => 'com_jquery&com_bootstrap&com_pform&com_soundmanager',
	),
);