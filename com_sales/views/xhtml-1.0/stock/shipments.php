<?php
/**
 * Lists shipments and provides functions to manipulate them.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = ($this->removed ? 'Completed ' : 'Pending ').'Shipments';
if (isset($this->location))
	$this->title .= " at and below {$this->location->name} [{$this->location->groupname}]";
$pines->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = $_SESSION['user']->pgrid_saved_states['com_sales/stock/shipments'];
$pines->com_jstree->load();
?>
<script type="text/javascript">
	// <![CDATA[

	pines(function(){
		var submit_url = "<?php echo addslashes(pines_url('com_sales', 'stock/shipments')); ?>";
		var submit_search = function(){
			// Submit the form with all of the fields.
			pines.post(submit_url, {
				"location": location
			});
		};

		// Location Defaults
		var location = "<?php echo $this->location->guid; ?>";

		var state_xhr;
		var cur_state = JSON.parse("<?php echo (isset($this->pgrid_state) ? addslashes($this->pgrid_state) : '{}');?>");
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (!$this->removed) { ?>
				{type: 'button', text: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){shipments_grid.location_form();}},
				{type: 'separator'},
				<?php } if (gatekeeper('com_sales/managestock')) { ?>
				{type: 'button', text: 'Edit/Ship', extra_class: 'picon picon-document-edit', double_click: true, url: '<?php echo addslashes(pines_url('com_sales', 'stock/ship', array('type' => '__col_1__', 'id' => '__title__'))); ?>'},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post("<?php echo addslashes(pines_url('system', 'csv')); ?>", {
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
				state_xhr = $.post("<?php echo addslashes(pines_url('com_pgrid', 'save_state')); ?>", {view: "com_sales/stock/shipments", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var shipments_grid = $("#p_muid_grid").pgrid(cur_options);

		shipments_grid.location_form = function(){
			$.ajax({
				url: "<?php echo addslashes(pines_url('com_sales', 'forms/locationselect')); ?>",
				type: "POST",
				dataType: "html",
				data: {"location": location},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to retreive the location form:\n"+XMLHttpRequest.status+": "+textStatus);
				},
				success: function(data){
					if (data == "")
						return;
					var form = $("<div title=\"Location Selector\" />");
					form.dialog({
						bgiframe: true,
						autoOpen: true,
						height: 250,
						modal: true,
						open: function(){
							form.html(data);
						},
						close: function(){
							form.remove();
						},
						buttons: {
							"Update": function(){
								location = form.find(":input[name=location]").val();
								form.dialog('close');
								submit_search();
							}
						}
					});
				}
			});
		};
	});

	// ]]>
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Type</th>
			<th>ID/Number</th>
			<?php if (!$this->removed) { ?>
			<th>Location</th>
			<?php } ?>
			<th>Destination</th>
			<th>Shipper</th>
			<th>Tracking #</th>
			<th>ETA</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->sales as $sale) { ?>
		<tr title="<?php echo $sale->guid; ?>">
			<td>Sale</td>
			<td><?php echo htmlspecialchars($sale->id); ?></td>
			<?php if (!$this->removed) { ?>
			<td><?php echo htmlspecialchars("{$sale->group->name} [{$sale->group->groupname}]"); ?></td>
			<?php } ?>
			<td><?php echo htmlspecialchars("{$sale->shipping_address->name} (Customer: {$sale->customer->name})"); ?></td>
			<td><?php echo htmlspecialchars($sale->shipper->name); ?></td>
			<td><?php echo htmlspecialchars(isset($sale->tracking_numbers) ? implode(', ', $sale->tracking_numbers) : ''); ?></td>
			<td><?php echo isset($sale->eta) ? format_date($sale->eta, 'date_sort') : '' ; ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>