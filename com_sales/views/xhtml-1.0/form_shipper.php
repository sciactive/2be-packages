<?php
/**
 * Provides a form for the user to edit a shipper.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (is_null($this->entity->guid)) ? 'Editing New Shipper' : 'Editing ['.htmlentities($this->entity->name).']';
$this->note = 'Provide shipper details in this form.';
?>
<form class="pform" method="post" id="shipper_details" action="<?php echo pines_url($this->new_option, $this->new_action); ?>">
	<?php if (isset($this->entity->guid)) { ?>
	<div class="date_info" style="float: right; text-align: right;">
			<?php if (isset($this->entity->uid)) { ?>
		<span>Created By: <span class="date"><?php echo $config->user_manager->get_username($this->entity->uid); ?></span></span>
		<br />
			<?php } ?>
		<span>Created On: <span class="date"><?php echo date('Y-m-d', $this->entity->p_cdate); ?></span></span>
		<br />
		<span>Modified On: <span class="date"><?php echo date('Y-m-d', $this->entity->p_mdate); ?></span></span>
	</div>
	<?php } ?>
	<div class="element">
		<label><span class="label">Name</span>
			<input class="field" type="text" name="name" size="20" value="<?php echo $this->entity->name; ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Email</span>
			<input class="field" type="text" name="email" size="20" value="<?php echo $this->entity->email; ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Address 1</span>
			<input class="field" type="text" name="address_1" size="20" value="<?php echo $this->entity->address_1; ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Address 2</span>
			<input class="field" type="text" name="address_2" size="20" value="<?php echo $this->entity->address_2; ?>" /></label>
	</div>
	<div class="element">
		<span class="label">City, State</span>
		<input class="field" type="text" name="city" size="15" value="<?php echo $this->entity->city; ?>" />
		<input class="field" type="text" name="state" size="2" value="<?php echo $this->entity->state; ?>" />
	</div>
	<div class="element">
		<label><span class="label">Zip</span>
			<input class="field" type="text" name="zip" size="20" value="<?php echo $this->entity->zip; ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Corporate Phone</span>
			<input class="field" type="text" name="phone_work" size="20" value="<?php echo $this->entity->phone_work; ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Fax</span>
			<input class="field" type="text" name="fax" size="20" value="<?php echo $this->entity->fax; ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Account #</span>
			<input class="field" type="text" name="account_number" size="20" value="<?php echo $this->entity->account_number; ?>" /></label>
	</div>
	<div class="element full_width">
		<label><span class="label">Terms</span>
			<div class="group">
				<div class="field">
					<textarea rows="3" cols="35" name="terms" style="width: 100%;"><?php echo $this->entity->terms; ?></textarea>
				</div>
			</div></label>
	</div>
	<div class="element full_width">
		<label><span class="label">Comments</span>
			<div class="group">
				<div class="field">
					<textarea rows="3" cols="35" name="comments" style="width: 100%;"><?php echo $this->entity->comments; ?></textarea>
				</div>
			</div></label>
	</div>
	<div class="element buttons">
		<?php if ( !is_null($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php echo $this->entity->guid; ?>" />
		<?php } ?>
		<input class="button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Submit" />
		<input class="button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="window.location='<?php echo pines_url('com_sales', 'listshippers'); ?>';" value="Cancel" />
	</div>
</form>