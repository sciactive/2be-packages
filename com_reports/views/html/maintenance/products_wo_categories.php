<?php
/**
 * Shows a list of products without categories.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$this->title = 'Products Without Categories';
$this->note = 'That is, <em>enabled</em> products without <em>enabled</em> categories.';
$_->com_pgrid->load();
?>
<script type="text/javascript">
	$_(function(){
		<?php if ($this->fix_cat_count) { ?>
		$_.notice("Also, while this report was running, I took the liberty of fixing <?php echo (int) $this->fix_cat_count; ?> broken product references I found.", "Cleanup");
		<?php } ?>
		$("#p_muid_grid").pgrid({
			pgrid_toolbar: true,
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_sales/editproduct')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'product/edit', array('id' => '__title__'))); ?>},
				<?php } ?>
				{type: 'separator'},
				<?php if (gatekeeper('com_sales/deleteproduct')) { ?>
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'product/delete', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'separator'},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'products_without_categories',
						content: rows
					});
				}}
			]
		});
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>SKU</th>
			<th>Name</th>
			<th>Price</th>
			<th>Cost(s)</th>
			<th>Vendor(s)</th>
			<?php if ($_->config->com_sales->enable_manufacturers) { ?>
			<th>Manufacturer</th>
			<th>Manufacturer SKU</th>
			<?php } ?>
			<th>Stock Type</th>
			<th>Serialized</th>
			<th>Discountable</th>
			<th>Additional Barcodes</th>
			<th>Receipt Description</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->products as $product) {
		$costs = $vendors = array();
		foreach($product->vendors as $cur_vendor) {
			$vendors[] = '<a data-entity="'.h($cur_vendor['entity']->guid).'" data-entity-context="com_sales_vendor">'.h($cur_vendor['entity']->name).'</a>';
			$costs[] = '$'.$_->com_sales->round($cur_vendor['cost'], true);
		}
	?>
		<tr title="<?php e($product->guid); ?>">
			<td><?php e($product->sku); ?></td>
			<td><a data-entity="<?php e($product->guid); ?>" data-entity-context="com_sales_product"><?php e($product->name); ?></a></td>
			<td style="text-align: right;">$<?php e($_->com_sales->round($product->unit_price, true)); ?></td>
			<td style="text-align: right;"><?php e(implode(', ', $costs)); ?></td>
			<td><?php echo implode(', ', $vendors); ?></td>
			<?php if ($_->config->com_sales->enable_manufacturers) { ?>
			<td><a data-entity="<?php e($product->manufacturer->guid); ?>" data-entity-context="com_sales_manufacturer"><?php e($product->manufacturer->name); ?></a></td>
			<td><?php e($product->manufacturer_sku); ?></td>
			<?php } ?>
			<td><?php switch ($product->stock_type) {
				case 'non_stocked':
					echo 'Non Stocked';
					break;
				case 'stock_optional':
					echo 'Stock Optional';
					break;
				case 'regular_stock':
					echo 'Regular Stock';
					break;
				default:
					echo 'Unrecognized';
					break;
			} ?></td>
			<td><?php echo ($product->serialized ? 'Yes' : 'No'); ?></td>
			<td><?php echo ($product->discountable ? 'Yes' : 'No'); ?></td>
			<td><?php e(implode(', ', $product->additional_barcodes)); ?></td>
			<td><?php e($product->receipt_description); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>