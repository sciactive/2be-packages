<?php
/**
 * Shows a list of totals by location.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$this->title = 'Location Summary ['.h($this->location->name).']';
if ($this->descendants)
	$this->note = 'Including locations beneath '.h($this->location->name);
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
?>
<style type="text/css">
	.p_muid_location_actions button {
		padding: 0;
	}
	.p_muid_location_actions button .ui-button-text {
		padding: 0;
	}
	.p_muid_btn {
		display: inline-block;
		width: 16px;
		height: 16px;
	}
	#p_muid_grid th {
		text-align: center;
	}
	#p_muid_grid .total {
		text-align: right;
	}
</style>
<script type="text/javascript">
	var p_muid_notice;

	pines(function(){
		search_locations = function(){
			// Submit the form with all of the fields.
			pines.get(<?php echo json_encode(pines_url('com_reports', 'locationsummary')); ?>, {
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

		var locations_grid = $("#p_muid_grid").pgrid({
			pgrid_toolbar: true,
			pgrid_sortable: true,
			pgrid_sort_col: 10,
			pgrid_sort_ord: 'desc',
			pgrid_toolbar_contents: [
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){locations_grid.location_form();}},
				{type: 'button', title: 'Timespan', extra_class: 'picon picon-view-time-schedule', selection_optional: true, click: function(){locations_grid.date_form();}},
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'location_summary',
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
			]
		});

		locations_grid.date_form = function(){
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
								search_locations();
							}
						}
					});
					pines.play();
				}
			});
		};
		locations_grid.location_form = function(){
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
								search_locations();
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
			<th>Location</th>
			<th># Sold</th>
			<th># Ref</th>
			<th># Net</th>
			<th>$ Sold</th>
			<th>$ Ref</th>
			<th>$ Net</th>
			<th>Adjustment</th>
			<th>Cost</th>
			<th>Profit</th>
			<th>Commission</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->invoices as $cur_invoice) {
			if ($cur_sale->status == 'voided')
				continue;
			if (!isset($totals[$cur_invoice->group->guid])){
				$totals[$cur_invoice->group->guid] = array(
					'location' => $cur_invoice->group,
					'qty_sold' => 0,
					'qty_returned' => 0,
					'qty_net' => 0,
					'total_sold' => 0,
					'total_returned' => 0,
					'total_net' => 0,
					'adjustment' => 0,
					'cost' => 0,
					'profit' => 0,
					'commission' => 0
				);
				$commissions = array();
			}
			if ($cur_invoice->has_tag('sale')) {
				$totals[$cur_invoice->group->guid]['qty_sold']++;
				$totals[$cur_invoice->group->guid]['qty_net']++;
				$totals[$cur_invoice->group->guid]['total_sold'] += $cur_invoice->subtotal;
				$totals[$cur_invoice->group->guid]['total_net'] += $cur_invoice->subtotal;
				foreach ($cur_invoice->products as $cur_item) {
					foreach ($cur_item['stock_entities'] as $cur_stock)
						$totals[$cur_invoice->group->guid]['cost'] += $cur_stock->cost;
				}
				foreach ($cur_invoice->products as $cur_product) {
					foreach ($cur_product['salesperson']->commissions as $cur_commission) {
						if (!in_array($cur_commission, $commissions) && $cur_commission['ticket']->guid == $cur_invoice->guid) {
							$totals[$cur_invoice->group->guid]['commission'] += $cur_commission['amount'];
							$commissions[] = $cur_commission;
						}
					}
				}
			} elseif ($cur_invoice->has_tag('return')) {
				$totals[$cur_invoice->group->guid]['qty_returned']++;
				$totals[$cur_invoice->group->guid]['qty_net']--;
				$totals[$cur_invoice->group->guid]['total_returned'] += $cur_invoice->subtotal;
				$totals[$cur_invoice->group->guid]['total_net'] -= $cur_invoice->subtotal;
				foreach ($cur_invoice->products as $cur_product) {
					foreach ($cur_product['salesperson']->commissions as $cur_commission) {
						if (!in_array($cur_commission, $commissions) && $cur_commission['ticket']->guid == $cur_invoice->guid) {
							$totals[$cur_invoice->group->guid]['commission'] += $cur_commission['amount'];
							$commissions[] = $cur_commission;
						}
					}
				}
			}
		}
		foreach ($totals as $cur_total) {
			$cur_total['profit'] = ($cur_total['total_sold']-$cur_total['total_returned'])-$cur_total['cost'];
		?>
		<tr title="<?php e($cur_total['location']->guid); ?>">
			<td><?php e($cur_total['location']->name); ?></td>
			<td><?php e($cur_total['qty_sold']); ?></td>
			<td><?php e($cur_total['qty_returned']); ?></td>
			<td><?php e($cur_total['qty_net']); ?></td>
			<td class="total">$<?php echo number_format($cur_total['total_sold'], 2, '.', ''); ?></td>
			<td class="total">$<?php echo number_format($cur_total['total_returned'], 2, '.', ''); ?></td>
			<td class="total">$<?php echo number_format($cur_total['total_net'], 2, '.', ''); ?></td>
			<td class="total">$<?php echo number_format($cur_total['adjustment'], 2, '.', ''); ?></td>
			<td class="total">$<?php echo number_format($cur_total['cost'], 2, '.', ''); ?></td>
			<td class="total">$<?php echo number_format($cur_total['profit'], 2, '.', ''); ?></td>
			<td class="total">$<?php echo number_format($cur_total['commission'], 2, '.', ''); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>