<?php
/**
 * Lists customers and provides functions to manipulate them.
 *
 * @package Pines
 * @subpackage com_customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Customers';
$this->note = 'Begin by searching for a customer.';
$pines->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = $_SESSION['user']->pgrid_saved_states['com_customer/customer/list'];
?>
<script type="text/javascript">
	// <![CDATA[

	pines(function(){
		// Customer search function for the pgrid toolbar.
		var customer_search_box;
		var submit_search = function(){
			var search_string = customer_search_box.val();
			if (search_string == "") {
				alert("Please enter a search string.");
				return;
			}
			var loader;
			$.ajax({
				url: "<?php echo addslashes(pines_url('com_customer', 'customer/search')); ?>",
				type: "POST",
				dataType: "json",
				data: {"q": search_string},
				beforeSend: function(){
					loader = $.pnotify({
						pnotify_title: 'Search',
						pnotify_text: 'Searching the database...',
						pnotify_notice_icon: 'picon picon-throbber',
						pnotify_nonblock: true,
						pnotify_hide: false,
						pnotify_history: false
					});
					customer_grid.pgrid_get_all_rows().pgrid_delete();
				},
				complete: function(){
					loader.pnotify_remove();
				},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured:\n"+XMLHttpRequest.status+": "+textStatus);
				},
				success: function(data){
					if (!data) {
						alert("No customers were found that matched the query.");
						return;
					}
					var struct = [];
					$.each(data, function(){
						struct.push({
							"key": this.guid,
							"values": [
								this.guid,
								this.username,
								this.name,
								this.email,
								this.company,
								this.phone_home,
								this.phone_work,
								this.phone_cell,
								this.fax,
								this.enabled ? "Yes" : "No",
								this.member ? (this.valid_member ? "Yes" : "Expired") : "No",
								this.member_exp,
								this.points
							]
						});
					});
					customer_grid.pgrid_add(struct);
				}
			});
		}
		var customer_id;
		var interaction_dialog = $("#p_muid_interaction_dialog");

		interaction_dialog.dialog({
			bgiframe: true,
			autoOpen: false,
			modal: true,
			width: 402,
			buttons: {
				"Log Interaction": function(){
					var loader;
					$.ajax({
						url: "<?php echo addslashes(pines_url('com_customer', 'interaction/add')); ?>",
						type: "POST",
						dataType: "json",
						data: {
							customer: customer_id,
							employee: <?php echo $_SESSION['user']->guid; ?>,
							date: $("#p_muid_interaction_dialog [name=interaction_date]").val(),
							time_ampm: $("#p_muid_interaction_dialog [name=interaction_ampm]").val(),
							time_hour: $("#p_muid_interaction_dialog [name=interaction_hour]").val(),
							time_minute: $("#p_muid_interaction_dialog [name=interaction_minute]").val(),
							type: $("#p_muid_interaction_dialog [name=interaction_type]").val(),
							status: $("#p_muid_interaction_dialog [name=interaction_status]").val(),
							comments: $("#p_muid_interaction_dialog [name=interaction_comments]").val()
						},
						beforeSend: function(){
							loader = $.pnotify({
								pnotify_title: 'Logging',
								pnotify_text: 'Documenting customer interaction...',
								pnotify_notice_icon: 'picon picon-throbber',
								pnotify_nonblock: true,
								pnotify_hide: false,
								pnotify_history: false
							});
						},
						complete: function(){
							loader.pnotify_remove();
						},
						error: function(XMLHttpRequest, textStatus){
							pines.error("An error occured:\n"+XMLHttpRequest.status+": "+textStatus);
						},
						success: function(data){
							if (!data) {
								alert("Could not log the customer interaction.");
								return;
							}
							alert("Successfully logged interaction.");
							$("#p_muid_interaction_dialog [name=interaction_comments]").val('');
							interaction_dialog.dialog("close");
						}
					});
				}
			}
		});

		var state_xhr;
		var cur_state = JSON.parse("<?php echo (isset($this->pgrid_state) ? addslashes($this->pgrid_state) : '{}');?>");
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				{type: 'text', load: function(textbox){
					// Display the current sku being searched.
					textbox.keydown(function(e){
						if (e.keyCode == 13)
							submit_search();
					});
					customer_search_box = textbox;
				}},
				{type: 'button', extra_class: 'picon picon-system-search', selection_optional: true, pass_csv_with_headers: true, click: submit_search},
				{type: 'separator'},
				<?php if (gatekeeper('com_customer/newcustomer')) { ?>
				{type: 'button', text: 'New', extra_class: 'picon picon-document-new', selection_optional: true, url: '<?php echo addslashes(pines_url('com_customer', 'customer/edit')); ?>'},
				<?php } if (gatekeeper('com_customer/editcustomer')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: '<?php echo addslashes(pines_url('com_customer', 'customer/edit', array('id' => '__title__'))); ?>'},
				{type: 'button', text: 'Edit as User', extra_class: 'picon picon-user-properties', url: '<?php echo addslashes(pines_url('com_user', 'edituser', array('id' => '__title__'))); ?>'},
				<?php } if (gatekeeper('com_customer/newinteraction')) { ?>
				{type: 'button', title: 'Add New Interaction', extra_class: 'picon picon-meeting-participant-optional', click: function(e, row){
					customer_id = row.attr('title');
					interaction_dialog.dialog("open");
				}},
				<?php } ?>
				//{type: 'button', text: 'E-Mail', extra_class: 'picon picon-mail-message-new', multi_select: true, url: 'mailto:__col_2__', delimiter: ','},
				<?php if (gatekeeper('com_customer/viewhistory')) { ?>
				{type: 'button', title: 'History', extra_class: 'picon picon-documentinfo', url: '<?php echo addslashes(pines_url('com_customer', 'customer/history', array('id' => '__title__'))); ?>'},
				<?php } ?>
				{type: 'separator'},
				<?php if ($pines->config->com_customer->resetpoints && gatekeeper('com_customer/resetpoints')) { ?>
				{type: 'button', title: 'Reset the Customer\'s Points', extra_class: 'picon picon-edit-clear', multi_select: true, url: '<?php echo addslashes(pines_url('com_customer', 'customer/resetpoints', array('id' => '__title__'))); ?>', delimiter: ','},
				<?php } if (gatekeeper('com_customer/deletecustomer')) { ?>
				{type: 'button', title: 'Remove as Customer', extra_class: 'picon picon-list-remove', confirm: true, multi_select: true, url: '<?php echo addslashes(pines_url('com_customer', 'customer/remove', array('id' => '__title__'))); ?>', delimiter: ','},
				{type: 'button', title: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: '<?php echo addslashes(pines_url('com_customer', 'customer/delete', array('id' => '__title__'))); ?>', delimiter: ','},
				{type: 'separator'},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post("<?php echo addslashes(pines_url('system', 'csv')); ?>", {
						filename: 'customers',
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
				state_xhr = $.post("<?php echo addslashes(pines_url('com_pgrid', 'save_state')); ?>", {view: "com_customer/customer/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		var customer_grid = $("#p_muid_grid").pgrid(cur_options);
		customer_grid.pgrid_get_all_rows().pgrid_delete();
	
		$("#p_muid_interaction_dialog [name=interaction_date]").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true
		});
	});

	// ]]>
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>ID</th>
			<th>Username</th>
			<th>Name</th>
			<th>Email</th>
			<th>Company</th>
			<th>Home Phone</th>
			<th>Work Phone</th>
			<th>Cell Phone</th>
			<th>Fax</th>
			<th>Login Enabled</th>
			<th>Member</th>
			<th>Expiration</th>
			<th>Points</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
		</tr>
	</tbody>
</table>
<div id="p_muid_interaction_dialog" title="Log Customer Interaction" style="display: none;">
	<form class="pf-form" method="post" action="">
		<div class="pf-element">
			<label><span class="pf-label">Interaction Type</span>
				<select class="ui-widget-content ui-corner-all" name="interaction_type">
					<?php
						foreach ($pines->config->com_customer->interaction_types as $cur_type) {
							echo '<option value="'.$cur_type.'">'.$cur_type.'</option>';
						}
					?>
				</select></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Date</span>
				<input class="ui-widget-content ui-corner-all" type="text" size="22" name="interaction_date" value="<?php echo format_date(time(), 'date_sort'); ?>" /></label>
		</div>
		<div class="pf-element pf-full-width">
			<?php
			$time_hour = format_date(time(), 'custom', 'H');
			$time_minute = format_date(time(), 'custom', 'i');
			?>
			<span class="pf-label">Time</span>
			<select class="ui-widget-content ui-corner-all" name="interaction_hour">
				<option value="1" <?php echo ($time_hour == '1' || $time_hour == '13') ? 'selected="selected"' : ''; ?>>1</option>
				<option value="2" <?php echo ($time_hour == '2' || $time_hour == '14') ? 'selected="selected"' : ''; ?>>2</option>
				<option value="3" <?php echo ($time_hour == '3' || $time_hour == '15') ? 'selected="selected"' : ''; ?>>3</option>
				<option value="4" <?php echo ($time_hour == '4' || $time_hour == '16') ? 'selected="selected"' : ''; ?>>4</option>
				<option value="5" <?php echo ($time_hour == '5' || $time_hour == '17') ? 'selected="selected"' : ''; ?>>5</option>
				<option value="6" <?php echo ($time_hour == '6' || $time_hour == '18') ? 'selected="selected"' : ''; ?>>6</option>
				<option value="7" <?php echo ($time_hour == '7' || $time_hour == '19') ? 'selected="selected"' : ''; ?>>7</option>
				<option value="8" <?php echo ($time_hour == '8' || $time_hour == '20') ? 'selected="selected"' : ''; ?>>8</option>
				<option value="9" <?php echo ($time_hour == '9' || $time_hour == '21') ? 'selected="selected"' : ''; ?>>9</option>
				<option value="10" <?php echo ($time_hour == '10' || $time_hour == '22') ? 'selected="selected"' : ''; ?>>10</option>
				<option value="11" <?php echo ($time_hour == '11' || $time_hour == '23') ? 'selected="selected"' : ''; ?>>11</option>
				<option value="0" <?php echo ($time_hour == '0' || $time_hour == '12') ? 'selected="selected"' : ''; ?>>12</option>
			</select> :
			<select class="ui-widget-content ui-corner-all" name="interaction_minute">
				<option value="0" <?php echo ($time_minute >= '0' && $time_minute < '15') ? 'selected="selected"' : ''; ?>>00</option>
				<option value="15" <?php echo ($time_minute >= '15' && $time_minute < '30') ? 'selected="selected"' : ''; ?>>15</option>
				<option value="30" <?php echo ($time_minute >= '30' && $time_minute < '45') ? 'selected="selected"' : ''; ?>>30</option>
				<option value="45" <?php echo ($time_minute >= '45' && $time_minute < '60') ? 'selected="selected"' : ''; ?>>45</option>
			</select>
			<select class="ui-widget-content ui-corner-all" name="interaction_ampm">
				<option value="am" selected="selected">AM</option>
				<option value="pm" <?php echo ($time_hour >= 12) ? 'selected="selected"' : ''; ?>>PM</option>
			</select>
		</div>
		<div class="pf-element">
			<label>
				<span class="pf-label">Status</span>
				<select class="ui-widget-content ui-corner-all" name="interaction_status">
					<option value="open">Open</option>
					<option value="closed">Closed</option>
				</select>
			</label>
		</div>
		<div class="pf-element pf-full-width">
			<textarea class="ui-widget-content ui-corner-all" rows="3" cols="40" name="interaction_comments"></textarea>
		</div>
	</form>
	<br />
</div>