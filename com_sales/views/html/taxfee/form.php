<?php
/**
 * Provides a form for the user to edit a tax/fee.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New Tax/Fee' : 'Editing ['.h($this->entity->name).']';
$this->note = 'Provide tax/fee details in this form.';
?>
<form class="pf-form" method="post" action="<?php e(pines_url('com_sales', 'taxfee/save')); ?>">
	<?php if (isset($this->entity->guid)) { ?>
	<div class="date_info" style="float: right; text-align: right;">
		<?php if (isset($this->entity->user)) { ?>
		<div>User: <span class="date"><?php e("{$this->entity->user->name} [{$this->entity->user->username}]"); ?></span></div>
		<div>Group: <span class="date"><?php e("{$this->entity->group->name} [{$this->entity->group->groupname}]"); ?></span></div>
		<?php } ?>
		<div>Created: <span class="date"><?php e(format_date($this->entity->p_cdate, 'full_short')); ?></span></div>
		<div>Modified: <span class="date"><?php e(format_date($this->entity->p_mdate, 'full_short')); ?></span></div>
	</div>
	<?php } ?>
	<div class="pf-element">
		<label>
			<span class="pf-label">Name</span>
			<input class="pf-field" type="text" name="name" size="24" value="<?php e($this->entity->name); ?>" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Enabled</span>
			<input class="pf-field" type="checkbox" name="enabled" value="ON"<?php echo $this->entity->enabled ? ' checked="checked"' : ''; ?> />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Type</span>
			<span class="pf-note">This determines how the rate is applied to the price of items.</span>
			<select class="pf-field" name="type">
				<option value="percentage"<?php echo $this->entity->type == 'percentage' ? ' selected="selected"' : ''; ?>>Percentage</option>
				<option value="flat_rate"<?php echo $this->entity->type == 'flat_rate' ? ' selected="selected"' : ''; ?>>Flat Rate</option>
			</select>
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Rate</span>
			<span class="pf-note">Enter a percentage (5 for 5%) or a flat rate in dollars (5 for $5).</span>
			<input class="pf-field" type="text" name="rate" size="24" value="<?php e($this->entity->rate); ?>" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Locations</span>
			<span class="pf-note">This tax will be applied to sales by users in these groups.</span>
			<span class="pf-note">Hold Ctrl (Command on Mac) to select multiple groups.</span>
			<select class="pf-field" name="locations[]" multiple="multiple" size="6">
				<?php
				$_->user_manager->group_sort($this->locations, 'name');
				foreach ($this->locations as $cur_group) {
					?><option value="<?php e($cur_group->guid); ?>"<?php echo $cur_group->in_array($this->entity->locations) ? ' selected="selected"' : ''; ?>><?php e(str_repeat('->', $cur_group->get_level())." {$cur_group->name} [{$cur_group->groupname}]"); ?></option><?php
				} ?>
			</select>
		</label>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn" type="button" onclick="pines.get(<?php e(json_encode(pines_url('com_sales', 'taxfee/list'))); ?>);" value="Cancel" />
	</div>
</form>