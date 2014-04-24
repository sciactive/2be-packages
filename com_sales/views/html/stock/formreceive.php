<?php
/**
 * Provides a form for the user to receive inventory.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Receive Inventory';
if (!gatekeeper('com_sales/receivelocation'))
	$this->note = 'Only use this form to receive inventory into your <strong>current</strong> location ('.(!isset($_SESSION['user']->group) ? 'No Location' : h($_SESSION['user']->group->name)).').';
$_->com_pgrid->load();
$_->com_jstree->load();
if ($_->config->com_sales->autocomplete_product)
	$_->com_sales->load_product_select();
?>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_sales', 'stock/receive')); ?>">
	<script type="text/javascript">
		$_(function(){
			var products = $("#p_muid_products");
			var products_table = $("#p_muid_products_table");

			$_.com_sales_add_product = function(data){
				var serial = "";
				if (data.serialized) {
					while (!serial) {
						serial = prompt("\""+data.name+"\" is serialized. Please provide the serial:");
						if (serial == null)
							return;
					}
				} else {
					var match = false;
					products_table.pgrid_get_all_rows().each(function(){
						if (match)
							return;
						var cur_row = $(this);
						if (cur_row.pgrid_get_value(1) == data.sku) {
							cur_row.pgrid_set_value(3, parseInt(cur_row.pgrid_get_value(3)) + 1);
							$_.com_sales_update_products();
							match = true;
						}
					});
					if (match)
						return;
				}
				products_table.pgrid_add([{values: [$_.safe(data.sku), $_.safe(serial), 1]}], function(){
					var cur_row = $(this);
					cur_row.data("product", data);
				});
				$_.com_sales_update_products();
			};

			$_.com_sales_update_products = function(){
				var all_rows = products_table.pgrid_get_all_rows().pgrid_export_rows();
				products.val(JSON.stringify(all_rows));
			};

			var receive_row = function(row){
				var loader, url, cur_row = $(row);
				if (cur_row.hasClass('pending_receiving')) {
					if (!confirm('It looks like you\'ve already selected this shipment for receiving. Are you sure you want to add it again?'))
						return;
				}
				if (cur_row.pgrid_get_value(1) == "PO")
					url = <?php echo json_encode(pines_url('com_sales', 'po/products')); ?>;
				else
					url = <?php echo json_encode(pines_url('com_sales', 'transfer/products')); ?>;
				$.ajax({
					url: url,
					type: "POST",
					dataType: "json",
					data: {"id": cur_row.attr('title')},
					beforeSend: function(){
						loader = new PNotify({
							title: 'Shipment Search',
							text: 'Retrieving products...',
							icon: 'picon picon-throbber',
							nonblock: true,
							hide: false,
							history: false
						});
					},
					complete: function(){
						loader.pnotify_remove();
					},
					error: function(XMLHttpRequest, textStatus){
						$_.error("An error occured while trying to lookup the shipment:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
					},
					success: function(data){
						if (!data) {
							alert("No shipment was found for "+cur_row.pgrid_get_value(1)+".");
							return;
						}
						cur_row.addClass('pending_receiving ui-state-highlight');
						$.each(data, function(){
							for (var i = 0; i < this.quantity; i++)
								$_.com_sales_add_product(this);
						});
						var guids = shipment_table.find(".pending_receiving").map(function(){return $(this).attr('title')}).get();
						$("#p_muid_shipments").val(JSON.stringify(guids));
					}
				});
			};
			var shipment_table = $("#p_muid_shipment_table").pgrid({
				pgrid_paginate: false,
				pgrid_view_height: '250px',
				pgrid_sort_col: 3,
				pgrid_toolbar: true,
				pgrid_toolbar_contents : [
					{
						type: 'button',
						text: 'Receive',
						title: 'Receive the selected Shipments',
						extra_class: 'picon picon-mail-receive',
						double_click: true,
						multi_select: true,
						click: function(e, rows){
							$.each(rows, function(){
								receive_row(this);
							});
						}
					},
					{type: 'separator'},
					{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
					{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true}
				]
			});

			products_table.pgrid({
				pgrid_paginate: false,
				pgrid_view_height: '350px',
				pgrid_filtering: false,
				pgrid_toolbar: true,
				pgrid_toolbar_contents : [
					{
						type: 'button',
						title: 'Select a Product by Category',
						extra_class: 'picon picon-view-list-tree',
						selection_optional: true,
						click: function(){
							category_dialog.dialog("open");
						}
					},
					{
						type: 'text',
						title: 'Enter a Product SKU or Barcode',
						load: function(textbox){
							var select = function(code){
								if (code == "") {
									alert("Please enter a product code.");
									return;
								}
								textbox.val("");
								var loader;
								$.ajax({
									url: <?php echo json_encode(pines_url('com_sales', 'product/codesearch')); ?>,
									type: "POST",
									dataType: "json",
									data: {"code": code},
									beforeSend: function(){
										loader = new PNotify({
											title: 'Product Search',
											text: 'Retrieving product from server...',
											icon: 'picon picon-throbber',
											nonblock: true,
											hide: false,
											history: false
										});
									},
									complete: function(){
										loader.pnotify_remove();
									},
									error: function(XMLHttpRequest, textStatus){
										$_.error("An error occured while trying to lookup the product code:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
									},
									success: function(data){
										if (!data) {
											alert("No product was found with the code "+code+".");
											return;
										}
										$_.com_sales_add_product(data);
									}
								});
							};
							<?php if ($_->config->com_sales->autocomplete_product) { ?>
							textbox.productselect({select: function(event, ui){select(ui.item.value); return false;}});
							<?php } ?>
							textbox.keydown(function(e){
								if (e.keyCode == 13)
									select(textbox.val());
							});
						}
					},
					{type: 'separator'},
					{
						type: 'button',
						text: 'Serial',
						extra_class: 'picon picon-view-barcode',
						double_click: true,
						click: function(e, rows){
							var product = rows.data("product");
							if (!product.serialized) {
								alert("This product isn't serialized.");
								return;
							}
							var serial = rows.pgrid_get_value(2);
							do {
								serial = prompt("This item is serialized. Please provide the serial:", serial);
							} while (!serial && serial != null);
							if (serial != null) {
								rows.pgrid_set_value(2, $_.safe(serial));
								$_.com_sales_update_products();
							}
						}
					},
					{
						type: 'button',
						text: 'Qty',
						extra_class: 'picon picon-document-multiple',
						double_click: true,
						click: function(e, rows){
							var product = rows.data("product");
							if (product.serialized) {
								alert("This product is serialized.");
								return;
							}
							var qty = rows.pgrid_get_value(3);
							do {
								qty = prompt("Please enter a quantity:", qty);
							} while ((parseInt(qty) < 1 || isNaN(parseInt(qty))) && qty != null);
							if (qty != null) {
								rows.pgrid_set_value(3, parseInt(qty));
								$_.com_sales_update_products();
							}
						}
					},
					{type: 'separator'},
					{
						type: 'button',
						title: 'Remove',
						extra_class: 'picon picon-edit-delete',
						multi_select: true,
						click: function(e, rows){
							rows.pgrid_delete();
							$_.com_sales_update_products();
						}
					}
				]
			});

			// Category Grid
			var category_grid = $("#p_muid_category_grid").pgrid({
				pgrid_hidden_cols: [1],
				pgrid_sort_col: 1,
				pgrid_sort_ord: "asc",
				pgrid_child_prefix: "ch_",
				pgrid_paginate: false,
				pgrid_view_height: "300px",
				pgrid_multi_select: false,
				pgrid_double_click: function(e, row){
					category_products_grid.pgrid_get_all_rows().pgrid_delete();
					var loader;
					$.ajax({
						url: <?php echo json_encode(pines_url('com_sales', 'category/products')); ?>,
						type: "POST",
						dataType: "json",
						data: {"id": $(row).attr("title")},
						beforeSend: function(){
							loader = new PNotify({
								title: 'Product Search',
								text: 'Retrieving product from server...',
								icon: 'picon picon-throbber',
								nonblock: true,
								hide: false,
								history: false
							});
						},
						complete: function(){
							loader.pnotify_remove();
						},
						error: function(XMLHttpRequest, textStatus){
							$_.error("An error occured while trying to lookup the product code:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
						},
						success: function(data){
							if (!data || !data[0]) {
								alert("No products were returned.");
								return;
							}
							$.each(data, function(){
								var product = this;
								category_products_grid.pgrid_add([{key: this.guid, values: ['<a data-entity="'+$_.safe(this.guid)+'" data-entity-context="com_sales_product">'+$_.safe(this.name)+'</a>', $_.safe(this.sku)]}], function(){
									$(this).data("product", product);
								});
							});
							category_products_dialog.dialog("open");
						}
					});
				}
			});
			// Category Dialog
			var category_dialog = $("#p_muid_category_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				modal: true,
				width: 600,
				open: function() {
					category_grid.pgrid_get_selected_rows().pgrid_deselect_rows();
				}
			});
			// Category Products Grid
			var category_products_grid = $("#p_muid_category_products_grid").pgrid({
				pgrid_sort_col: 1,
				pgrid_sort_ord: "asc",
				pgrid_view_height: "300px",
				pgrid_multi_select: false,
				pgrid_double_click: function(){
					category_products_dialog.dialog("option", "buttons").Done();
				}
			});
			// Category Products Dialog
			var category_products_dialog = $("#p_muid_category_products_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				modal: true,
				width: 800,
				open: function() {
					category_products_grid.pgrid_get_selected_rows().pgrid_deselect_rows();
				},
				buttons: {
					'Done': function() {
						var data = category_products_grid.pgrid_get_selected_rows().data("product");
						if (!data) {
							alert("Please select a product.");
							return;
						}
						$_.com_sales_add_product(data);
						category_products_dialog.dialog('close');
						category_dialog.dialog('close');
					}
				}
			});

			$_.com_sales_update_products();

			<?php if (isset($this->shipments)) { foreach ((array) $this->shipments as $cur_id) { ?>
			(function(){
				var row = shipment_table.find('tbody tr[title='+<?php echo json_decode("$cur_id"); ?>+']');
				if (row.length)
					receive_row(row);
			})();
			<?php } } ?>
		});
	</script>
	<?php if (gatekeeper('com_sales/receivelocation')) { ?>
	<script type='text/javascript'>
		$_(function(){
			// Location Tree
			var location = $("#p_muid_form [name=location]");
			$("#p_muid_form .location_tree")
			.bind("select_node.jstree", function(e, data){
				location.val(data.inst.get_selected().attr("id").replace("p_muid_", ""));
			})
			.bind("before.jstree", function (e, data){
				if (data.func == "parse_json" && "args" in data && 0 in data.args && "attr" in data.args[0] && "id" in data.args[0].attr)
					data.args[0].attr.id = "p_muid_"+data.args[0].attr.id;
			})
			.bind("loaded.jstree", function(e, data){
				var path = data.inst.get_path("#"+data.inst.get_settings().ui.initially_select, true);
				if (!path.length) return;
				data.inst.open_node("#"+path.join(", #"), false, true);
			})
			.jstree({
				"plugins" : [ "themes", "json_data", "ui" ],
				"json_data" : {
					"ajax" : {
						"dataType" : "json",
						"url" : <?php echo json_encode(pines_url('com_jstree', 'groupjson')); ?>
					}
				},
				"ui" : {
					"select_limit" : 1,
					"initially_select" : [<?php echo isset($this->location) ? json_encode("{$this->location}") : (isset($_SESSION['user']->group->guid) ? json_encode("{$_SESSION['user']->group->guid}") : '""'); ?>]
				}
			});
		});
	</script>
	<div class="pf-element pf-full-width">
		<span class="pf-label">Location</span>
		<div class="pf-group">
			<div class="pf-field location_tree ui-widget-content ui-corner-all" style="height: 180px; width: 200px; overflow: auto;"></div>
		</div>
		<input type="hidden" name="location" value="<?php echo (!isset($_SESSION['user']->group) ? '' : $_SESSION['user']->group->guid); ?>" />
	</div>
	<?php } ?>
	<div id="p_muid_category_dialog" title="Categories" style="display: none;">
		<table id="p_muid_category_grid">
			<thead>
				<tr>
					<th>Order</th>
					<th>Name</th>
					<th>Products</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($this->categories as $category) { ?>
				<tr title="<?php e($category->guid); ?>" class="<?php echo $category->children ? 'parent ' : ''; ?><?php echo isset($category->parent) ? h("child ch_{$category->parent->guid} ") : ''; ?>">
					<td><?php echo isset($category->parent) ? $category->array_search($category->parent->children) + 1 : '0' ; ?></td>
					<td><?php e($category->name); ?></td>
					<td><?php echo count($category->products); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		<br class="pf-clearing" />
	</div>
	<div id="p_muid_category_products_dialog" title="Products" style="display: none;">
		<table id="p_muid_category_products_grid">
			<thead>
				<tr>
					<th>Name</th>
					<th>SKU</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<br class="pf-clearing" />
	</div>
	<div class="pf-element pf-heading">
		<h3>Purchase Orders / Transfers</h3>
	</div>
	<div class="pf-element pf-full-width">
		<div class="pf-field">
			<table id="p_muid_shipment_table">
				<thead>
					<tr>
						<th>Type</th>
						<th>Number</th>
						<th>ETA</th>
						<th>Reference Number</th>
						<th>Destination</th>
						<th>Origin/Vendor</th>
						<th>Shipper</th>
						<th>Status</th>
						<th>Products</th>
						<th>Comments</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($this->pos as $cur_shipment) { ?>
					<tr title="<?php e($cur_shipment->guid); ?>">
						<td>PO</td>
						<td><a data-entity="<?php e($cur_shipment->guid); ?>" data-entity-context="com_sales_po"><?php e($cur_shipment->po_number); ?></a></td>
						<td><?php echo ($cur_shipment->eta ? h(format_date($cur_shipment->eta, 'date_sort')) : ''); ?></td>
						<td><?php e($cur_shipment->reference_number); ?></td>
						<td><a data-entity="<?php e($cur_shipment->destination->guid); ?>" data-entity-context="group"><?php e("{$cur_shipment->destination->name} [{$cur_shipment->destination->groupname}]"); ?></a></td>
						<td><a data-entity="<?php e($cur_shipment->vendor->guid); ?>" data-entity-context="com_sales_vendor"><?php e($cur_shipment->vendor->name); ?></a></td>
						<td><a data-entity="<?php e($cur_shipment->shipper->guid); ?>" data-entity-context="com_sales_shipper"><?php e($cur_shipment->shipper->name); ?></a></td>
						<td><?php echo $cur_shipment->final ? ($cur_shipment->finished ? 'Received' : (empty($cur_shipment->received) ? 'Not Received' : 'Partially Received')) : 'Not Committed'; ?></td>
						<td><?php
						$names = array();
						foreach ((array) $cur_shipment->products as $cur_product)
							$names[] = '<a data-entity="'.h($cur_product['entity']->guid).'" data-entity-context="com_sales_product">'.h("{$cur_product['entity']->name} [{$cur_product['entity']->sku}]").'</a>'.($cur_product['quantity'] > 1 ? h(" x {$cur_product['quantity']}") : '');
						echo implode(', ', $names);
						?></td>
						<td><?php e($cur_shipment->comments); ?></td>
					</tr>
				<?php } foreach($this->transfers as $cur_shipment) { ?>
					<tr title="<?php e($cur_shipment->guid); ?>">
						<td>Transfer</td>
						<td><a data-entity="<?php e($cur_shipment->guid); ?>" data-entity-context="com_sales_transfer"><?php e($cur_shipment->guid); ?></a></td>
						<td><?php echo ($cur_shipment->eta ? h(format_date($cur_shipment->eta, 'date_sort')) : ''); ?></td>
						<td><?php e($cur_shipment->reference_number); ?></td>
						<td><a data-entity="<?php e($cur_shipment->destination->guid); ?>" data-entity-context="group"><?php e("{$cur_shipment->destination->name} [{$cur_shipment->destination->groupname}]"); ?></a></td>
						<td><a data-entity="<?php e($cur_shipment->origin->guid); ?>" data-entity-context="group"><?php e("{$cur_shipment->origin->name} [{$cur_shipment->origin->groupname}]"); ?></a></td>
						<td><a data-entity="<?php e($cur_shipment->shipper->guid); ?>" data-entity-context="com_sales_shipper"><?php e($cur_shipment->shipper->name); ?></a></td>
						<td><?php echo $cur_shipment->final ? ($cur_shipment->finished ? 'Received' : (empty($cur_shipment->received) ? 'Not Received' : 'Partially Received')) : 'Not Committed'; ?></td>
						<td><?php
						$names = array();
						foreach ((array) $cur_shipment->products as $cur_product)
							$names[] = '<a data-entity="'.h($cur_product->guid).'" data-entity-context="com_sales_product">'.h("{$cur_product->name} [{$cur_product->sku}]").'</a>';
						echo implode(', ', $names);
						?></td>
						<td><?php e($cur_shipment->comments); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="p_muid_shipments" name="shipments" value="[]" />
	</div>
	<div class="pf-element pf-heading">
		<h3>Products to be Received</h3>
	</div>
	<div class="pf-element pf-full-width">
		<div class="pf-field">
			<table id="p_muid_products_table">
				<thead>
					<tr>
						<th>Product Code</th>
						<th>Serial</th>
						<th>Quantity</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="p_muid_products" name="products" />
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-primary" type="button" onclick="if (confirm('Are all of the product serials correct?')) $('#p_muid_form').submit();" value="Submit" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url())); ?>);" value="Cancel" />
	</div>
</form>