<?php
/**
 * Lists employee bonuses and provides functions to manipulate them.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Employee Bonuses';
$_->com_pgrid->load();
$_->com_hrm->load_employee_select();
?>
<script type="text/javascript">

	pines(function(){
		var bonus_id;
		var bonus_dialog = $("#p_muid_bonus_dialog");

		$("#p_muid_bonus_dialog [name=effective_date]").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true
		});

		bonus_dialog.find("form").submit(function(){
			bonus_dialog.dialog('option', 'buttons').Save();
			return false;
		});
		bonus_dialog.dialog({
			autoOpen: false,
			modal: true,
			width: 300,
			buttons: {
				'Save': function(){
					pines.post(<?php echo json_encode(pines_url('com_hrm', 'bonus/save')); ?>, {
						id: bonus_id,
						name: $("#p_muid_bonus_dialog [name=name]").val(),
						employee: $("#p_muid_bonus_dialog [name=employee]").val(),
						date: $("#p_muid_bonus_dialog [name=effective_date]").val(),
						amount: $("#p_muid_bonus_dialog [name=amount]").val(),
						comments: $("#p_muid_bonus_dialog [name=comments]").val()
					});
					bonus_dialog.dialog("close");
				}
			}
		});

		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_hrm/listemployees')) { ?>
				{type: 'button', text: '&laquo; Employees', extra_class: 'picon picon-system-users', selection_optional: true, url: <?php echo json_encode(pines_url('com_hrm', 'employee/list')); ?>},
				<?php } if (gatekeeper('com_hrm/editbonus')) { ?>
				{type: 'button', text: 'New', extra_class: 'picon picon-document-new', selection_optional: true, click: function(){
					bonus_id = 0;
					$("#p_muid_bonus_dialog [name=effective_date]").val(<?php echo json_encode(format_date(time(), 'date_sort')); ?>);
					$("#p_muid_bonus_dialog [name=name]").val("");
					$("#p_muid_bonus_dialog [name=employee]").val("");
					$("#p_muid_bonus_dialog [name=amount]").val("");
					$("#p_muid_bonus_dialog [name=comments]").val("");
					bonus_dialog.dialog("open");
				}},
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, click: function(e, row){
					bonus_id = row.attr("title");
					$("#p_muid_bonus_dialog [name=effective_date]").val(pines.unsafe(row.pgrid_get_value(1)));
					$("#p_muid_bonus_dialog [name=name]").val(pines.unsafe(row.pgrid_get_value(2)));
					$("#p_muid_bonus_dialog [name=employee]").val(pines.unsafe(row.pgrid_get_value(3)));
					$("#p_muid_bonus_dialog [name=amount]").val(pines.unsafe(row.pgrid_get_value(4)).replace('$',''));
					$("#p_muid_bonus_dialog [name=comments]").val(pines.unsafe(row.pgrid_get_value(5)));
					bonus_dialog.dialog("open");
				}},
				{type: 'button', text: 'Remove', extra_class: 'picon picon-document-close', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_hrm', 'bonus/delete', array('id' => '__title__'))); ?>},
				{type: 'separator'},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'employee_bonuses',
						content: rows
					});
				}}
			],
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc'
		};
		$("#p_muid_grid").pgrid(cur_defaults);
		$("#p_muid_bonus_dialog [name=employee]").employeeselect();
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Date</th>
			<th>Description</th>
			<th>Employee</th>
			<th>Amount</th>
			<th>Comments</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->bonuses as $cur_bonus) { ?>
		<tr title="<?php e($cur_bonus->guid); ?>">
			<td><?php e(format_date($cur_bonus->date)); ?></td>
			<td><?php e($cur_bonus->name); ?></td>
			<td><?php e($cur_bonus->employee->guid.': '.$cur_bonus->employee->name); ?></td>
			<td>$<?php e(number_format($cur_bonus->amount, 2, '.', '')); ?></td>
			<td><?php e($cur_bonus->comments); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div id="p_muid_bonus_dialog" title="Employee Bonus" style="display: none;">
	<form class="pf-form" method="post" action="">
		<div class="pf-element">
			<label><span class="pf-label">Employee</span>
				<input class="pf-field" type="text" size="24" name="employee" /></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Bonus</span>
				<input class="pf-field" type="text" size="24" name="name" /></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Date</span>
				<input type="text" size="24" name="effective_date" value="<?php e(format_date(time(), 'date_sort')); ?>" /></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Amount</span>
				<span class="pf-field">$ <input type="text" size="5" name="amount" /></span></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Comments</span>
				<input class="pf-field" type="text" size="24" name="comments" value="" /></label>
		</div>
	</form>
	<br />
</div>