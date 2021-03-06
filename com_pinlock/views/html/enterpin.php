<?php
/**
 * Provide a form for the user to enter their PIN.
 *
 * @package Components\pinlock
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Verify Access';
?>
<script type="text/javascript">
	$_(function(){
		if ($) $("#p_muid_form input[name=pin]").focus();
	});
</script>
<form class="pf-form" id="p_muid_form" method="post" action="<?php e($_SERVER["REQUEST_URI"]); ?>">
	<div class="pf-element">
		<label>
			<span class="pf-label">PIN</span>
			<span class="pf-note">Please enter your PIN to continue.</span>
			<input class="pf-field form-control" type="password" name="pin" size="24" />
		</label>
	</div>
	<div class="pf-element pf-buttons">
		<input type="hidden" name="option" value="<?php e($this->orig_component); ?>" />
		<input type="hidden" name="action" value="<?php e($this->orig_action); ?>" />
		<input type="hidden" name="sessionid" value="<?php e($this->orig_sessionid); ?>" />
		<input type="hidden" name="com_pinlock_continue" value="true" />
		<input class="pf-button btn btn-primary" type="submit" name="submit" value="Continue" />
		<input class="pf-button btn btn-default" type="reset" name="reset" value="Reset" />
	</div>
</form>