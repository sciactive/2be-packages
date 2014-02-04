<?php
/**
 * Retrieve the required options to send a newsletter.
 *
 * @package Components\newsletter
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_newsletter/send') )
	punt_user(null, pines_url('com_newsletter', 'list'));

$sendprep = new module('com_newsletter', 'sendprep', 'content');

if ( empty($_REQUEST['mail_id']) ) {
	pines_error('Mail ID not valid!');
	return;
}

$mail = $_->entity_manager->get_entity(array(), array('&', 'guid' => (int) $_REQUEST['mail_id'], 'tag' => array('com_newsletter', 'mail')));
if ( !isset($mail) ) {
	pines_error('Invalid mail specified!');
	return;
}

$sendprep->mail = $mail;