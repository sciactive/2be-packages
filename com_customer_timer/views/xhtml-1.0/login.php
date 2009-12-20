<?php
/**
 * Provides a form for the customer to login.
 *
 * @package Pines
 * @subpackage com_customer_timer
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Customer Login';
$this->note = 'Please enter your info, or scan your barcode to login.';
?>
<form class="pform" id="customer_login" name="customer_login" method="post" action="<?php echo pines_url('com_customer_timer', 'login'); ?>">
	<script type="text/javascript">
	// <![CDATA[
	
	// ]]>
	</script>
	<div class="element">
		<label><span class="label">Customer ID</span>
			<input class="field" type="text" name="id" size="20" /></label>
	</div>
	<div class="element">
		<label><span class="label">Password</span>
			<input class="field" type="password" name="password" size="20" /></label>
	</div>
	<div class="element buttons">
		<input class="button ui-state-default ui-priority-primary ui-corner-all" type="submit" name="submit" value="Login" />
		<input class="button ui-state-default ui-priority-secondary ui-corner-all" type="reset" name="reset" value="Reset" />
	</div>
</form>