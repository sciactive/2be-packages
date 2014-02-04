<?php
/**
 * Provides a form to apply for employment.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Employment Application ['.h($this->entity->name).']';
?>
<style type="text/css">
	#p_muid_offer .employee_app_style .pf-label, #p_muid_offer .employee_app_style .pf-note {
		font-weight: bold;
		text-align: right;
	}
</style>
<div class="pf-form pf-form-twocol" id="p_muid_offer">
	<div class="pf-element pf-full-width">
		<span class="pf-label">Status:</span>
		<span class="pf-field">
			<strong><?php e(ucwords($this->entity->status)); ?></strong>
		</span>
	</div>
	<?php if (!empty($this->entity->notes)) { ?>
		<div class="pf-element pf-heading">
			<h3>Notes</h3>
		</div>
		<?php foreach ($this->entity->notes as $cur_note) { ?>
		<div class="pf-element pf-full-width">
			<span class="pf-label"><?php e($cur_note['user']->name); ?></span>
			<span class="pf-note"><?php e(format_date($cur_note['date'])); ?></span>
			<span class="pf-field"><?php e($cur_note['note']); ?></span>
		</div>
		<?php }
	} ?>
	<div class="employee_app_style">
		<div class="pf-element pf-heading">
			<h3>Credit Application Setup Information</h3>
		</div>
		<div class="pf-element">
			<span class="pf-label">Name:</span>
			<span class="pf-field"><?php e($this->entity->name); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Email:</span>
			<span class="pf-field">
				<?php e($this->entity->email); ?>
			</span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Phone:</span>
			<span class="pf-field">
				<?php e(format_phone($this->entity->phone)); ?>
			</span>
		</div>
		<div class="pf-element">
			<span class="pf-label">SSN:</span>
			<span class="pf-field">
				<?php e($this->entity->ssn); ?>
			</span>
		</div>
		<div class="pf-element pf-heading">
			<h3>Education</h3>
		</div>
		<?php foreach ($this->entity->education as $cur_school) { ?>
		<div class="pf-element">
			<span class="pf-label">Name of Institution:</span>
			<span class="pf-field"><?php e($cur_school['name'].' ('.$cur_school['type'].')'); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Area of Interest:</span>
			<span class="pf-field"><?php e($cur_school['major']); ?></span>
		</div>
		<br class="pf-clearing" />
		<?php } ?>
		<div class="pf-element pf-heading">
			<h3>Employment History</h3>
		</div>
		<?php foreach ($this->entity->employment as $cur_employer) { ?>
		<div class="pf-element">
			<span class="pf-label">Position:</span>
			<span class="pf-field"><?php e(ucwords($cur_employer['position'])); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Timeline:</span>
			<span class="pf-field"><?php e(format_date($cur_employer['start'], 'date_short')).' - '.h(format_date($cur_employer['end'], 'date_short')); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Company:</span>
			<span class="pf-field"><?php e($cur_employer['company']); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Phone:</span>
			<span class="pf-field"><?php e(format_phone($cur_employer['phone'])); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Reason for Leaving:</span>
			<span class="pf-field"><?php e($cur_employer['reason']); ?></span>
		</div>
		<br class="pf-clearing" />
		<br class="pf-clearing" />
		<?php } ?>
		<div class="pf-element pf-heading">
			<h3>References</h3>
		</div>
		<?php foreach ($this->entity->references as $cur_reference) { ?>
		<div class="pf-element">
			<span class="pf-label">Name:</span>
			<span class="pf-field"><?php e(ucwords($cur_reference['name'])); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Phone:</span>
			<span class="pf-field"><?php e(format_phone($cur_reference['phone'])); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Company:</span>
			<span class="pf-field"><?php e($cur_reference['company']); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Occupation:</span>
			<span class="pf-field"><?php e($cur_reference['occupation']); ?></span>
		</div>
		<br class="pf-clearing" />
		<br class="pf-clearing" />
		<?php } ?>
		<div class="pf-element pf-heading">
			<h3>References</h3>
		</div>
		<div class="pf-element">
			<span class="pf-label">File Location:</span>
			<span class="pf-field"><?php e($this->entity->resume['path']); ?></span>
		</div>
	</div>
</div>