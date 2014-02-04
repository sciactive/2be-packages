<?php
/**
 * Lists taxes/fees and provides functions to manipulate them.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Taxes/Fees';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/taxfee/list']);
?>
<script type="text/javascript">
	pines(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_sales/newtaxfee')) { ?>
				{type: 'button', text: 'New', extra_class: 'picon picon-document-new', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'taxfee/edit')); ?>},
				<?php } if (gatekeeper('com_sales/edittaxfee')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'taxfee/edit', array('id' => '__title__'))); ?>},
				<?php } ?>
				//{type: 'button', text: 'E-Mail', extra_class: 'picon picon-mail-message-new', multi_select: true, url: 'mailto:__col_2__', delimiter: ','},
				{type: 'separator'},
				<?php if (gatekeeper('com_sales/deletetaxfee')) { ?>
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'taxfee/delete', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'separator'},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'taxes and fees',
						content: rows
					});
				}}
			],
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/taxfee/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		$("#p_muid_grid").pgrid(cur_options);
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Name</th>
			<th>Enabled</th>
			<th>Type</th>
			<th>Rate</th>
			<th>Locations</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->tax_fees as $tax_fee) { ?>
		<tr title="<?php e($tax_fee->guid); ?>">
			<td><a data-entity="<?php e($tax_fee->guid); ?>" data-entity-context="com_sales_tax_fee"><?php e($tax_fee->name); ?></a></td>
			<td><?php echo $tax_fee->enabled ? 'Yes' : 'No'; ?></td>
			<td><?php echo $tax_fee->type == 'percentage' ? 'Percentage' : 'Flat Rate'; ?></td>
			<td><?php e($tax_fee->rate); ?></td>
			<td><?php
			$groupname_array = array();
			foreach ($tax_fee->locations as $cur_location) {
				if (!isset($cur_location->guid))
					continue;
				$groupname_array[] = '<a data-entity="'.h($cur_location->guid).'" data-entity-context="group">'.h($cur_location->info('name')).'</a>';
			}
			echo implode(', ', $groupname_array);
			?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>