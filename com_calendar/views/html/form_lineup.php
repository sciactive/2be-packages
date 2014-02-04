<?php
/**
 * Display a form to quickly create a company schedule.
 * 
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style type="text/css" >
	#p_muid_lineup .form_select {
		width: 90%;
	}
</style>
<script type='text/javascript'>
	pines(function(){
		$("#p_muid_calendar").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true,
			onSelect: function(dateText){
				$("#p_muid_lineup [name=shifts]").ptags_add(dateText+'|'+$("#p_muid_lineup [name=shift]").val()+'|'+$("#p_muid_lineup [name=employee]").val());
			}
		});

		$("#p_muid_lineup [name=shifts]").ptags();
	});
</script>
<form class="pf-form" method="post" id="p_muid_lineup" action="<?php e(pines_url('com_calendar', 'savelineup')); ?>">
	<div class="pf-element">
		<small>Dates and times are calculated using each employee's timezone.</small>
	</div>
	<div class="pf-element pf-full-width">
		<select class="form_select" name="employee">
			<?php // Load employees for this location.
			foreach ($this->employees as $cur_employee) {
				if (!$cur_employee->in_group($this->location))
					continue;
				echo '<option value="'.h($cur_employee->guid).'">'.h($cur_employee->name).'</option>"';
			} ?>
		</select>
	</div>
	<div class="pf-element pf-full-width">
		<select class="form_select" name="shift">
			<?php foreach ($_->config->com_calendar->lineup_shifts as $cur_shift) {
				$shift = explode('-', $cur_shift);
				$shift_start = format_date(strtotime($shift[0]), 'time_short');
				$shift_end = format_date(strtotime($shift[1]), 'time_short'); ?>
				<option value="<?php e($cur_shift); ?>"><?php e($shift_start).' - '.h($shift_end); ?></option>
			<?php } ?>
		</select>
	</div>
	<div class="pf-element pf-full-width">
		<span id="p_muid_calendar"></span>
	</div>
	<input type="hidden" name="location" value="<?php e($this->location->guid); ?>" />
	<div class="pf-element pf-full-width">
		<input type="hidden" name="shifts" value="" />
	</div>
</form>