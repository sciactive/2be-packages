<?php
/**
 * Lists transfers and provides functions to manipulate them.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = ($this->finished ? 'Completed ' : '').'Transfers';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/transfer/list']);
?>
<script type="text/javascript">
	pines(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_sales/managestock')) { ?>
				{type: 'button', text: 'New', extra_class: 'picon picon-document-new', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'transfer/edit')); ?>},
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'transfer/edit', array('id' => '__title__'))); ?>},
				<?php } if (gatekeeper('com_sales/shipstock')) { ?>
				{type: 'button', text: 'Ship', extra_class: 'picon picon-mail-send', url: <?php echo json_encode(pines_url('com_sales', 'transfer/ship', array('id' => '__title__'))); ?>},
				<?php } ?>
				//{type: 'button', text: 'E-Mail', extra_class: 'picon picon-mail-message-new', multi_select: true, url: 'mailto:__col_2__', delimiter: ','},
				{type: 'separator'},
				<?php if (gatekeeper('com_sales/managestock')) { ?>
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'transfer/delete', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'separator'},
				<?php } if (!$this->finished) { ?>
				{type: 'button', text: 'Completed', extra_class: 'picon picon-vcs-removed', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'transfer/list', array('finished' => 'true'))); ?>},
				<?php } else { ?>
				{type: 'button', text: 'Pending', extra_class: 'picon picon-vcs-normal', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'transfer/list')); ?>},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'transfers',
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
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/transfer/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		$("#p_muid_grid").pgrid(cur_options);
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Transfer ID</th>
			<th>Reference Number</th>
			<th>Origin</th>
			<th>Destination</th>
			<th>Shipped</th>
			<th>Shipper</th>
			<th>ETA</th>
			<th>Status</th>
			<th>Products</th>
			<th>Comments</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->transfers as $transfer) { ?>
		<tr title="<?php e($transfer->guid); ?>">
			<td><a data-entity="<?php e($transfer->guid); ?>" data-entity-context="com_sales_transfer"><?php e($transfer->guid); ?></a></td>
			<td><?php e($transfer->reference_number); ?></td>
			<td><a data-entity="<?php e($transfer->origin->guid); ?>" data-entity-context="group"><?php echo $transfer->origin->guid ? h("{$transfer->origin->name} [{$transfer->origin->groupname}]") : ''; ?></a></td>
			<td><a data-entity="<?php e($transfer->destination->guid); ?>" data-entity-context="group"><?php echo $transfer->destination->guid ? h("{$transfer->destination->name} [{$transfer->destination->groupname}]") : ''; ?></a></td>
			<td><?php echo $transfer->shipped ? h(format_date($transfer->shipped_date, 'full_sort')) : 'No'; ?></td>
			<td><a data-entity="<?php e($transfer->shipper->guid); ?>" data-entity-context="com_sales_shipper"><?php e($transfer->shipper->name); ?></a></td>
			<td><?php echo $transfer->eta ? h(format_date($transfer->eta, 'date_sort')) : 'None'; ?></td>
			<td><?php echo $transfer->final ? ($transfer->finished ? 'Received' : (empty($transfer->received) ? 'Not Received' : 'Partially Received')) : 'Not Committed'; ?></td>
			<td><?php
			$names = array();
			foreach ((array) $transfer->products as $cur_product) {
				$names[] = '<a data-entity="'.h($cur_product->guid).'" data-entity-context="com_sales_product">'.h("{$cur_product->name} [{$cur_product->sku}]").'</a>';
			}
			echo implode(', ', $names);
			?></td>
			<td><?php e($transfer->comments); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>