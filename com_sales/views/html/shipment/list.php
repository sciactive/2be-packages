<?php
/**
 * Lists shipments and provides functions to manipulate them.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = ($this->removed ? 'Completed ' : 'Pending ').'Shipments';
if (isset($this->location))
	$this->title .= h(" out of {$this->location->name} [{$this->location->groupname}]");
$this->note = $this->descendants ? 'Including Descendant Locations' : '';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/shipment/list']);
$_->com_jstree->load();
?>
<script type="text/javascript">
	$_(function(){
		var submit_url = <?php echo json_encode(pines_url('com_sales', 'shipment/list')); ?>;
		var submit_search = function(){
			// Submit the form with all of the fields.
			$_.get(submit_url, {
				"location": location,
				"descendants": descendants
			});
		};

		// Location Defaults
		var location = <?php echo json_encode("{$this->location->guid}"); ?>;
		var descendants = <?php echo $this->descendants ? 'true' : 'false'; ?>;

		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (!$this->removed) { ?>
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){shipments_grid.location_form();}},
				{type: 'separator'},
				<?php } if (gatekeeper('com_sales/managestock')) { ?>
				{type: 'button', text: 'Packing List', extra_class: 'picon picon-preflight-verifier', double_click: true, url: <?php echo json_encode(pines_url('com_sales', 'shipment/packinglist', array('id' => '__title__'))); ?>},
				{type: 'button', text: 'Ship', extra_class: 'picon picon-mail-send', url: <?php echo json_encode(pines_url('com_sales', 'shipment/ship', array('id' => '__title__'))); ?>},
				{type: 'button', text: 'Delivered', title: 'Toggle the delivered status of a shipment.', extra_class: 'picon picon-mail-read', url: <?php echo json_encode(pines_url('com_sales', 'shipment/delivered', array('id' => '__title__'))); ?>},
				<?php } ?>
				{type: 'separator'},
				<?php if (!$this->removed) { ?>
				{type: 'button', text: 'Completed', extra_class: 'picon picon-vcs-removed', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'shipment/list', array('removed' => 'true'))); ?>},
				<?php } else { ?>
				{type: 'button', text: 'Pending', extra_class: 'picon picon-vcs-normal', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'shipment/list')); ?>},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'shipments',
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
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/shipment/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var shipments_grid = $("#p_muid_grid").pgrid(cur_options);

		shipments_grid.location_form = function(){
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
							"Update": function(){
								location = form.find(":input[name=location]").val();
								if (form.find(":input[name=descendants]").attr('checked'))
									descendants = true;
								else
									descendants = false;
								form.dialog('close');
								submit_search();
							}
						}
					});
					$_.play();
				}
			});
		};
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>ID</th>
			<th>Reference</th>
			<th>Status</th>
			<th>Date</th>
			<th>Location</th>
			<th>Destination</th>
			<th>Shipper</th>
			<th>ETA</th>
			<th>Tracking #</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach((array) $this->sales as $sale) { ?>
		<tr title="<?php e($sale->guid); ?>">
			<td><a data-entity="<?php e($sale->guid); ?>" data-entity-context="com_sales_sale"><?php e($sale->id); ?></a></td>
			<td>Sale <a href="javascript:void(0);" onclick="alert('Recent upgrades have changed the shipment system, and new sales will no longer show in this view. It will instead be just for shipments.');">(?)</a></td>
			<td><?php echo $sale->hasTag('shipping_shipped') ? 'Shipped' : 'Not Shipped'; ?></td>
			<td><?php e(format_date($sale->tender_date, 'full_sort')); ?></td>
			<td><a data-entity="<?php e($sale->group->guid); ?>" data-entity-context="group"><?php e("{$sale->group->name} [{$sale->group->groupname}]"); ?></a></td>
			<td><?php e($sale->shipping_address->name); ?><?php if (isset($sale->customer->guid)) { ?> (<a data-entity="<?php e($sale->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e("{$sale->customer->guid}: {$sale->customer->name}"); ?></a>)<?php } ?></td>
			<td><a data-entity="<?php e($sale->shipper->guid); ?>" data-entity-context="com_sales_shipper"><?php e($sale->shipper->name); ?></a></td>
			<td><?php echo $sale->eta ? h(format_date($sale->eta, 'date_sort')) : '' ; ?></td>
			<td><?php e(isset($sale->tracking_numbers) ? implode(', ', $sale->tracking_numbers) : ''); ?></td>
		</tr>
	<?php } foreach((array) $this->shipments as $shipment) {
		if ($shipment->ref->hasTag('sale'))
			$ref_class = 'com_sales_sale';
		elseif ($shipment->ref->hasTag('transfer'))
			$ref_class = 'com_sales_transfer';
		else
			$ref_class = 'Entity';
		?>
		<tr title="<?php e($shipment->guid); ?>">
			<td><a data-entity="<?php e($shipment->guid); ?>" data-entity-context="com_sales_shipment"><?php e($shipment->id); ?></a></td>
			<td><a data-entity="<?php e($shipment->ref->guid); ?>" data-entity-context="<?php e($ref_class); ?>"><?php e($shipment->ref->info('name')); ?></a></td>
			<td><?php echo $shipment->shipped ? 'Shipped' : 'Not Shipped'; ?> - <?php echo $shipment->delivered ? 'Delivered' : 'In Transit'; ?></td>
			<td><?php e(format_date($shipment->cdate, 'full_sort')); ?></td>
			<td><a data-entity="<?php e($shipment->group->guid); ?>" data-entity-context="group"><?php e("{$shipment->group->name} [{$shipment->group->groupname}]"); ?></a></td>
			<td><?php
				e($shipment->shipping_address->name);
				if (isset($shipment->ref->customer->guid)) {
					?> (<a data-entity="<?php e($shipment->ref->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e("{$shipment->ref->customer->guid}: {$shipment->ref->customer->name}"); ?></a>)<?php
				} elseif (isset($shipment->ref->destination->guid)) {
					?> (<a data-entity="<?php e($shipment->ref->destination->guid); ?>" data-entity-context="group"><?php e("{$shipment->ref->destination->name} [{$shipment->ref->destination->groupname}]"); ?></a>)<?php
				}
			?></td>
			<td><a data-entity="<?php e($shipment->shipper->guid); ?>" data-entity-context="com_sales_shipper"><?php e($shipment->shipper->name); ?></a></td>
			<td><?php echo $shipment->eta ? h(format_date($shipment->eta, 'date_sort')) : '' ; ?></td>
			<td><?php
				if (isset($shipment->shipper->guid) && $shipment->shipper->can_track()) {
					$links = array();
					foreach ((array) $shipment->tracking_numbers as $cur_number)
						$links[] = '<a href="'.h($shipment->shipper->tracking_url($cur_number)).'" target="_blank">'.h($cur_number).'</a>';
					echo implode(', ', $links);
				} else
					e(isset($shipment->tracking_numbers) ? implode(', ', $shipment->tracking_numbers) : '');
			?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>