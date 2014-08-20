<?php
/**
 * Lists products and provides functions to manipulate them.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = ($this->enabled ? '' : 'Disabled ').'Products';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/product/list']);
?>
<script type="text/javascript">
	$_(function(){
		// Product search function for the pgrid toolbar.
		var product_search_box;
		var submit_search = function(){
			var search_string = product_search_box.val();
			if (search_string == "") {
				alert("Please enter a search string.");
				return;
			}
			var loader;
			$.ajax({
				url: <?php echo json_encode(pines_url('com_sales', 'product/search')); ?>,
				type: "POST",
				dataType: "json",
				data: {"q": search_string, "enabled": <?php echo json_encode($this->enabled ? 'true' : 'false'); ?>},
				beforeSend: function(){
					loader = new PNotify({
						title: 'Search',
						text: 'Searching the database...',
						icon: 'picon picon-throbber',
						nonblock: {
							nonblock: true
						},
						hide: false,
						history: {
							history: false
						}
					});
				},
				complete: function(){
					loader.remove();
				},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
				},
				success: function(data){
					if (!data) {
						product_grid.pgrid_get_all_rows().pgrid_delete();
						alert("No products were found that matched the query.");
						return;
					}
					var struct = [];
					$.each(data, function(){
						<?php if ($_->config->com_sales->enable_manufacturers) { ?>
						if (typeof this.manufacturer_name != "undefined" && this.manufacturer_name !== null)
							this.manufacturer = '<a data-entity="'+$_.safe(this.manufacturer_guid)+'" data-entity-context="com_sales_manufacturer">'+$_.safe(this.manufacturer_name)+'</a>';
						else
							this.manufacturer = '';
						<?php } ?>
						var vendors = [],
							links = [],
							costs = [];
						$.each(this.vendors, function(i, vendor){
							vendors.push('<a data-entity="'+$_.safe(vendor.guid)+'" data-entity-context="com_sales_vendor">'+$_.safe(vendor.name)+'</a>');
							costs.push(vendor.cost);
							if (typeof vendor.link != "undefined" && vendor.link !== null && vendor.link !== "")
								links.push('<a href="'+$_.safe(vendor.link)+'" target="_blank">'+$_.safe(vendor.link)+'</a>');
						});
						struct.push({
							"key": this.guid,
							"values": [
								$_.safe(this.sku),
								'<a data-entity="'+$_.safe(this.guid)+'" data-entity-context="com_sales_product">'+$_.safe(this.name)+'</a>',
								$_.safe('$'+this.price),
								$_.safe(costs.join(', ')),
								vendors.join(', '),
								<?php if ($_->config->com_sales->enable_manufacturers) { ?>
								this.manufacturer,
								$_.safe(this.manufacturer_sku),
								<?php } ?>
								$_.safe(this.stock_type),
								this.custom_item ? 'Yes' : 'No',
								this.serialized ? 'Yes' : 'No',
								this.discountable ? 'Yes' : 'No',
								$_.safe(this.additional_barcodes),
								$_.safe(this.images),
								$_.safe(this.receipt_description),
								$_.safe(this.created),
								$_.safe(this.modified),
								$_.safe(this.expiration),
								links.join(', ')
								<?php if ($_->config->com_sales->com_shop) { ?>,
								this.shop ? 'Yes' : 'No',
								this.featured ? 'Yes' : 'No'
								<?php } ?>
							]
						});
					});
					product_grid.pgrid_get_all_rows().pgrid_delete();
					product_grid.pgrid_add(struct);
				}
			});
		}

		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				{type: 'text', load: function(textbox){
					// Display the current product being searched for.
					textbox.keydown(function(e){
						if (e.keyCode == 13)
							submit_search();
					});
					product_search_box = textbox;
					<?php if (!empty($this->show)) { ?>
					product_search_box.val(<?php echo json_encode((string) $this->show); ?>);
					submit_search();
					<?php } ?>
				}},
				{type: 'button', extra_class: 'picon picon-system-search', selection_optional: true, pass_csv_with_headers: true, click: submit_search},
				<?php if (gatekeeper('com_sales/newproduct')) { ?>
				{type: 'button', text: 'New', extra_class: 'picon picon-document-new', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'product/edit')); ?>},
				<?php } if (gatekeeper('com_sales/editproduct')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'product/edit', array('id' => '__title__'))); ?>},
				<?php } ?>
				{type: 'separator'},
				<?php if (gatekeeper('com_sales/deleteproduct')) { ?>
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'product/delete', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'separator'},
				<?php } if ($this->enabled) { ?>
				{type: 'button', text: 'Disabled', extra_class: 'picon picon-vcs-removed', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'product/list', array('enabled' => 'false'))); ?>},
				<?php } else { ?>
				{type: 'button', text: 'Enabled', extra_class: 'picon picon-vcs-normal', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'product/list')); ?>},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', text: 'Image Report', title: 'See products that have incomplete images.', extra_class: 'picon picon-graphics-viewer-document', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'product/reportimages')); ?>},
				{type: 'button', text: 'Image Archive', title: 'Generate an archive of all product images.', extra_class: 'picon picon-folder-image', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'product/image_archive')); ?>},
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'products',
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
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/product/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var product_grid = $("#p_muid_grid").pgrid(cur_options);
		product_grid.pgrid_get_all_rows().pgrid_delete();
		cur_options.pgrid_state_change(product_grid.pgrid_export_state());
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
			<th>Custom Item</th>
			<th>Serialized</th>
			<th>Discountable</th>
			<th>Additional Barcodes</th>
			<th>Images</th>
			<th>Receipt Description</th>
			<th>Created</th>
			<th>Modified</th>
			<th>Expiration</th>
			<th>Link</th>
			<?php if ($_->config->com_sales->com_shop) { ?>
			<th>Shown in Shop</th>
			<th>Featured</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<?php if ($_->config->com_sales->com_shop) { ?>
			<td>-</td>
			<td>-</td>
			<?php } ?>
		</tr>
	</tbody>
</table>