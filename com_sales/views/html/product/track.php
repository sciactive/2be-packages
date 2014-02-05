<?php
/**
 * Provides a report of a product's history.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Stock Tracking';
$this->note = count($this->transactions).' transaction(s) for '.count($this->stock).' item(s) found.';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/product/track']);
$_->com_jstree->load();
?>
<style type="text/css" >
	#p_muid_grid a {
		text-decoration: underline;
	}
</style>
<script type="text/javascript">
	$_(function(){
		var submit_url = <?php echo json_encode(pines_url('com_sales', 'product/track')); ?>;
		var submit_search = function(){
			if ($("#p_muid_types_dialog [name=types_invoice]").attr('checked'))
				types.push('invoice');
			if ($("#p_muid_types_dialog [name=types_return]").attr('checked'))
				types.push('return');
			if ($("#p_muid_types_dialog [name=types_swap]").attr('checked'))
				types.push('swap');
			if ($("#p_muid_types_dialog [name=types_transfer]").attr('checked'))
				types.push('transfer');
			if ($("#p_muid_types_dialog [name=types_po]").attr('checked'))
				types.push('po');
			if ($("#p_muid_types_dialog [name=types_countsheet]").attr('checked'))
				types.push('countsheet');
			// Submit the form with all of the fields.
			$_.get(submit_url, {
				"serial": serial_box.val(),
				"sku": sku_box.val(),
				"types": types,
				"location": location,
				"descendants": descendants,
				"all_time": all_time,
				"start_date": start_date,
				"end_date": end_date
			});
		};

		var serial_box, sku_box;
		var types = new Array();
		// Timespan Defaults
		var all_time = <?php echo $this->all_time ? 'true' : 'false'; ?>;
		var start_date = <?php echo $this->start_date ? json_encode(format_date($this->start_date, 'date_sort')) : '""'; ?>;
		var end_date = <?php echo $this->end_date ? json_encode(format_date($this->end_date - 1, 'date_sort')) : '""'; ?>;
		// Location Defaults
		var location = <?php echo json_encode("{$this->location->guid}"); ?>;
		var descendants = <?php echo $this->descendants ? 'true' : 'false'; ?>;

		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents : [
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){history_grid.location_form();}},
				{type: 'button', title: 'Timespan', extra_class: 'picon picon-view-time-schedule', selection_optional: true, click: function(){history_grid.date_form();}},
				{type: 'button', title: 'Transactions', extra_class: 'picon picon-view-choose', selection_optional: true, click: function(){history_grid.types_form();}},
				{type: 'separator'},
				{type: 'text', label: 'SKU: ', load: function(textbox){
					// Display the current sku being searched.
					textbox.val(<?php echo json_encode($this->sku); ?>);
					textbox.keydown(function(e){
						if (e.keyCode == 13)
							submit_search();
					});
					sku_box = textbox;
				}},
				{type: 'separator'},
				{type: 'text', label: 'Serial #: ', load: function(textbox){
					// Display the current serial being searched.
					textbox.val(<?php echo json_encode($this->serial); ?>);
					textbox.keydown(function(e){
						if (e.keyCode == 13)
							submit_search();
					});
					serial_box = textbox;
				}},
				{type: 'separator'},
				{type: 'button', title: 'Update', extra_class: 'picon picon-view-refresh', selection_optional: true, click: submit_search}
			],
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/product/track", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var history_grid = $("#p_muid_grid").pgrid(cur_options);

		history_grid.date_form = function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_sales', 'forms/dateselect')); ?>,
				type: "POST",
				dataType: "html",
				data: {"all_time": all_time, "start_date": start_date, "end_date": end_date},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured while trying to retrieve the date form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
				},
				success: function(data){
					if (data == "")
						return;
					$_.pause();
					var form = $("<div title=\"Date Selector\"></div>").html(data+"<br />").dialog({
						bgiframe: true,
						autoOpen: true,
						modal: true,
						close: function(){
							form.remove();
						},
						buttons: {
							"Done": function(){
								if (form.find(":input[name=timespan_saver]").val() == "alltime") {
									all_time = true;
								} else {
									all_time = false;
									start_date = form.find(":input[name=start_date]").val();
									end_date = form.find(":input[name=end_date]").val();
								}
								form.dialog('close');
							}
						}
					});
					$_.play();
				}
			});
		};
		history_grid.location_form = function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_sales', 'forms/locationselect')); ?>,
				type: "POST",
				dataType: "html",
				data: {"location": location, "descendants": descendants},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured while trying to retrieve the location form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
				},
				success: function(data){
					if (data == "")
						return;
					$_.pause();
					var form = $("<div title=\"Location Selector\"></div>").html(data+"<br />").dialog({
						bgiframe: true,
						autoOpen: true,
						modal: true,
						close: function(){
							form.remove();
						},
						buttons: {
							"Done": function(){
								location = form.find(":input[name=location]").val();
								if (form.find(":input[name=descendants]").attr('checked'))
									descendants = true;
								else
									descendants = false;
								form.dialog('close');
							}
						}
					});
					$_.play();
				}
			});
		};
		history_grid.types_form = function(row){
			var types_dialog = $("#p_muid_types_dialog").dialog({
				bgiframe: true,
				autoOpen: false,
				modal: true,
				width: 250,
				buttons: {
					'Done': function() {
						types_dialog.dialog('close');
					}
				}
			});
			types_dialog.dialog('open');
		};
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Created Date</th>
			<th>Stock</th>
			<th>SKU</th>
			<th>Product</th>
			<th>Location</th>
			<th>Transaction #</th>
			<th>Type</th>
			<th>Status</th>
			<th>Qty</th>
			<th>Serials</th>
		</tr>
	</thead>
	<tbody>
	<?php
	foreach ($this->transactions as $cur_transaction) {
		switch ($cur_transaction->type) {
			case 'sale':
				$link = $cur_transaction->entity->guid;
				$context = 'com_sales_sale';
				$group = $cur_transaction->entity->group;
				$quantity = count($cur_transaction->entity->products);
				$serials = implode(', ', $cur_transaction->serials);
				break;
			case 'return':
				$link = $cur_transaction->entity->guid;
				$context = 'com_sales_return';
				$group = $cur_transaction->entity->group;
				$quantity = count($cur_transaction->entity->products);
				$serials = implode(', ', $cur_transaction->serials);
				break;
			case 'swap':
				$link = $cur_transaction->entity->ticket->guid;
				$context = 'com_sales_sale';
				$group = $cur_transaction->entity->group;
				$quantity = $cur_transaction->qty;
				$serials = implode(', ', $cur_transaction->serials);
				break;
			case 'countsheet':
				$link = $cur_transaction->entity->guid;
				$context = 'com_sales_countsheet';
				$group = $cur_transaction->entity->group;
				$quantity = count($cur_transaction->entity->products);
				$serials = '';
				break;
			case 'transfer':
				$link = $cur_transaction->entity->guid;
				$context = 'com_sales_transfer';
				$group = $cur_transaction->entity->destination;
				$quantity = $cur_transaction->qty;
				$serials = implode(', ', $cur_transaction->serials);
				break;
			case 'po':
				$link = $cur_transaction->entity->guid;
				$context = 'com_sales_po';
				$group = $cur_transaction->entity->destination;
				$quantity = $cur_transaction->qty;
				$serials = implode(', ', $cur_transaction->serials);
				break;
			default:
				$link = '';
				$context = '';
				$group = $cur_transaction->entity->group;
				$quantity = '';
				$serials = '';
				break;
		}
	?>
		<tr title="<?php e($cur_transaction->entity->guid); ?>">
			<td><?php e(format_date($cur_transaction->entity->p_cdate)); ?></td>
			<td><a data-entity="<?php e($cur_transaction->stock->guid); ?>" data-entity-context="com_sales_stock"><?php e($cur_transaction->stock->guid); ?></a></td>
			<td><?php e($cur_transaction->product->sku); ?></td>
			<td><a data-entity="<?php e($cur_transaction->product->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_transaction->product->name); ?></a></td>
			<td><a data-entity="<?php e($group->guid); ?>" data-entity-context="group"><?php e("{$group->name} [{$group->groupname}]"); ?></a></td>
			<td><a data-entity="<?php e($link); ?>" data-entity-context="<?php e($context); ?>"><?php e($cur_transaction->entity->guid); ?></a></td>
			<td><?php e(ucwords($cur_transaction->type)); ?></td>
			<td><?php e($cur_transaction->transaction_info); ?></td>
			<td><?php e($quantity); ?></td>
			<td><?php e($serials); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<div id="p_muid_types_dialog" title="Transaction Types" style="display: none;">
	<div class="pf-form">
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">Invoices</span>
				<input class="pf-field" type="checkbox" name="types_invoice" value="ON"<?php echo $this->types['invoice'] ? ' checked="checked"' : ''; ?> /></label>
		</div>
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">Returns</span>
				<input class="pf-field" type="checkbox" name="types_return" value="ON"<?php echo $this->types['return'] ? ' checked="checked"' : ''; ?> /></label>
		</div>
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">Swaps</span>
				<input class="pf-field" type="checkbox" name="types_swap" value="ON"<?php echo $this->types['swap'] ? ' checked="checked"' : ''; ?> /></label>
		</div>
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">Transfers</span>
				<input class="pf-field" type="checkbox" name="types_transfer" value="ON"<?php echo $this->types['transfer'] ? ' checked="checked"' : ''; ?> /></label>
		</div>
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">POs</span>
				<input class="pf-field" type="checkbox" name="types_po" value="ON"<?php echo $this->types['po'] ? ' checked="checked"' : ''; ?> /></label>
		</div>
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">Countsheets</span>
				<input class="pf-field" type="checkbox" name="types_countsheet" value="ON"<?php echo $this->types['countsheet'] ? ' checked="checked"' : ''; ?> /></label>
		</div>
	</div>
	<br />
</div>