<?php
/**
 * Lets the user request the verification email again.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->show_title = false;
?>
<style type="text/css">
.ui-pnotify.p_muid_notice {
right: auto;
top: auto;
}
</style>
<script type="text/javascript">
$_(function(){new PNotify({
	type: 'info',
	title: 'Email Verification',
	text: 'You haven\'t verified your email address yet. <a href="javascript:void(0);" class="resend">Request another verification email</a> if you\'re having trouble.',
	hide: false,
	addclass: "p_muid_notice",
	stack: {"dir1": "up", "dir2": "left", "firstpos1": 25, "firstpos2": 25},
	history: {
		history: false
	},
	after_init: function(pnotify){
		pnotify.get().on("click", ".resend", function(){
			$.post(<?php echo json_encode(pines_url('com_user', 'resend_verification')); ?>, function(data){
				if (data) {
					new PNotify({type: "success", title: "Email Sent", text: "The verification email has been resent."});
					pnotify.remove();
				} else
					$_.error("The email could not be sent. Please try again in a few minutes.");
			}, "json");
		});
	}
});});
</script>