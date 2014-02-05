<?php
/**
 * Displays a log.
 *
 * @package Components\logger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Log Files View';
$this->note = $this->all_time ? 'Showing all time.' : 'Showing '.h(format_date($this->start_date, 'date_short')).' - '.h(format_date($this->end_date - 1, 'date_short')).'.';
// This regex breaks apart the log entries into each part.
preg_match_all('/^(\d{4}-\d{2}-\d{2}T[\d:-]+): ([a-z]+): (com_\w+)?, ([^:]+)?: ([\d.]+)?(.*?)? ?\(?(\d+)?\)?: (.*)$/mi', $this->log, $matches, PREG_SET_ORDER);
$_->icons->load();
$_->com_jstree->load();
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_logger/view']);

// Remember users that we've already pulled from the database.
$users = array();
// Go through the matches array and get rid of entries that don't belong.
foreach ($matches as $key => &$cur_match) {
	// Check date.
	$cur_match['timestamp'] = strtotime($cur_match[1]);
	if (!$this->all_time && ($cur_match['timestamp'] < $this->start_date || $cur_match['timestamp'] >= $this->end_date)) {
		unset($matches[$key]);
		continue;
	}
	// If we're not checking a location, we're done.
	if (!isset($this->location->guid))
		continue;
	// If the entry didn't have a user, it doesn't belong.
	if (empty($cur_match[7])) {
		unset($matches[$key]);
		continue;
	}
	// Get the user that the entry belongs to.
	if (!array_key_exists($cur_match[7], $users)) {
		$user = user::factory((int) $cur_match[7]);
		$users[$cur_match[7]] = $user;
	} else {
		$user = $users[$cur_match[7]];
	}
	// Check if we actually found a valid user.
	if (!isset($user->guid)) {
		unset($matches[$key]);
		continue;
	}
	// Now check if the user belongs to the location we're checking, or, if we're checking descendants too, check if they are a descendant.
	if (!$user->in_group($this->location) && (!$this->descendants || !$user->is_descendant($this->location))) {
		unset($matches[$key]);
		continue;
	}
}
unset($cur_match);

// Count each type of log entry.
$debugs = $infos = $notices = $warnings = $errors = $fatals = array();
foreach ($matches as $match) {
	switch ($match[2]){
		case 'debug':
			$debugs[] = $match[2];
			break;
		case 'info':
			$infos[] = $match[2];
			break;
		case 'notice':
			$notices[] = $match[2];
			break;
		case 'warning':
			$warnings[] = $match[2];
			break;
		case 'error':
			$errors[] = $match[2];
			break;
		case 'fatal':
			$fatals[] = $match[2];
			break;
	}
}

?>
<script type="text/javascript">
	$_(function(){
		var search_logs = function(){
			// Submit the form with all of the fields.
			$_.get(<?php echo json_encode(pines_url('com_logger', 'view')); ?>, {
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
		// Grid
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				{type: 'button', title: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){log_grid.location_form();}},
				{type: 'button', title: 'Timespan', extra_class: 'picon picon-view-time-schedule', selection_optional: true, click: function(){log_grid.date_form();}},
				{type: 'button', text: 'Reset', extra_class: 'picon picon-view-refresh', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: ""});
				}},
				{type: 'button', text: 'Debug', extra_class: 'picon picon-help-hint', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: "p_muid_debug"});
				}},
				{type: 'button', text: 'Info', extra_class: 'picon picon-dialog-information', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: "p_muid_info"});
				}},
				{type: 'button', text: 'Notice', extra_class: 'picon picon-view-pim-notes', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: "p_muid_notice"});
				}},
				{type: 'button', text: 'Error', extra_class: 'picon picon-dialog-error', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: "p_muid_error"});
				}},
				{type: 'button', text: 'Warning', extra_class: 'picon picon-dialog-warning', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: "p_muid_warning"});
				}},
				{type: 'button', text: 'Fatal', extra_class: 'picon picon-script-error', selection_optional: true, click: function(e){
					log_grid.pgrid_import_state({pgrid_filter: "p_muid_fatal"});
				}},
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'log entries',
						content: rows
					});
				}}
			],
			pgrid_hidden_cols: [9],
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_logger/view", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var log_grid = $("#p_muid_grid").pgrid(cur_options);
		
		log_grid.date_form = function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_logger', 'dateselect')); ?>,
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
								search_logs();
							}
						}
					});
					$_.play();
				}
			});
		};
		log_grid.location_form = function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_logger', 'locationselect')); ?>,
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
								search_logs();
							}
						}
					});
					$_.play();
				}
			});
		};
	});
</script>
<div class="pf-form">
	<div class="pf-element pf-heading">
		<h3>Total Log Entries: <strong><?php echo count($matches); ?></strong></h3>
	</div>
	<div class="pf-element pf-full-width">
		<div style="width: 200px; padding-right: 100px; float: left;">
			<span class="picon picon-help-hint" style="padding-left: 16px; background-repeat: no-repeat; line-height: 16px;"> Total Debug Entries: <strong><span style="float: right;"><?php echo count($debugs); ?></span></strong></span><br/>
			<span class="picon picon-dialog-information" style="padding-left: 18px; background-repeat: no-repeat; line-height: 16px;"> Total Info Entries: <strong><span style="float: right;"><?php echo count($infos); ?></span></strong></span><br/>
			<span class="picon picon-view-pim-notes" style="padding-left: 18px; background-repeat: no-repeat; line-height: 16px;"> Total Notice Entries: <strong><span style="float: right;"><?php echo count($notices); ?></span></strong></span><br/><br/>
		</div>
		<div style="width: 200px; float: left;">
			<span class="picon picon-dialog-warning" style="padding-left: 18px; background-repeat: no-repeat; line-height: 16px;"> Total Warning Entries: <strong><span style="float: right;"><?php echo count($warnings); ?></span></strong></span><br/>
			<span class="picon picon-dialog-error" style="padding-left: 18px; background-repeat: no-repeat; line-height: 16px;"> Total Error Entries: <strong><span style="float: right;"><?php echo count($errors); ?></span></strong></span><br/>
			<span class="picon picon-script-error" style="padding-left: 18px; background-repeat: no-repeat; line-height: 16px;"> Total Fatal Entries: <strong><span style="float: right;"><?php echo count($fatals); ?></span></strong></span><br/><br/>
		</div>
	</div>
</div>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Date / Time</th>
			<th>Level</th>
			<th>Component</th>
			<th>Action</th>
			<th>IP Address</th>
			<th>User</th>
			<th>User ID</th>
			<th>Message</th>
			<th>Filter Helper</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($matches as $cur_match) { ?>
		<tr>
			<td><?php e(format_date($cur_match['timestamp'])); ?></td>
			<td><?php
			switch ($cur_match[2]) {
				case 'debug':
					$class = 'picon-help-hint';
					break;
				case 'info':
					$class = 'picon-dialog-information';
					break;
				case 'notice':
					$class = 'picon-view-pim-notes';
					break;
				case 'warning':
					$class = 'picon-dialog-warning';
					break;
				case 'error':
					$class = 'picon-dialog-error';
					break;
				case 'fatal':
					$class = 'picon-script-error';
					break;
				default:
					$class = '';
					break;
			}
			?><span class="<?php echo $class; ?>" style="display: inline-block; line-height: 16px; padding-left: 18px; background-repeat: no-repeat;"><?php e($cur_match[2]); ?></span></td>
			<td><?php e($cur_match[3]); ?></td>
			<td><?php e($cur_match[4]); ?></td>
			<td><?php e($cur_match[5]); ?></td>
			<td><?php e($cur_match[6]); ?></td>
			<td><?php e($cur_match[7]); ?></td>
			<td><?php e($cur_match[8]); ?></td>
			<td><?php e('p_muid_'.$cur_match[2]); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>