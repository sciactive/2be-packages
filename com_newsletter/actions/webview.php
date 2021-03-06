<?php
/**
 * View a newsletter without the 2be interface.
 *
 * @package Components\newsletter
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !isset($_REQUEST['mail_id']) ) {
	pines_error("No mail specified!");
	return false;
}

$mail = $_->nymph->getEntity(array(), array('&', 'guid' => (int) $_REQUEST['mail_id'], 'tag' => array('com_newsletter', 'mail')));
if ( !isset($mail) ) {
	pines_error('Invalid mail!');
	return false;
}

$_->page->override = true;

$_->page->override_doc('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>'.$mail->subject.'</title>
</head>

<body>
	'.$mail->message.'
</body>
</html>');