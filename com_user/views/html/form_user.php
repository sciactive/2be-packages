<?php
/**
 * Provides a form for the user to edit a user.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New User' : 'Editing ['.htmlspecialchars($this->entity->username).']';
$this->note = 'Provide user details in this form.';
$pines->com_pgrid->load();
//$pines->com_jstree->load();
if ($pines->config->com_user->check_username)
	$pines->icons->load();
?>
<?php if ($pines->config->com_user->check_username) { ?>
<style type="text/css">
	/* <![CDATA[ */
	#p_muid_username_loading {
		background-position: left;
		background-repeat: no-repeat;
		padding-left: 16px;
		display: none;
	}
	#p_muid_username_message {
		background-position: left;
		background-repeat: no-repeat;
		padding-left: 20px;
		line-height: 16px;
	}
	/* ]]> */
</style>
<?php } ?>
<script type="text/javascript">
	// <![CDATA[
	pines(function(){
		<?php if ($this->display_username && $pines->config->com_user->check_username) { ?>
		// Check usernames.
		$("[name=username]", "#p_muid_form").change(function(){
			var username = $(this);
			var id = "<?php echo $this->entity->guid; ?>";
			$.ajax({
				url: "<?php echo addslashes(pines_url('com_user', 'checkusername')); ?>",
				type: "POST",
				dataType: "json",
				data: {"id": id, "username": username.val()},
				beforeSend: function(){
					$("#p_muid_username_loading").show();
					username.removeClass("ui-state-error");
					$("#p_muid_username_message").removeClass("picon-task-complete").removeClass("picon-task-attempt").html("");
				},
				complete: function(){
					$("#p_muid_username_loading").hide();
				},
				error: function(){
					username.addClass("ui-state-error");
					$("#p_muid_username_message").addClass("picon-task-attempt").html("Error checking username. Please check your internet connection.");
				},
				success: function(data){
					if (!data) {
						username.addClass("ui-state-error");
						$("#p_muid_username_message").addClass("picon-task-attempt").html("Error checking username.");
						return;
					}
					if (data.result) {
						username.removeClass("ui-state-error");
						$("#p_muid_username_message").addClass("picon-task-complete").html(data.message);
						return;
					}
					username.addClass("ui-state-error");
					$("#p_muid_username_message").addClass("picon-task-attempt").html(data.message);
				}
			});
		}).blur(function(){
			$(this).change();
		});
		<?php } ?>

		var password = $("#p_muid_form [name=password]");
		var password2 = $("#p_muid_form [name=password2]");
		$("#p_muid_form").submit(function(){
			if (password.val() != password2.val()) {
				alert("Your passwords do not match.");
				return false;
			}
			return true;
		});

		$("#p_muid_tabs").tabs();
	});
	// ]]>
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php echo htmlspecialchars(pines_url('com_user', 'saveuser')); ?>" autocomplete="off">
	<div id="p_muid_tabs" style="clear: both;">
		<ul>
			<li><a href="#p_muid_tab_general">General</a></li>
			<?php if ( $this->display_groups ) { ?>
			<li><a href="#p_muid_tab_groups">Groups</a></li>
			<?php } if (in_array('address', $pines->config->com_user->user_fields) || in_array('additional_addresses', $pines->config->com_user->user_fields)) { ?>
			<li><a href="#p_muid_tab_location">Address</a></li>
			<?php } if ( $this->display_abilities ) { ?>
			<li><a href="#p_muid_tab_abilities">Abilities</a></li>
			<?php } if (in_array('attributes', $pines->config->com_user->user_fields)) { ?>
			<li><a href="#p_muid_tab_attributes">Attributes</a></li>
			<?php } ?>
		</ul>
		<div id="p_muid_tab_general">
			<?php if (isset($this->entity->guid)) { ?>
			<div class="date_info" style="float: right; text-align: right;">
				<div>Created: <span class="date"><?php echo format_date($this->entity->p_cdate, 'full_short'); ?></span></div>
				<div>Modified: <span class="date"><?php echo format_date($this->entity->p_mdate, 'full_short'); ?></span></div>
			</div>
			<?php } if ($this->display_username) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Username</span>
					<span class="pf-group" style="display: block;">
						<input class="pf-field ui-widget-content ui-corner-all" type="text" name="username" size="24" value="<?php echo htmlspecialchars($this->entity->username); ?>" />
						<?php if ($pines->config->com_user->check_username) { ?>
						<span class="pf-field picon picon-throbber loader" id="p_muid_username_loading" style="display: none;">&nbsp;</span>
						<span class="pf-field picon" id="p_muid_username_message"></span>
						<?php } ?>
					</span>
				</label>
			</div>
			<?php } if (in_array('name', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element">
				<label><span class="pf-label">First Name</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="text" name="name_first" size="24" value="<?php echo htmlspecialchars($this->entity->name_first); ?>" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Middle Name</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="text" name="name_middle" size="24" value="<?php echo htmlspecialchars($this->entity->name_middle); ?>" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Last Name</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="text" name="name_last" size="24" value="<?php echo htmlspecialchars($this->entity->name_last); ?>" /></label>
			</div>
			<?php } if ($this->display_enable) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Enabled</span>
					<input class="pf-field" type="checkbox" name="enabled" value="ON"<?php echo $this->entity->has_tag('enabled') ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<?php } if (in_array('email', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Email</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="email" name="email" size="24" value="<?php echo htmlspecialchars($this->entity->email); ?>" /></label>
			</div>
			<?php } if (in_array('phone', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Phone</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="tel" name="phone" size="24" value="<?php echo format_phone($this->entity->phone); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
			</div>
			<?php } if (in_array('fax', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Fax</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="tel" name="fax" size="24" value="<?php echo format_phone($this->entity->fax); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
			</div>
			<?php } if ($pines->config->com_user->referral_codes) { ?>
			<div class="pf-element">
				<label><span class="pf-label">Referral Code</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="text" name="referral_code" size="24" value="<?php echo htmlspecialchars($this->entity->referral_code); ?>" /></label>
			</div>
			<?php } if (in_array('timezone', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element">
				<label>
					<span class="pf-label">Timezone</span>
					<span class="pf-note">This overrides the primary group's timezone.</span>
					<select class="pf-field ui-widget-content ui-corner-all" name="timezone" size="1">
						<option value="">--Inherit From Group--</option>
						<?php
						$tz = DateTimeZone::listIdentifiers();
						sort($tz);
						foreach ($tz as $cur_tz) {
							?><option value="<?php echo htmlspecialchars($cur_tz); ?>"<?php echo $this->entity->timezone == $cur_tz ? ' selected="selected"' : ''; ?>><?php echo htmlspecialchars($cur_tz); ?></option><?php
						} ?>
					</select>
				</label>
			</div>
			<?php } ?>
			<div class="pf-element">
				<label><span class="pf-label"><?php if (isset($this->entity->guid)) echo 'Update '; ?>Password</span>
					<?php if (!isset($this->entity->guid)) {
						echo ($pines->config->com_user->pw_empty ? '<span class="pf-note">May be blank.</span>' : '');
					} else {
						echo '<span class="pf-note">Leave blank, if not changing.</span>';
					} ?>
					<input class="pf-field ui-widget-content ui-corner-all" type="password" name="password" size="24" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Repeat Password</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="password" name="password2" size="24" /></label>
			</div>
			<?php if ($this->display_pin && in_array('pin', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element">
				<label><span class="pf-label">PIN code</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="password" name="pin" size="5" value="<?php echo htmlspecialchars($this->entity->pin); ?>" <?php echo $pines->config->com_user->max_pin_length > 0 ? "maxlength=\"{$pines->config->com_user->max_pin_length}\"" : ''; ?>/></label>
			</div>
			<?php } ?>
			<br class="pf-clearing" />
		</div>
		<?php if ( $this->display_groups ) { ?>
		<div id="p_muid_tab_groups">
				<?php if (empty($this->group_array_primary)) { ?>
				<div class="pf-element">
					<span>There are no primary groups to display.</span>
				</div>
				<?php } else { ?>
				<div class="pf-element">
					<label>
						<span class="pf-label">Primary Group</span>
						<select class="pf-field ui-widget-content ui-corner-all" name="group" size="1">
							<option value="null">-- No Primary Group --</option>
							<?php
							$pines->user_manager->group_sort($this->group_array_primary, 'name');
							foreach ($this->group_array_primary as $cur_group) {
								?><option value="<?php echo $cur_group->guid; ?>"<?php echo $cur_group->is($this->entity->group) ? ' selected="selected"' : ''; ?>><?php echo htmlspecialchars(str_repeat('->', $cur_group->get_level())." {$cur_group->name} [{$cur_group->groupname}]"); ?></option><?php
							} ?>
						</select>
					</label>
				</div>
				<?php } if (empty($this->group_array_secondary)) { ?>
				<div class="pf-element">
					<span>There are no secondary groups to display.</span>
				</div>
				<?php } else { ?>
				<div class="pf-element pf-full-width">
					<script type="text/javascript">
						// <![CDATA[
						pines(function(){
							// Group Grid
							$("#p_muid_group_grid").pgrid({
								pgrid_toolbar: true,
								pgrid_toolbar_contents: [
									{type: 'button', text: 'Expand', title: 'Expand All', extra_class: 'picon picon-arrow-down', selection_optional: true, return_all_rows: true, click: function(e, rows){
										rows.pgrid_expand_rows();
									}},
									{type: 'button', text: 'Collapse', title: 'Collapse All', extra_class: 'picon picon-arrow-right', selection_optional: true, return_all_rows: true, click: function(e, rows){
										rows.pgrid_collapse_rows();
									}},
									{type: 'separator'},
									{type: 'button', text: 'All', title: 'Check All', extra_class: 'picon picon-checkbox', selection_optional: true, return_all_rows: true, click: function(e, rows){
										$("input", rows).attr("checked", "true");
									}},
									{type: 'button', text: 'None', title: 'Check None', extra_class: 'picon picon-dialog-cancel', selection_optional: true, return_all_rows: true, click: function(e, rows){
										$("input", rows).removeAttr("checked");
									}}
								],
								pgrid_sort_col: 2,
								pgrid_sort_ord: "asc",
								pgrid_child_prefix: "ch_",
								pgrid_paginate: false,
								pgrid_view_height: "300px"
							});
						});
						// ]]>
					</script>
					<span class="pf-label">Groups</span>
					<div class="pf-group">
						<div class="pf-field">
							<table id="p_muid_group_grid">
								<thead>
									<tr>
										<th>In</th>
										<th>Name</th>
										<th>Groupname</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach($this->group_array_secondary as $cur_group) { ?>
									<tr title="<?php echo $cur_group->guid; ?>" class="<?php echo $cur_group->get_children() ? 'parent ' : ''; ?><?php echo (isset($cur_group->parent) && $cur_group->parent->in_array($this->group_array_secondary)) ? "child ch_{$cur_group->parent->guid} " : ''; ?>">
										<td><input type="checkbox" name="groups[]" value="<?php echo $cur_group->guid; ?>" <?php echo $cur_group->in_array($this->entity->groups) ? 'checked="checked" ' : ''; ?>/></td>
										<td><?php echo htmlspecialchars($cur_group->name); ?></td>
										<td><?php echo htmlspecialchars($cur_group->groupname); ?></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php } ?>
			<br class="pf-clearing" />
		</div>
		<?php } if (in_array('address', $pines->config->com_user->user_fields) || in_array('additional_addresses', $pines->config->com_user->user_fields)) { ?>
		<div id="p_muid_tab_location">
			<?php if (in_array('address', $pines->config->com_user->user_fields)) {
				if (in_array('additional_addresses', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element pf-heading">
				<h1>Main Address</h1>
			</div>
			<?php } ?>
			<div class="pf-element">
				<script type="text/javascript">
					// <![CDATA[
					pines(function(){
						var address_us = $("#p_muid_address_us");
						var address_international = $("#p_muid_address_international");
						$("#p_muid_form [name=address_type]").change(function(){
							var address_type = $(this);
							if (address_type.is(":checked") && address_type.val() == "us") {
								address_us.show();
								address_international.hide();
							} else if (address_type.is(":checked") && address_type.val() == "international") {
								address_international.show();
								address_us.hide();
							}
						}).change();
					});
					// ]]>
				</script>
				<span class="pf-label">Address Type</span>
				<label><input class="pf-field" type="radio" name="address_type" value="us"<?php echo ($this->entity->address_type == 'us') ? ' checked="checked"' : ''; ?> /> US</label>
				<label><input class="pf-field" type="radio" name="address_type" value="international"<?php echo $this->entity->address_type == 'international' ? ' checked="checked"' : ''; ?> /> International</label>
			</div>
			<div id="p_muid_address_us" style="display: none;">
				<div class="pf-element">
					<label><span class="pf-label">Address 1</span>
						<input class="pf-field ui-widget-content ui-corner-all" type="text" name="address_1" size="24" value="<?php echo htmlspecialchars($this->entity->address_1); ?>" /></label>
				</div>
				<div class="pf-element">
					<label><span class="pf-label">Address 2</span>
						<input class="pf-field ui-widget-content ui-corner-all" type="text" name="address_2" size="24" value="<?php echo htmlspecialchars($this->entity->address_2); ?>" /></label>
				</div>
				<div class="pf-element">
					<span class="pf-label">City, State</span>
					<input class="pf-field ui-widget-content ui-corner-all" type="text" name="city" size="15" value="<?php echo htmlspecialchars($this->entity->city); ?>" />
					<select class="pf-field ui-widget-content ui-corner-all" name="state">
						<option value="">None</option>
						<?php foreach (array(
								'AL' => 'Alabama',
								'AK' => 'Alaska',
								'AZ' => 'Arizona',
								'AR' => 'Arkansas',
								'CA' => 'California',
								'CO' => 'Colorado',
								'CT' => 'Connecticut',
								'DE' => 'Delaware',
								'DC' => 'DC',
								'FL' => 'Florida',
								'GA' => 'Georgia',
								'HI' => 'Hawaii',
								'ID' => 'Idaho',
								'IL' => 'Illinois',
								'IN' => 'Indiana',
								'IA' => 'Iowa',
								'KS' => 'Kansas',
								'KY' => 'Kentucky',
								'LA' => 'Louisiana',
								'ME' => 'Maine',
								'MD' => 'Maryland',
								'MA' => 'Massachusetts',
								'MI' => 'Michigan',
								'MN' => 'Minnesota',
								'MS' => 'Mississippi',
								'MO' => 'Missouri',
								'MT' => 'Montana',
								'NE' => 'Nebraska',
								'NV' => 'Nevada',
								'NH' => 'New Hampshire',
								'NJ' => 'New Jersey',
								'NM' => 'New Mexico',
								'NY' => 'New York',
								'NC' => 'North Carolina',
								'ND' => 'North Dakota',
								'OH' => 'Ohio',
								'OK' => 'Oklahoma',
								'OR' => 'Oregon',
								'PA' => 'Pennsylvania',
								'RI' => 'Rhode Island',
								'SC' => 'South Carolina',
								'SD' => 'South Dakota',
								'TN' => 'Tennessee',
								'TX' => 'Texas',
								'UT' => 'Utah',
								'VT' => 'Vermont',
								'VA' => 'Virginia',
								'WA' => 'Washington',
								'WV' => 'West Virginia',
								'WI' => 'Wisconsin',
								'WY' => 'Wyoming',
								'AA' => 'Armed Forces (AA)',
								'AE' => 'Armed Forces (AE)',
								'AP' => 'Armed Forces (AP)'
							) as $key => $cur_state) { ?>
						<option value="<?php echo $key; ?>"<?php echo $this->entity->state == $key ? ' selected="selected"' : ''; ?>><?php echo $cur_state; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="pf-element">
					<label><span class="pf-label">Zip</span>
						<input class="pf-field ui-widget-content ui-corner-all" type="text" name="zip" size="24" value="<?php echo htmlspecialchars($this->entity->zip); ?>" /></label>
				</div>
			</div>
			<div id="p_muid_address_international" style="display: none;">
				<div class="pf-element pf-full-width">
					<label><span class="pf-label">Address</span>
						<span class="pf-field pf-full-width"><textarea class="ui-widget-content ui-corner-all" style="width: 100%;" rows="3" cols="35" name="address_international"><?php echo $this->entity->address_international; ?></textarea></span></label>
				</div>
			</div>
			<?php } if (in_array('additional_addresses', $pines->config->com_user->user_fields)) {
				if (in_array('address', $pines->config->com_user->user_fields)) { ?>
			<div class="pf-element pf-heading">
				<h1>Additional Addresses</h1>
			</div>
			<?php } ?>
			<script type="text/javascript">
				// <![CDATA[
				pines(function(){
					// Addresses
					var addresses = $("#p_muid_addresses");
					var addresses_table = $("#p_muid_addresses_table");
					var address_dialog = $("#p_muid_address_dialog");

					addresses_table.pgrid({
						pgrid_paginate: false,
						pgrid_toolbar: true,
						pgrid_toolbar_contents : [
							{
								type: 'button',
								text: 'Add Address',
								extra_class: 'picon picon-list-add',
								selection_optional: true,
								click: function(){
									address_dialog.dialog('open');
								}
							},
							{
								type: 'button',
								text: 'Remove Address',
								extra_class: 'picon picon-list-remove',
								click: function(e, rows){
									rows.pgrid_delete();
									update_address();
								}
							}
						],
						pgrid_view_height: "300px"
					});

					// Address Dialog
					address_dialog.dialog({
						bgiframe: true,
						autoOpen: false,
						modal: true,
						width: 600,
						buttons: {
							"Done": function(){
								var cur_address_type = $("#p_muid_cur_address_type").val();
								var cur_address_addr1 = $("#p_muid_cur_address_addr1").val();
								var cur_address_addr2 = $("#p_muid_cur_address_addr2").val();
								var cur_address_city = $("#p_muid_cur_address_city").val();
								var cur_address_state = $("#p_muid_cur_address_state").val();
								var cur_address_zip = $("#p_muid_cur_address_zip").val();
								if (cur_address_type == "" || cur_address_addr1 == "") {
									alert("Please provide a name and a street address.");
									return;
								}
								var new_address = [{
									key: null,
									values: [
										cur_address_type,
										cur_address_addr1,
										cur_address_addr2,
										cur_address_city,
										cur_address_state,
										cur_address_zip
									]
								}];
								addresses_table.pgrid_add(new_address);
								$(this).dialog('close');
							}
						},
						close: function(){
							update_addresses();
						}
					});

					var update_addresses = function(){
						$("#p_muid_cur_address_type, #p_muid_cur_address_addr1, #p_muid_cur_address_addr2, #p_muid_cur_address_city, #p_muid_cur_address_state, #p_muid_cur_address_zip").val("");
						addresses.val(JSON.stringify(addresses_table.pgrid_get_all_rows().pgrid_export_rows()));
					};

					update_addresses();
				});
				// ]]>
			</script>
			<div class="pf-element pf-full-width">
				<table id="p_muid_addresses_table">
					<thead>
						<tr>
							<th>Type</th>
							<th>Address 1</th>
							<th>Address 2</th>
							<th>City</th>
							<th>State</th>
							<th>Zip</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->entity->addresses as $cur_address) { ?>
						<tr>
							<td><?php echo htmlspecialchars($cur_address['type']); ?></td>
							<td><?php echo htmlspecialchars($cur_address['address_1']); ?></td>
							<td><?php echo htmlspecialchars($cur_address['address_2']); ?></td>
							<td><?php echo htmlspecialchars($cur_address['city']); ?></td>
							<td><?php echo htmlspecialchars($cur_address['state']); ?></td>
							<td><?php echo htmlspecialchars($cur_address['zip']); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<input type="hidden" id="p_muid_addresses" name="addresses" />
			</div>
			<div id="p_muid_address_dialog" title="Add an Address" style="display: none;">
				<div class="pf-form">
					<div class="pf-element">
						<label><span class="pf-label">Type</span>
							<input class="pf-field ui-widget-content ui-corner-all" type="text" size="24" name="cur_address_type" id="p_muid_cur_address_type" /></label>
					</div>
					<div class="pf-element">
						<label><span class="pf-label">Address 1</span>
							<input class="pf-field ui-widget-content ui-corner-all" type="text" size="24" name="cur_address_addr1" id="p_muid_cur_address_addr1" /></label>
					</div>
					<div class="pf-element">
						<label><span class="pf-label">Address 2</span>
							<input class="pf-field ui-widget-content ui-corner-all" type="text" size="24" name="cur_address_addr2" id="p_muid_cur_address_addr2" /></label>
					</div>
					<div class="pf-element">
						<span class="pf-label">City, State, Zip</span>
						<input class="pf-field ui-widget-content ui-corner-all" type="text" size="8" name="cur_address_city" id="p_muid_cur_address_city" />
						<input class="pf-field ui-widget-content ui-corner-all" type="text" size="2" name="cur_address_state" id="p_muid_cur_address_state" />
						<input class="pf-field ui-widget-content ui-corner-all" type="text" size="5" name="cur_address_zip" id="p_muid_cur_address_zip" />
					</div>
				</div>
				<br class="pf-clearing" />
			</div>
			<?php } ?>
			<br class="pf-clearing" />
		</div>
		<?php } if ( $this->display_abilities ) { ?>
		<div id="p_muid_tab_abilities">
			<script type="text/javascript">
				// <![CDATA[
				pines(function(){
					var sections = $("#p_muid_form .abilities_accordian");
					sections.accordion({
						autoHeight: false,
						collapsible: true,
						active: false
					});
					$("#p_muid_form button.expand_all").button().click(function(){
						sections.each(function(){
							var section = $(this);
							if (section.accordion("option", "active") === false)
								section.accordion("activate", 0);
						});
					});
					$("#p_muid_form button.collapse_all").button().click(function(){
						sections.accordion("activate", false);
					});
				});
				// ]]>
			</script>
			<div class="pf-element">
				<span class="pf-label">Inherit</span>
				<label>
					<input class="pf-field" type="checkbox" name="inherit_abilities" value="ON" <?php echo ($this->entity->inherit_abilities ? 'checked="checked" ' : ''); ?>/>
					&nbsp;Inherit additional abilities from groups.
				</label>
			</div>
			<div class="pf-element pf-full-width ui-helper-clearfix">
				<div style="float: right; clear: both;">
					<button type="button" class="expand_all">Expand All</button>
					<button type="button" class="collapse_all">Collapse All</button>
				</div>
			</div>
			<br class="pf-clearing" />
			<?php foreach ($this->sections as $cur_section) {
				if ($cur_section == 'system') {
					$section_abilities = (array) $pines->info->abilities;
				} else {
					$section_abilities = (array) $pines->info->$cur_section->abilities;
				}
				if (!$section_abilities) continue; ?>
			<div class="abilities_accordian">
				<h3><a href="#"><?php echo ($cur_section == 'system') ? htmlspecialchars($pines->info->name) : htmlspecialchars($pines->info->$cur_section->name); ?> (<?php echo htmlspecialchars($cur_section); ?>)</a></h3>
				<div>
					<div class="pf-element">
						<?php foreach ($section_abilities as $cur_ability) { ?>
						<label>
							<input type="checkbox" name="<?php echo htmlspecialchars($cur_section); ?>[]" value="<?php echo htmlspecialchars($cur_ability[0]); ?>" <?php echo (array_search("{$cur_section}/{$cur_ability[0]}", $this->entity->abilities) !== false) ? 'checked="checked" ' : ''; ?>/>
							<?php echo htmlspecialchars($cur_ability[1]); ?>&nbsp;<small><?php echo htmlspecialchars($cur_ability[2]); ?></small>
						</label>
						<br class="pf-clearing" />
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
			<br class="pf-clearing" />
		</div>
		<?php } if (in_array('attributes', $pines->config->com_user->user_fields)) { ?>
		<div id="p_muid_tab_attributes">
			<script type="text/javascript">
				// <![CDATA[
				pines(function(){
					// Attributes
					var attributes = $("#p_muid_tab_attributes input[name=attributes]");
					var attributes_table = $("#p_muid_tab_attributes .attributes_table");
					var attribute_dialog = $("#p_muid_tab_attributes .attribute_dialog");

					attributes_table.pgrid({
						pgrid_paginate: false,
						pgrid_toolbar: true,
						pgrid_toolbar_contents : [
							{
								type: 'button',
								text: 'Add Attribute',
								extra_class: 'picon picon-list-add',
								selection_optional: true,
								click: function(){
									attribute_dialog.dialog('open');
								}
							},
							{
								type: 'button',
								text: 'Remove Attribute',
								extra_class: 'picon picon-list-remove',
								click: function(e, rows){
									rows.pgrid_delete();
									update_attributes();
								}
							}
						],
						pgrid_view_height: "300px"
					});

					// Attribute Dialog
					attribute_dialog.dialog({
						bgiframe: true,
						autoOpen: false,
						modal: true,
						width: 500,
						buttons: {
							"Done": function(){
								var cur_attribute_name = $("#p_muid_cur_attribute_name").val();
								var cur_attribute_value = $("#p_muid_cur_attribute_value").val();
								if (cur_attribute_name == "" || cur_attribute_value == "") {
									alert("Please provide both a name and a value for this attribute.");
									return;
								}
								var new_attribute = [{
									key: null,
									values: [
										cur_attribute_name,
										cur_attribute_value
									]
								}];
								attributes_table.pgrid_add(new_attribute);
								$(this).dialog('close');
							}
						},
						close: function(){
							update_attributes();
						}
					});

					var update_attributes = function(){
						$("#p_muid_cur_attribute_name").val("");
						$("#p_muid_cur_attribute_value").val("");
						attributes.val(JSON.stringify(attributes_table.pgrid_get_all_rows().pgrid_export_rows()));
					};

					update_attributes();
				});
				// ]]>
			</script>
			<div class="pf-element pf-full-width">
				<table class="attributes_table">
					<thead>
						<tr><th>Name</th><th>Value</th></tr>
					</thead>
					<tbody>
						<?php foreach ($this->entity->attributes as $cur_attribute) { ?>
						<tr><td><?php echo htmlspecialchars($cur_attribute['name']); ?></td><td><?php echo htmlspecialchars($cur_attribute['value']); ?></td></tr>
						<?php } ?>
					</tbody>
				</table>
				<input type="hidden" name="attributes" />
			</div>
			<div class="attribute_dialog" style="display: none;" title="Add an Attribute">
				<div class="pf-form">
					<div class="pf-element">
						<label><span class="pf-label">Name</span>
							<input class="pf-field ui-widget-content ui-corner-all" type="text" id="p_muid_cur_attribute_name" size="24" /></label>
					</div>
					<div class="pf-element">
						<label><span class="pf-label">Value</span>
							<input class="pf-field ui-widget-content ui-corner-all" type="text" id="p_muid_cur_attribute_value" size="24" /></label>
					</div>
				</div>
				<br style="clear: both; height: 1px;" />
			</div>
			<br class="pf-clearing" />
		</div>
		<?php } ?>
	</div>

	<div class="pf-element pf-buttons">
		<br />
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php echo $this->entity->guid; ?>" />
		<?php } ?>
		<input class="pf-button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Submit" />
		<?php if (gatekeeper('com_user/listusers')) { ?>
		<input class="pf-button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="pines.get('<?php echo htmlspecialchars(pines_url('com_user', 'listusers')); ?>');" value="Cancel" />
		<?php } else { ?>
		<input class="pf-button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="pines.get('<?php echo htmlspecialchars(pines_url()); ?>');" value="Cancel" />
		<?php } ?>
	</div>
</form>