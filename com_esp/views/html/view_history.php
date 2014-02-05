<?php
/**
 * Provides a printable esp form.
 *
 * @package Components\esp
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'ESP History ['.h($this->entity->guid).']';
?>
<form class="pf-form" method="post" action="<?php e(pines_url('com_esp', 'history')); ?>">
	<div class="pf-element pf-heading">
		<h3><?php e($this->entity->customer->name);?></h3>
	</div>
	<?php foreach ($this->entity->history as $cur_history) { ?>
	<div class="pf-element">
		<span class="pf-label"><?php e(format_date($cur_history['date'])); ?></span>
		<span class="pf-note"><?php e($cur_history['user']->name); ?></span>
		<span class="pf-field"><?php e($cur_history['note']); ?></span>
	</div>
	<?php } if (isset($this->entity->claim_info)) { ?>
	<div class="pf-element pf-heading">
		<h3>Accidental Claim Information</h3>
	</div>
	<div class="pf-element">
		<span class="pf-label"><?php e(format_date($this->entity->claim_info['date'])); ?></span>
		<span class="pf-note"><?php e($this->entity->claim_info['user']->name); ?></span>
		<span class="pf-field"><?php e($this->entity->claim_info['note']); ?></span>
	</div>
	<?php } ?>
	<div class="pf-element">
		<span class="pf-label">Note</span>
		<span class="pf-note">Comments or Information</span>
		<textarea class="pf-field" name="history_note"></textarea>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_esp', 'list'))); ?>);" value="Cancel" />
	</div>
</form>