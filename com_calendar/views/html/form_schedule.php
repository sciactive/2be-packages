<?php
/**
 * Display a form to edit a work schedule.
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
<script type='text/javascript'>
	$_(function(){
		$("#p_muid_calendar").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true,
			onSelect: function(dateText){
				$("#p_muid_form [name=dates]").ptags_add(dateText);
			}
		});

		var timespan = $("[name=time_start_hour], [name=time_start_minute], [name=time_start_ampm], [name=time_end_hour], [name=time_end_minute], [name=time_end_ampm],", "#p_muid_form");
		$("#p_muid_form [name=all_day]").change(function(){
			if ($(this).is(":checked"))
				timespan.attr("disabled", "disabled");
			else
				timespan.removeAttr("disabled");
		}).change();

		$("#p_muid_form [name=dates]").ptags();
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_calendar', 'saveschedule')); ?>">
	<div class="pf-element">
		<small>Dates and times are calculated using the employee's timezone.
			<?php if (isset($this->entity->guid)) { ?>
			(<?php e($this->entity->get_timezone()); ?>)
			<?php } ?>
		</small>
	</div>
	<div class="pf-element">
		<label><input class="pf-field" type="checkbox" name="all_day" value="ON" />All Day</label>
	</div>
	<div class="pf-element">
		<select name="time_start_hour">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9" selected="selected">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="0">12</option>
		</select>:
		<select name="time_start_minute">
			<option value="0" selected="selected">00</option>
			<option value="15">15</option>
			<option value="30">30</option>
			<option value="45">45</option>
		</select>
		<select name="time_start_ampm">
			<option value="am" selected="selected">AM</option>
			<option value="pm">PM</option>
		</select>
	</div>
	<div class="pf-element">
		<select name="time_end_hour">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5" selected="selected">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="0">12</option>
		</select>:
		<select name="time_end_minute">
			<option value="0" selected="selected">00</option>
			<option value="15">15</option>
			<option value="30">30</option>
			<option value="45">45</option>
		</select>
		<select name="time_end_ampm">
			<option value="am">AM</option>
			<option value="pm" selected="selected">PM</option>
		</select>
	</div>
	<div class="pf-element pf-full-width">
		<span id="p_muid_calendar"></span>
	</div>
	<div class="pf-element pf-full-width">
		<input type="hidden" name="dates" value="" />
	</div>
	<?php if (isset($this->entity->guid)) { ?>
	<input type="hidden" name="employee" value="<?php e($this->entity->guid); ?>" />
	<?php } ?>
</form>