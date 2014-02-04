<?php
/**
 * Displays assigned warehouse items.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Assigned Warehouse Orders';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/warehouse/assigned']);
?>
<script type="text/javascript">
	pines(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_sales/managestock')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'stock/edit', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'button', text: 'Transfer', extra_class: 'picon picon-go-jump', multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'stock/transfer', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'button', text: 'Ship Sale', extra_class: 'picon picon-mail-send', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'shipment/ship', array('id' => '__col_1__'))); ?>},
				{type: 'separator'},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'assigned warehouse orders',
						content: rows
					});
				}}
			],
			pgrid_sort_col: 2,
			pgrid_sort_ord: 'asc',
			pgrid_hidden_cols: [1],
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/warehouse/assigned", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		$("#p_muid_grid").pgrid(cur_options);
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Sale GUID</th>
			<th>Date</th>
			<th>Product</th>
			<th>Serial</th>
			<th>Sale ID</th>
			<th>Sale Location</th>
			<th>Customer</th>
			<th>Current Location</th>
			<th>Fulfilled</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->sales as $sale) {
		foreach ($sale->products as $key => $cur_product) {
			// Filter non warehouse products.
			if ($cur_product['delivery'] != 'warehouse')
				continue;
			foreach ($cur_product['stock_entities'] as $skey => $cur_stock) {
				if ($cur_stock->in_array($cur_product['returned_stock_entities']) || $cur_stock->in_array($cur_product['shipped_entities']))
					continue;
		?>
		<tr title="<?php e($cur_stock->guid); ?>">
			<td><?php e($sale->guid); ?></td>
			<td><?php e(format_date($sale->tender_date, 'full_sort')); ?></td>
			<td><a data-entity="<?php e($cur_product['entity']->guid); ?>" data-entity-context="com_sales_product"><?php e("{$cur_product['entity']->sku} : {$cur_product['entity']->name}"); ?></a></td>
			<td><?php e($cur_stock->serial); ?></td>
			<td><a data-entity="<?php e($sale->guid); ?>" data-entity-context="com_sales_sale"><?php e($sale->id); ?></a></td>
			<td><a data-entity="<?php e($sale->group->guid); ?>" data-entity-context="group"><?php e("{$sale->group->name} [{$sale->group->groupname}]"); ?></a></td>
			<td><a data-entity="<?php e($sale->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e("{$sale->customer->guid}: {$sale->customer->name}"); ?></a></td>
			<?php if (isset($cur_stock->location)) { ?>
			<td><a data-entity="<?php e($cur_stock->location->guid); ?>" data-entity-context="group"><?php e("{$cur_stock->location->name} [{$cur_stock->location->groupname}]"); ?></a></td>
			<td><?php echo $cur_stock->location->is($sale->group) ? 'Yes' : 'No'; ?></td>
			<?php } else { ?>
			<td>In Transit</td>
			<td>No</td>
			<?php } ?>
		</tr>
	<?php } } } ?>
	</tbody>
</table>