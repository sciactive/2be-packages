<?php
/**
 * Displays pending warehouse items.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = ($this->ordered ? 'Ordered' : 'New').' Pending Warehouse Orders';
if (isset($this->location)) {
	$this->title .= h(" at {$this->location->name} [{$this->location->groupname}]");
	if ($this->descendants)
		$this->title .= ' and Below';
}
if ($this->all_time) {
	$this->note = 'All time included.';
} elseif (isset($this->start_date) || isset($this->end_date)) {
	if (isset($this->start_date))
		$this->note = h(format_date($this->start_date, 'date_short')).' - ';
	else
		$this->note = 'Up to and including ';
	if (isset($this->end_date))
		$this->note .= h(format_date($this->end_date - 1, 'date_short')).'.';
	else
		$this->note .= ' and beyond.';
}
$_->com_pgrid->load();
$_->com_jstree->load();
$google_drive = false;
if (isset($_->com_googledrive)) {
    $_->com_googledrive->export_to_drive('csv');
    $google_drive = true;
} else {
    pines_log("Google Drive is not installed", 'notice');
}

if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/warehouse/pending']);
?>
<script type="text/javascript">
	$_(function(){
		var submit_url = <?php echo json_encode(pines_url('com_sales', 'warehouse/pending', array('ordered' => ($this->ordered ? 'true' : 'false')))); ?>;
		var submit_search = function(){
			// Submit the form with all of the fields.
			$_.get(submit_url, {
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
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){pending_grid.location_form();}},
				{type: 'button', title: 'Timespan', extra_class: 'picon picon-view-time-schedule', selection_optional: true, click: function(){pending_grid.date_form();}},
				{type: 'separator'},
				{type: 'button', text: 'Guide', title: 'See information about where current stock is available.', extra_class: 'picon picon-view-calendar-tasks', double_click: true, click: function(e, rows){
					var loader;
					$.ajax({
						url: <?php echo json_encode(pines_url('com_sales', 'warehouse/pending_info')); ?>,
						type: "POST",
						dataType: "html",
						data: {id: rows.attr("title")},
						beforeSend: function(){
							loader = $.pnotify({
								title: 'Stock Location Guide',
								text: 'Retrieving info...',
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
							$_.error("An error occured while trying to create guide:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
						},
						success: function(data){
							$_.pause();
							$("<div title=\"Stock Location Guide\"></div>").html(data+"<br />").dialog({
								modal: false,
								width: 800
							});
							$_.play();
						}
					});
				}},
				<?php if (gatekeeper('com_sales/warehouse')) { if (!$this->ordered) { ?>
				{type: 'button', text: 'Mark Ordered', extra_class: 'picon picon-task-complete', multi_select: true, confirm: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/markordered', array('id' => '__title__', 'ordered' => 'true'))); ?>, delimiter: ','},
				<?php } else { ?>
				{type: 'button', text: 'Mark Not Ordered', extra_class: 'picon picon-task-attempt', multi_select: true, confirm: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/markordered', array('id' => '__title__', 'ordered' => 'false'))); ?>, delimiter: ','},
				<?php } if (gatekeeper('com_sales/newpo')) { ?>
				{type: 'button', text: 'Create PO', extra_class: 'picon picon-resource-calendar-child', multi_select: true, click: function(e, rows){
					var problem = false,
						location = false,
						vendors = false;
					rows.each(function(i, v){
						if (problem)
							return;
						var row = $(v),
							row_po = $(row.pgrid_get_value(8)).filter('a[data-entity]').attr('data-entity'),
							row_loc = $(row.pgrid_get_value(3)).filter('a[data-entity]').attr('data-entity'),
							row_ven = $(row.pgrid_get_value(9)).filter('a[data-entity]').map(function(i,e){return $(e).attr('data-entity')}).get();
						if (row_po && row_po != '0') {
							alert('All selected orders must not have attached POs.');
							problem = true;
							return;
						}
						if (!location)
							location = row_loc;
						else if (location != row_loc) {
							alert('All selected orders must have the same location.');
							problem = true;
							return;
						}
						if (!vendors)
							vendors = row_ven;
						else {
							vendors = $.grep(vendors, function(n){
								return ($.inArray(n, row_ven) != -1);
							});
							if (!vendors.length) {
								alert('All selected orders must have at least one vendor in common.');
								problem = true;
								return;
							}
						}
					});
					if (problem)
						return;
					var ids = rows.map(function(i,e){return $(e).attr('title')}).get();
					$_.get(<?php echo json_encode(pines_url('com_sales', 'warehouse/createpo')); ?>, {id: ids.join(',')}, '_blank');
				}},
				<?php } ?>
				{type: 'button', text: 'Attach PO', extra_class: 'picon picon-mail-attachment', multi_select: true, confirm: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/attachpo', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'button', text: 'Detach PO', extra_class: 'picon picon-list-remove', multi_select: true, confirm: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/detachpo', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'button', text: 'Assign Stock', extra_class: 'picon picon-document-import', multi_select: true, confirm: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/assignstock', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'button', title: 'Flag', extra_class: 'picon picon-flag-red', multi_select: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/flag', array('id' => '__title__'))); ?>, delimiter: ','},
				<?php } ?>
				{type: 'separator'},
				<?php if (!$this->ordered) { ?>
				{type: 'button', text: 'Ordered', extra_class: 'picon picon-vcs-removed', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/pending', array('ordered' => 'true', 'location' => $this->location->guid, 'descendants' => ($this->descendants ? 'true' : 'false'), 'all_time' => ($this->all_time ? 'true' : 'false'), 'start_date' => ($this->start_date ? format_date($this->start_date, 'date_sort') : ''), 'end_date' => ($this->end_date ? format_date($this->end_date - 1, 'date_sort') : '')))); ?>},
				<?php } else { ?>
				{type: 'button', text: 'New Orders', extra_class: 'picon picon-vcs-normal', selection_optional: true, url: <?php echo json_encode(pines_url('com_sales', 'warehouse/pending', array('location' => $this->location->guid, 'descendants' => ($this->descendants ? 'true' : 'false'), 'all_time' => ($this->all_time ? 'true' : 'false'), 'start_date' => ($this->start_date ? format_date($this->start_date, 'date_sort') : ''), 'end_date' => ($this->end_date ? format_date($this->end_date - 1, 'date_sort') : '')))); ?>},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'pending warehouse orders',
						content: rows
					});
				}},
				<?php // Need to check if Google Drive is installed
					if ($google_drive && !empty($_->config->com_googledrive->client_id)) { ?>
				{type: 'button', title: 'Export to Google Drive', extra_class: 'picon drive-icon', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					setRows(rows);
					checkAuth();
				}},
				<?php } elseif ($google_drive && empty($_->config->com_googledrive->client_id)) { ?>
				{type: 'button', title: 'Export to Google Drive', extra_class: 'picon drive-icon', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					alert('You need to set the CLIENT ID before you can export to Google Drive');
				}},
				<?php } ?>
			],
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/warehouse/pending", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var pending_grid = $("#p_muid_grid").pgrid(cur_options);

		pending_grid.date_form = function(){
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
							"Update": function(){
								if (form.find(":input[name=timespan_saver]").val() == "alltime") {
									all_time = true;
								} else {
									all_time = false;
									start_date = form.find(":input[name=start_date]").val();
									end_date = form.find(":input[name=end_date]").val();
								}
								form.dialog('close');
								submit_search();
							}
						}
					});
					$_.play();
				}
			});
		};
		pending_grid.location_form = function(){
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
			<th>Date</th>
			<th>Sale</th>
			<th>Location</th>
			<th>Employee</th>
			<th>Product</th>
			<th>Qty</th>
			<th>Customer</th>
			<th>PO</th>
			<th>Vendors</th>
			<th>Flag Comments</th>
			<th>SKU</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->sales as $sale) {
		foreach ($sale->products as $key => $cur_product) {
			// Filter non warehouse products.
			if ($cur_product['delivery'] != 'warehouse' || ($cur_product['ordered'] xor $this->ordered))
				continue;
			// Have they all been returned?
			if ($cur_product['quantity'] <= $cur_product['returned_quantity'])
				continue;
			// Have they already all been assigned?
			if (count($cur_product['stock_entities']) >= ($cur_product['quantity'] + $cur_product['returned_quantity']))
				continue;
			$styles = array();
			if (isset($cur_product['flag_bgcolor']))
				$styles[] = 'background-color: '.h($cur_product['flag_bgcolor']).';';
			if (isset($cur_product['flag_textcolor']))
				$styles[] = 'color: '.h($cur_product['flag_textcolor']).';';
			if ($styles)
				$style = ' style="'.implode (' ', $styles).'"';
			else
				$style = '';
		?>
		<tr title="<?php echo $sale->guid.'_'.$key; ?>">
			<td<?php echo $style; ?>><?php e(format_date($sale->tender_date, 'date_sort')); ?></td>
			<td<?php echo $style; ?>><a<?php echo $style; ?> data-entity="<?php e($sale->guid); ?>" data-entity-context="com_sales_sale"><?php e($sale->id); ?></a></td>
			<td<?php echo $style; ?>><a data-entity="<?php e($sale->group->guid); ?>" data-entity-context="group"><?php e($sale->group->name); ?></a></td>
			<td<?php echo $style; ?>><a data-entity="<?php e($cur_product['salesperson']->guid); ?>" data-entity-context="user"><?php e($cur_product['salesperson']->name); ?></a></td>
			<td<?php echo $style; ?>><a<?php echo $style; ?> data-entity="<?php e($cur_product['entity']->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_product['entity']->name); ?></a></td>
			<td<?php echo $style; ?>><?php e(($cur_product['quantity'] - (int) $cur_product['returned_quantity']) - (count($cur_product['stock_entities']) - count($cur_product['returned_stock_entities']))); ?></td>
			<td<?php echo $style; ?>><a<?php echo $style; ?> data-entity="<?php e($sale->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e($sale->customer->name); ?></a></td>
			<td<?php echo $style; ?>><?php if (isset($cur_product['po'])) { ?><a<?php echo $style; ?> data-entity="<?php e($cur_product['po']->guid); ?>" data-entity-context="com_sales_po"><?php e($cur_product['po']->po_number); ?></a><?php } ?></td>
			<td<?php echo $style; ?>>
				<?php
				$vendors = array();
				foreach ($cur_product['entity']->vendors as $cur_vendor) {
					$cur_string = '';
					$cur_string .= '<a'.$style.' data-entity="'.h($cur_vendor['entity']->guid).'" data-entity-context="com_sales_vendor">'.h($cur_vendor['entity']->name).'</a>';
					if (!empty($cur_vendor['link']))
						$cur_string .= ' [<a'.$style.' href="'.h($cur_vendor['link']).'" target="_blank">Vendor Link</a>]';
					$vendors[] = $cur_string;
				}
				echo implode(', ', $vendors);
				?>
			</td>
			<td<?php echo $style; ?>><?php e($cur_product['flag_comments']); ?></td>
			<td<?php echo $style; ?>><?php e($cur_product['entity']->sku); ?></td>
		</tr>
	<?php } } ?>
	</tbody>
</table>