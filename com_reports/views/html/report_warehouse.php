<?php
/**
 * Shows a warehouse item report.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$this->title = 'Warehouse Items ['.h($this->location->name).']';
if (!$this->all_time)
	$this->note = h(format_date($this->start_date, 'date_short')).' - '.h(format_date($this->end_date - 1, 'date_short'));

$_->icons->load();
$_->com_jstree->load();
$_->com_pgrid->load();
$google_drive = false;
if (isset($_->com_googledrive)) {
    $_->com_googledrive->export_to_drive('csv');
    $google_drive = true;
} else {
    pines_log("Google Drive is not installed", 'notice');
}
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_reports/report_warehouse']);
?>
<script type="text/javascript">
	pines(function(){
		pines.search_details = function(){
			// Submit the form with all of the fields.
			pines.get(<?php echo json_encode(pines_url('com_reports', 'reportwarehouse')); ?>, {
				"location": location,
				"descendants": descendants,
				"all_time": all_time,
				"start_date": start_date,
				"end_date": end_date
			});
		};

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
			pgrid_toolbar_contents: [
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){warehouse_grid.location_form();}},
				{type: 'button', title: 'Timespan', extra_class: 'picon picon-view-time-schedule', selection_optional: true, click: function(){warehouse_grid.date_form();}},
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'warehouse_items',
						content: rows
					});
				}},
                                <?php // Need to check if Google Drive is installed
                                    if ($google_drive && !empty($_->config->com_googledrive->client_id)) { ?>
                                        {type: 'button', title: 'Export to Google Drive', extra_class: 'picon drive-icon', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
                                        // First need to set the rows to which we want to export
                                        setRows(rows);
                                        // Then we have to check if we have permission to post to user's google drive
                                        checkAuth();
                                    }},
                                    <?php } elseif ($google_drive && empty($_->config->com_googledrive->client_id)) { ?>
                                        {type: 'button', title: 'Export to Google Drive', extra_class: 'picon drive-icon', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
                                        // They have com_googledrive installed but didn't set the client id, so alert them on click
                                        alert('You need to set the CLIENT ID before you can export to Google Drive');
                                    }},
                                    <?php } ?>
			],
			pgrid_sortable: true,
			pgrid_sort_col: 2,
			pgrid_sort_ord: "desc",
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_reports/report_warehouse", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		cur_options.pgrid_sort_col = false;
		var warehouse_grid = $("#p_muid_grid").pgrid(cur_options);

		warehouse_grid.date_form = function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_reports', 'dateselect')); ?>,
				type: "POST",
				dataType: "html",
				data: {"all_time": all_time, "start_date": start_date, "end_date": end_date},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to retrieve the date form:\n"+pines.safe(XMLHttpRequest.status)+": "+pines.safe(textStatus));
				},
				success: function(data){
					if (data == "")
						return;
					pines.pause();
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
								pines.search_details();
							}
						}
					});
					pines.play();
				}
			});
		};
		warehouse_grid.location_form = function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_reports', 'locationselect')); ?>,
				type: "POST",
				dataType: "html",
				data: {"location": location, "descendants": descendants},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to retrieve the location form:\n"+pines.safe(XMLHttpRequest.status)+": "+pines.safe(textStatus));
				},
				success: function(data){
					if (data == "")
						return;
					pines.pause();
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
								pines.search_details();
							}
						}
					});
					pines.play();
				}
			});
		};
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>ID</th>
			<th>Date</th>
			<th>Transaction</th>
			<th>Delivery</th>
			<th>Location</th>
			<th>Employee</th>
			<th>Customer</th>
			<th>SKU</th>
			<th>Serial</th>
			<th>Product</th>
			<th>Unit Cost</th>
			<th>Price</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->transactions as $cur_tx) {
			if (empty($cur_tx->products))
				continue;
			foreach ($cur_tx->products as $cur_item) {
				if (!empty($cur_item['serial']) || $cur_item['delivery'] != 'warehouse' || !empty($cur_item['returned_quantity']))
					continue;
			?>
			<tr title="<?php e($cur_tx->customer->guid); ?>">
				<td><a data-entity="<?php e($cur_tx->guid); ?>" data-entity-context="com_sales_sale"><?php e($cur_tx->id); ?></a></td>
				<td><?php e(format_date($cur_tx->p_cdate)); ?></td>
				<td><?php e(ucwords($cur_tx->status)); ?></td>
				<td><?php e($cur_item['delivery']); ?></td>
				<td><a data-entity="<?php e($cur_tx->group->guid); ?>" data-entity-context="group"><?php e($cur_tx->group->name); ?></a></td>
				<td><a data-entity="<?php e($cur_tx->user->guid); ?>" data-entity-context="user"><?php e($cur_tx->user->name); ?></a></td>
				<td><a data-entity="<?php e($cur_tx->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e($cur_tx->customer->name); ?></a></td>
				<td><?php e($cur_item['sku']); ?></td>
				<td><?php e($cur_item['serial']); ?></td>
				<td><a data-entity="<?php e($cur_item['entity']->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_item['entity']->name); ?></a></td>
				<td>$<?php echo round($cur_item['entity']->vendors[0]['cost'], 2); ?></td>
				<td>$<?php echo round($cur_item['price'], 2); ?></td>
			</tr>
		<?php }
		} ?>
	</tbody>
</table>