<?php
/**
 * Provides a list of shop products that are problematic/incomplete.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Problematic/Incomplete Shop Products';
$_->com_pgrid->load();
?>
<?php if (empty($this->items) && empty($this->image_descs)) { ?>
<p>All shop products appear to be valid.</p>
<?php } else { ?>
<script type="text/javascript">
	$_(function() {
		var options = {
			pgrid_view_height: "200px",
			pgrid_paginate: false,
			pgrid_select: true,
			pgrid_multi_select: true,
			pgrid_resize: false,
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'desc',
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_sales/editproduct')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'product/edit', array('id' => '__title__'))); ?>, target: 'editing_stock'},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'problematic_products',
						content: rows
					});
				}}
			]
		};
		$("#p_muid_grid, #p_muid_grid2").pgrid(options);
	});
</script>
<div class="pf-form">
	<?php if (!empty($this->items)) { ?>
	<div class="pf-element pf-heading">
		<h3>These items need to be listed as stock optional</h3>
	</div>
	<table id="p_muid_grid">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Name</th>
				<th>Enabled</th>
				<?php if ($_->config->com_sales->enable_manufacturers) { ?>
				<th>Manufacturer</th>
				<th>Manufacturer SKU</th>
				<?php } ?>
				<th>Pricing Method</th>
				<th>Stock Type</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->items as $cur_item) { ?>
			<tr title="<?php e($cur_item->guid); ?>">
				<td><?php e($cur_item->sku); ?></td>
				<td><?php e($cur_item->name); ?></td>
				<td><?php echo ($cur_item->enabled ? 'Yes' : 'No'); ?></td>
				<?php if ($_->config->com_sales->enable_manufacturers) { ?>
				<td><?php e($cur_item->manufacturer->name); ?></td>
				<td><?php e($cur_item->manufacturer_sku); ?></td>
				<?php } ?>
				<td><?php e($cur_item->pricing_method); ?></td>
				<td><?php e($cur_item->stock_type); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php } if (!empty($this->image_descs)) { ?>
	<div class="pf-element pf-heading">
		<h3>These items need image descriptions</h3>
	</div>
	<table id="p_muid_grid2">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Name</th>
				<th>Enabled</th>
				<?php if ($_->config->com_sales->enable_manufacturers) { ?>
				<th>Manufacturer</th>
				<th>Manufacturer SKU</th>
				<?php } ?>
				<th>Pricing Method</th>
				<th>Stock Type</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->image_descs as $cur_item) { ?>
			<tr title="<?php e($cur_item->guid); ?>">
				<td><?php e($cur_item->sku); ?></td>
				<td><?php e($cur_item->name); ?></td>
				<td><?php echo ($cur_item->enabled ? 'Yes' : 'No'); ?></td>
				<?php if ($_->config->com_sales->enable_manufacturers) { ?>
				<td><?php e($cur_item->manufacturer->name); ?></td>
				<td><?php e($cur_item->manufacturer_sku); ?></td>
				<?php } ?>
				<td><?php e($cur_item->pricing_method); ?></td>
				<td><?php e($cur_item->stock_type); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php } ?>
</div>
<?php }