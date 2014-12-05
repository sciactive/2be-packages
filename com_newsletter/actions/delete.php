<?php
/**
 * Delete a newsletter.
 *
 * @package Components\newsletter
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_newsletter/listmail') )
	punt_user(null, pines_url('com_newsletter', 'list'));

$list = explode(',', $_REQUEST['mail_id']);
foreach ($list as $cur_mail) {
	$mail = $_->nymph->getEntity(array(), array('&', 'guid' => (int) $cur_mail, 'tag' => array('com_newsletter', 'mail')));
	if ( !isset($mail) ) {
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_mail;
	}
	$mail->delete();
}
if (empty($failed_deletes)) {
	pines_notice('Selected mail(s) deleted successfully.');
} else {
	pines_error('Could not delete mails with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_newsletter', 'list'));