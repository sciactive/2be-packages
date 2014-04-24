<?php
/**
 * Display a contact form.
 *
 * @package Components\contact
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Contact Us';
?>
<script type="text/javascript">
	$_.com_contact_send_message = function(){
		var name	= $("#p_muid_form [name=author_name]").val();
		var phone	= $("#p_muid_form [name=author_phone]").val();
		var email	= $("#p_muid_form [name=author_email]").val();
		var subject = $("#p_muid_form [name=subject]").val();
		var message = $("#p_muid_form [name=message]").val();
		if (name == '' || phone == '' || email == '' || subject == '' || message == '') {
			alert('Please complete all fields of the form.');
		} else if (phone.length < 14) {
			alert('Please enter a complete phone number.');
		} else if (email.search('@') < 0) {
			alert('Please enter a valid e-mail address.');
		} else {
			$("#p_muid_form").submit();
		}
	};
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_contact', 'sendmessage')); ?>">
	<div class="pf-element">
		<label>
			<span class="pf-label">Name</span>
			<span class="pf-note">Enter your name here.</span>
			<input class="pf-field form-control" type="text" name="author_name" size="24" value="<?php e($_SESSION['user']->name); ?>" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Phone</span>
			<span class="pf-note">Enter your phone number.</span>
			<input class="pf-field form-control" type="tel" name="author_phone" size="24" value="<?php e(format_phone($_SESSION['user']->phone)); ?>"  onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">E-mail</span>
			<span class="pf-note">Enter your e-mail address.</span>
			<input class="pf-field form-control" type="email" name="author_email" size="24" value="<?php e($_SESSION['user']->email); ?>" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Subject</span>
			<input class="pf-field form-control" type="text" name="subject" size="24" value="" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Message</span>
			<span class="pf-note">Enter any questions or comments that you have.</span>
			<textarea class="pf-field" name="message" cols="30" rows="8"></textarea>
		</label>
	</div>
	<div class="pf-element pf-buttons">
		<input type="hidden" name="send_to" value="<?php e($this->send_to); ?>">
		<input class="pf-button btn btn-primary" type="button" value="Send Message" onclick="$_.com_contact_send_message();" />
	</div>
</form>