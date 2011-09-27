<?php
/**
 * Lists stock and provides functions to manipulate them.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
if ($this->removed) {
	$this->title = 'Removed Stock';
} else {
	if (!empty($this->stock)) {
		$this->title = "Stock at {$this->location->name} [{$this->location->groupname}]";
		if ($this->descendents)
			$this->note = "Stock for all locations below {$this->location->name} is also included.";
	} else {
		$this->title = 'Stock [No Location]';
		$this->note = 'This list is empty by default. Please select a location to view the inventory.';
	}
}

$pines->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = $_SESSION['user']->pgrid_saved_states['com_sales/stock/list'];
$pines->com_jstree->load();
?>
<script type="text/javascript">
	// <![CDATA[

	pines(function(){
		var submit_url = "<?php echo addslashes(pines_url('com_sales', 'stock/list')); ?>";
		var submit_search = function(){
			// Submit the form with all of the fields.
			pines.get(submit_url, {
				"location": location,
				"descendents": descendents
			});
		};

		// Location Defaults
		var location = "<?php echo $this->location->guid; ?>";
		var descendents = <?php echo $this->descendents ? 'true' : 'false'; ?>;

		var state_xhr;
		var cur_state = JSON.parse("<?php echo (isset($this->pgrid_state) ? addslashes($this->pgrid_state) : '{}');?>");
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (!$this->removed) { ?>
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){stock_grid.location_form();}},
				{type: 'separator'},
				<?php } if (gatekeeper('com_sales/receive')) { ?>
				{type: 'button', text: 'Receive', extra_class: 'picon picon-document-new', selection_optional: true, url: '<?php echo addslashes(pines_url('com_sales', 'stock/receive')); ?>'},
				<?php } if (gatekeeper('com_sales/managestock')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', multi_select: true, double_click: true, url: '<?php echo addslashes(pines_url('com_sales', 'stock/edit', array('id' => '__title__'))); ?>', delimiter: ','},
				{type: 'button', text: 'Transfer', extra_class: 'picon picon-go-jump', multi_select: true, url: '<?php echo addslashes(pines_url('com_sales', 'stock/transfer', array('id' => '__title__'))); ?>', delimiter: ','},
				{type: 'separator'},
				{type: 'button', text: 'Last Transaction', extra_class: 'picon picon-view-history', multi_select: true, click: function(e, rows){
					rows.each(function(){
						var cur_row = $(this);
						$.ajax({
							url: "<?php echo addslashes(pines_url('com_sales', 'stock/lasttransaction')); ?>",
							type: "POST",
							dataType: "text",
							data: {"id": cur_row.pgrid_export_rows()[0].key},
							error: function(XMLHttpRequest, textStatus){
								pines.error("An error occured while trying to lookup last transaction:\n"+XMLHttpRequest.status+": "+textStatus);
							},
							success: function(data){
								cur_row.pgrid_set_value(<?php echo $this->removed ? 7 : 8; ?>, data);
							}
						});
					});
				}},
				<?php } ?>
				{type: 'separator'},
				<?php if (!$this->removed) { ?>
				{type: 'button', text: 'Removed', extra_class: 'picon picon-vcs-removed', selection_optional: true, url: '<?php echo addslashes(pines_url('com_sales', 'stock/list', array('removed' => 'true'))); ?>'},
				<?php } else { ?>
				{type: 'button', text: 'Current', extra_class: 'picon picon-vcs-normal', selection_optional: true, url: '<?php echo addslashes(pines_url('com_sales', 'stock/list')); ?>'},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post("<?php echo addslashes(pines_url('system', 'csv')); ?>", {
						filename: 'stock',
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
				state_xhr = $.post("<?php echo addslashes(pines_url('com_pgrid', 'save_state')); ?>", {view: "com_sales/stock/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var stock_grid = $("#p_muid_grid").pgrid(cur_options);

		stock_grid.location_form = function(){
			$.ajax({
				url: "<?php echo addslashes(pines_url('com_sales', 'forms/locationselect')); ?>",
				type: "POST",
				dataType: "html",
				data: {"location": location, "descendents": descendents},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to retreive the location form:\n"+XMLHttpRequest.status+": "+textStatus);
				},
				success: function(data){
					if (data == "")
						return;
					var form = $("<div title=\"Location Selector\"></div>");
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
								if (form.find(":input[name=descendents]").attr('checked'))
									descendents = true;
								else
									descendents = false;
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
			<th>SKU</th>
			<th>Product</th>
			<th>Serial</th>
			<th>Vendor</th>
			<?php if (!$this->removed) { ?>
			<th>Location</th>
			<?php } ?>
			<th>Cost</th>
			<th>Available</th>
			<?php if (gatekeeper('com_sales/managestock')) { ?>
			<th>Last Transaction</th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->stock as $stock) { ?>
		<tr title="<?php echo $stock->guid; ?>">
			<td><?php echo htmlspecialchars($stock->product->sku); ?></td>
			<td><a href="<?php echo htmlspecialchars(pines_url('com_sales', 'product/edit', array('id' => $stock->product->guid))); ?>" onclick="window.open(this.href); return false;"><?php echo htmlspecialchars($stock->product->name); ?></a></td>
			<td><?php echo htmlspecialchars($stock->serial); ?></td>
			<td><a href="<?php echo htmlspecialchars(pines_url('com_sales', 'vendor/edit', array('id' => $stock->vendor->guid))); ?>" onclick="window.open(this.href); return false;"><?php echo htmlspecialchars($stock->vendor->name); ?></a></td>
			<?php if (!$this->removed) { ?>
			<td><?php echo htmlspecialchars("{$stock->location->name} [{$stock->location->groupname}]"); ?></td>
			<?php } ?>
			<td><?php echo isset($stock->cost) ? '$'.number_format($stock->cost, 2) : ''; ?></td>
			<td><?php echo $stock->available ? 'Yes' : 'No'; ?></td>
			<?php if (gatekeeper('com_sales/managestock')) { ?>
			<td></td>
			<?php } ?>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php if (gatekeeper('com_sales/managestock')) { ?>
<small>Note: Last transaction is database intensive, so it is not loaded. Select rows and click the toolbar button to load it.</small>
<?php } ?>