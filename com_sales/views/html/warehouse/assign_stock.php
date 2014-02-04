<?php
/**
 * Provides a form for the user to assign stock to warehouse orders.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Assign Stock';
$this->note = 'Provide stock selection in this form.';
$_->com_jstree->load();
$warehouse = group::factory($_->config->com_sales->warehouse_group);
?>
<form class="pf-form" id="p_muid_form" method="post" action="<?php e(pines_url('com_sales', 'warehouse/assignstocksave')); ?>">
	<style type="text/css">
		#p_muid_form .products .entry {
			float: left;
			font-size: .8em;
			padding: .5em;
			margin: 0 1em 1em 0;
		}
		#p_muid_form .products .entry > div {
			margin-right: 2em;
		}
		#p_muid_form .products .entry .remove {
			cursor: pointer;
			font-weight: bold;
			float: right;
		}
	</style>
	<script type="text/javascript">
		pines(function(){
			var current_item;

			$("#p_muid_form").delegate(".products div.serial a.assign", "click", function(){
				current_item = $(this).closest("div.serial");
				serial_dialog.dialog("open");
			}).delegate(".products div.nonserial a.assign", "click", function(){
				current_item = $(this).closest("div.nonserial");
				quantity_box.val(current_item.find(".qty_left").text());
				location_dialog.dialog("open");
			}).delegate(".products .entry a.remove", "click", function(){
				$(this).closest(".entry").remove();
				refresh_entries();
			});

			var add_entry = function(serial, location, quantity){
				var guids = [];
				$("#p_muid_form .products .guid").each(function(){
					guids.push($(this).text());
				});
				var loader;
				$.ajax({
					url: <?php echo json_encode(pines_url('com_sales', 'stock/search')); ?>,
					type: "POST",
					dataType: "json",
					data: {"not_guids": JSON.stringify(guids), "product": current_item.children(".product").text(), "serial": serial, "location": location, "quantity": quantity},
					beforeSend: function(){
						loader = $.pnotify({
							title: 'Stock Search',
							text: 'Retrieving stock from server...',
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
						pines.error("An error occured while trying to lookup the stock:\n"+pines.safe(XMLHttpRequest.status)+": "+pines.safe(textStatus));
					},
					success: function(data){
						if (!data) {
							alert("An error occured. Please try again.");
							return;
						}
						if (data.length == 0) {
							alert("No stock was found that matched your query.");
							return;
						}
						if (data.length < quantity)
							alert("Only "+data.length+" items were found that matched your query.");
						var entries = current_item.find(".entries");
						$.each(data, function(i, entry){
							entries.append('<div class="well entry"><a href="javascript:void(0);" class="remove btn btn-mini btn-danger">&nbsp;X&nbsp;</a><div>Stock Entry: <a data-entity="'+pines.safe(entry.guid)+'" data-entity-context="com_sales_stock" class="guid">'+pines.safe(entry.guid)+'</a></div><div>Location: <a data-entity="'+pines.safe(entry.location)+'" data-entity-context="group">'+pines.safe(entry.location_name)+'</a></div><span class="location" style="display: none">'+pines.safe(entry.location)+'</span><span class="product" style="display: none">'+pines.safe(entry.product)+'</span><div'+(entry.serial ? '' : ' style="display: none"')+'>Serial: <span class="serial">'+(entry.serial ? pines.safe(entry.serial) : '')+'</span></div></div>');
						});
						refresh_entries();
					}
				});
			};

			var refresh_entries = function(){
				var entries = [];
				$("#p_muid_form .products").children(".product_entry").each(function(){
					var cur_product = $(this),
						cur_entries = cur_product.find(".entries .entry"),
						sale = cur_product.children(".sale").text(),
						key = cur_product.children(".key").text(),
						// Update the quantity left.
						qty_left = parseInt(cur_product.find(".qty").text()) - (parseInt(cur_product.find(".qty_done").text()) + cur_entries.length);
					cur_product.find(".qty_left").html(qty_left);
					if (qty_left < 1)
						cur_product.find(".assign").hide();
					else
						cur_product.find(".assign").show();
					cur_entries.each(function(){
						var cur_entry = $(this);
						entries.push({
							"sale": sale,
							"key": key,
							"guid": cur_entry.find(".guid").text()
						});
					})
				});
				$("#p_muid_items").val(JSON.stringify(entries));
			};
			refresh_entries();

			var serial_dialog = $("#p_muid_serial_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				width: 450,
				modal: true,
				buttons: {
					"Done": function(){
						var serial = serial_box.val();
						if (serial == "") {
							alert("Please provide a serial number.");
							return;
						}
						add_entry(serial, null, 1);
						serial_dialog.dialog("close");
					}
				},
				close: function(){
					serial_box.val("");
				},
				open: function(){
					serial_box.focus().select();
				}
			});
			var serial_box = $("#p_muid_serial_number").keypress(function(e){
				if (e.keyCode == 13) {
					serial_dialog.dialog("option", "buttons").Done();
					return false;
				}
			});

			//var location_rollback;
			var location_dialog = $("#p_muid_location_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				width: 450,
				modal: true,
				buttons: {
					"Done": function(){
						var quantity = quantity_box.val();
						if (quantity == "") {
							alert("Please provide a quantity.");
							return;
						}
						quantity = parseInt(quantity);
						if (isNaN(quantity) || quantity < 1) {
							alert("Given quantity is invalid.");
							return;
						}
						var qty_left = parseInt(current_item.find(".qty_left").text());
						if (quantity > qty_left) {
							alert("Given quantity is too high. Only "+qty_left+" left to be assigned.");
							return;
						}
						add_entry("", location.val(), quantity);
						location_dialog.dialog("close");
					}
				},
				// This works, but it makes selecting the same node again more difficult.
				//close: function(){
				//	$.jstree.rollback(location_rollback);
				//},
				open: function(){
					//location_rollback = location_tree.jstree("get_rollback");
					location_tree.jstree("reopen");
					location_tree.jstree("set_focus");
				}
			});
			var quantity_box = $("#p_muid_quantity").keypress(function(e){
				if (e.keyCode == 13) {
					location_dialog.dialog("option", "buttons").Done();
					return false;
				}
			});
			// Location Tree
			var location = $("#p_muid_location_dialog [name=location]");
			var location_tree = $("#p_muid_location_dialog div.location_tree");
			location_tree
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
					"initially_select" : [<?php echo json_encode("{$warehouse->guid}"); ?>]
				}
			});
		});
	</script>
	<?php foreach ($this->items as $cur_item) { ?>
	<div class="pf-element pf-heading">
		<h3><a data-entity="<?php e($cur_item['sale']->guid); ?>" data-entity-context="com_sales_sale">Sale <?php e($cur_item['sale']->id); ?></a></h3>
		<p>
			<span style="float: left; clear: left; padding-right: 2em;">Tendered: <?php e(format_date($cur_item['sale']->tender_date, 'full_long')); ?>.</span>
			<span style="float: left; padding-right: 2em;">Location: <a data-entity="<?php e($cur_item['sale']->group->guid); ?>" data-entity-context="group"><?php e("{$cur_item['sale']->group->name} [{$cur_item['sale']->group->groupname}]"); ?></a>.</span>
			<span style="float: left; padding-right: 2em;">Salesperson: <a data-entity="<?php e($cur_item['sale']->user->guid); ?>" data-entity-context="user"><?php e("{$cur_item['sale']->user->name} [{$cur_item['sale']->user->username}]"); ?></a>.</span>
			<span style="float: left;">Customer: <a data-entity="<?php e($cur_item['sale']->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e($cur_item['sale']->customer->name); ?></a>.</span>
		</p>
	</div>
	<div class="ui-helper-clearfix products">
		<?php foreach ($cur_item['products'] as $cur_key => $cur_product) {
			if ($cur_product['delivery'] != 'warehouse')
				continue;
			// Is the product returned?
			if ((int) $cur_product['returned_quantity'] >= $cur_product['quantity'])
				continue;
			// Calculate quantity.
			$quantity = $cur_product['quantity'] - (int) $cur_product['returned_quantity'];
			// Calculate assigned.
			$assigned = count($cur_product['stock_entities']) - count((array) $cur_product['returned_stock_entities']);
			?>
		<div class="pf-element pf-full-width product_entry <?php echo $cur_product['entity']->serialized ? 'serial' : 'nonserial'; ?>">
			<span class="pf-label">
				<a data-entity="<?php e($cur_product['entity']->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_product['entity']->name); ?></a> [SKU: <?php e($cur_product['entity']->sku); ?>]
				<span class="pf-note"><small>x <span class="qty"><?php e($quantity); ?></span><span class="qty_left" style="display: none;"><?php echo $quantity - $assigned; ?></span> (<span class="qty_done"><?php echo $assigned; ?></span> already assigned)</small></span>
			</span>
			<a href="javascript:void(0);" class="pf-field assign">Assign</a>
			<div class="pf-field pf-full-width ui-helper-clearfix entries"></div>
			<br class="pf-clearing" />
			<div class="product" style="display: none;"><?php e($cur_product['entity']->guid); ?></div>
			<div class="sale" style="display: none;"><?php e($cur_item['sale']->guid); ?></div>
			<div class="key" style="display: none;"><?php e($cur_key); ?></div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
	<div id="p_muid_serial_dialog" title="Provide Serial" style="display: none;">
		<div class="pf-form">
			<div class="pf-element">
				<label><span class="pf-label">Serial Number</span>
					<input class="pf-field" type="text" id="p_muid_serial_number" name="serial_number" size="24" value="" /></label>
			</div>
		</div>
		<br />
	</div>
	<div id="p_muid_location_dialog" title="Pick Location" style="display: none;">
		<div class="pf-form">
			<div class="pf-element">
				<span class="pf-label">Location</span>
				<div class="pf-group">
					<div class="pf-field location_tree"></div>
				</div>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Quantity</span>
					<input class="pf-field" type="text" id="p_muid_quantity" name="quantity" size="5" value="" /></label>
			</div>
			<input type="hidden" name="location" value="<?php e($warehouse->guid); ?>" />
		</div>
		<br />
	</div>
	<div class="pf-element pf-buttons">
		<input type="hidden" name="items" id="p_muid_items" value="[]" />
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn" type="button" onclick="pines.get(<?php e(json_encode(pines_url('com_sales', 'warehouse/pending'))); ?>);" value="Cancel" />
	</div>
</form>