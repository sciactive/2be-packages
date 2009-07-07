<?php
defined('D_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_newsletter/managemails') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", $config->template->url('com_newsletter', 'list', null, false));
	return;
}

if ( isset($_REQUEST['mail_id']) ) {
	$mail = new entity;
	$mail = $config->entity_manager->get_entity($_REQUEST['mail_id']);
	if ( !$mail->has_tag('com_newsletter', 'mail') ) {
		display_error('Invalid mail!');
		return false;
	}
	$mail->delete();
	display_notice("Successfully deleted mail \"".$mail->name."\".");
} else {
	display_error("No mail specified!");
}

com_newsletter::list_mails('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', '');
?>