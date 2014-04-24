<?php
/**
 * Provides a form for the user to edit a template.
 *
 * @package Components\mailer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New Template' : 'Editing ['.h($this->entity->name).']';
$this->note = 'Provide template details in this form.';
$_->editor->load();
$_->com_pgrid->load();
?>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_mailer', 'template/save')); ?>">
	<script type="text/javascript">
		$_(function(){
			// Strings
			var strings = $("#p_muid_form [name=replacements]"),
				strings_table = $("#p_muid_form .strings_table"),
				string_dialog = $("#p_muid_form .string_dialog"),
				cur_string = null;

			strings_table.pgrid({
				pgrid_paginate: false,
				pgrid_toolbar: true,
				pgrid_toolbar_contents : [
					{
						type: 'button',
						text: 'Add String',
						extra_class: 'picon picon-edit-text-frame-update',
						selection_optional: true,
						click: function(){
							cur_string = null;
							string_dialog.dialog('open');
						}
					},
					{
						type: 'button',
						text: 'Edit String',
						extra_class: 'picon picon-edit-rename',
						double_click: true,
						click: function(e, rows){
							cur_string = rows;
							string_dialog.find("[name=cur_string_search]").val($_.unsafe(rows.pgrid_get_value(2)));
							string_dialog.find("[name=cur_string_replace]").val($_.unsafe(rows.pgrid_get_value(3)));
							if (rows.pgrid_get_value(4) == "Yes")
								string_dialog.find("[name=cur_string_macros]").attr("checked", true);
							else
								string_dialog.find("[name=cur_string_macros]").removeAttr("checked");
							string_dialog.dialog('open');
						}
					},
					{type: 'button', text: 'Move Up', extra_class: 'picon picon-arrow-up', click: function(e, row){
						if (!row.prev().length)
							return;
						row.prev().pgrid_set_value(1, parseInt(row.prev().pgrid_get_value(1))+1);
						row.pgrid_set_value(1, parseInt(row.pgrid_get_value(1))-1);
						update_strings();
					}},
					{type: 'button', text: 'Move Down', extra_class: 'picon picon-arrow-down', click: function(e, row){
						if (!row.next().length)
							return;
						row.next().pgrid_set_value(1, parseInt(row.next().pgrid_get_value(1))-1);
						row.pgrid_set_value(1, parseInt(row.pgrid_get_value(1))+1);
						update_strings();
					}},
					{type: 'separator'},
					{
						type: 'button',
						text: 'Remove String',
						extra_class: 'picon picon-edit-delete',
						click: function(e, rows){
							rows.pgrid_delete();
							update_strings();
						}
					}
				],
				pgrid_view_height: "300px"
			});

			// String Dialog
			string_dialog.dialog({
				bgiframe: true,
				autoOpen: false,
				modal: true,
				width: 500,
				buttons: {
					"Done": function(){
						var cur_string_search = string_dialog.find("[name=cur_string_search]").val();
						var cur_string_replace = string_dialog.find("[name=cur_string_replace]").val();
						var cur_string_macros = string_dialog.find("[name=cur_string_macros]").is(":checked");
						if (cur_string_search == "") {
							alert("Please provide a string.");
							return;
						}
						if (cur_string == null) {
							// Is this a duplicate?
							var dupe = false;
							// Get the next index.
							var index = 0;
							strings_table.pgrid_get_all_rows().each(function(){
								var cur_row = $(this);
								if (parseInt(cur_row.pgrid_get_value(1)) == index)
									index++;
								if (dupe) return;
								if (cur_row.pgrid_get_value(2) == cur_string_search && cur_row.pgrid_get_value(3) == cur_string_replace && cur_row.pgrid_get_value(4) == (cur_string_macros ? "Yes" : "No"))
									dupe = true;
							});
							if (dupe) {
								$_.notice('There is already a string just like this.');
								return;
							}
							var new_string = [{
								key: null,
								values: [
									$_.safe(index),
									$_.safe(cur_string_search),
									$_.safe(cur_string_replace),
									(cur_string_macros ? "Yes" : "No")
								]
							}];
							strings_table.pgrid_add(new_string);
						} else {
							cur_string.pgrid_set_value(2, $_.safe(cur_string_search));
							cur_string.pgrid_set_value(3, $_.safe(cur_string_replace));
							cur_string.pgrid_set_value(4, (cur_string_macros ? "Yes" : "No"));
						}
						$(this).dialog('close');
					}
				},
				close: function(){
					update_strings();
				}
			});

			var update_strings = function(){
				strings_table.pgrid_import_state({pgrid_sort_col: 1, pgrid_sort_ord: 'asc'});
				strings_table.pgrid_get_all_rows().each(function(i){
					$(this).pgrid_set_value(1, $_.safe(i));
				});
				string_dialog.find("[name=cur_string_search]").val("");
				string_dialog.find("[name=cur_string_replace]").val("");
				string_dialog.find("[name=cur_string_macros]").removeAttr("checked");
				strings.val(JSON.stringify(strings_table.pgrid_get_all_rows().pgrid_export_rows()));
			};

			update_strings();
		});
	</script>
	<ul class="nav nav-tabs" style="clear: both;">
		<li class="active"><a href="#p_muid_tab_general" data-toggle="tab">General</a></li>
		<li><a href="#p_muid_tab_replace" data-toggle="tab">Replace</a></li>
		<li><a href="#p_muid_tab_document" data-toggle="tab">Document</a></li>
		<li><a href="#p_muid_tab_conditions" data-toggle="tab">Conditions</a></li>
	</ul>
	<div id="p_muid_template_tabs" class="tab-content">
		<div class="tab-pane active" id="p_muid_tab_general">
			<?php if (isset($this->entity->guid)) { ?>
			<div class="date_info" style="float: right; text-align: right;">
				<?php if (isset($this->entity->user)) { ?>
				<div>User: <span class="date"><?php e("{$this->entity->user->name} [{$this->entity->user->username}]"); ?></span></div>
				<div>Group: <span class="date"><?php e("{$this->entity->group->name} [{$this->entity->group->groupname}]"); ?></span></div>
				<?php } ?>
				<div>Created: <span class="date"><?php e(format_date($this->entity->p_cdate, 'full_short')); ?></span></div>
				<div>Modified: <span class="date"><?php e(format_date($this->entity->p_mdate, 'full_short')); ?></span></div>
			</div>
			<?php } ?>
			<div class="pf-element">
				<label><span class="pf-label">Name</span>
					<input class="pf-field form-control" type="text" name="name" size="24" value="<?php e($this->entity->name); ?>" /></label>
			</div>
			<div class="pf-element">
				<label><span class="pf-label">Enabled</span>
					<input class="pf-field" type="checkbox" name="enabled" value="ON"<?php echo $this->entity->enabled ? ' checked="checked"' : ''; ?> /></label>
			</div>
			<div class="pf-element pf-heading">
				<h3>Content</h3>
			</div>
			<div class="pf-element pf-full-width">
				Put the text "#content#" (without quotes) where you want the content of the email to go.<br />
				<textarea rows="20" cols="35" class="peditor-email" style="width: 100%;" name="content"><?php e($this->entity->content); ?></textarea>
			</div>
			<div class="pf-element">
				Macros let you replace a string with a value that can change. To
				use them, insert the desired macro string into the content where
				you would like the macro to appear.
			</div>
			<div class="pf-element pf-full-width">
				<span class="pf-label">Universal Macros</span>
				<div class="pf-group">
					<div class="pf-field">
						<table class="table table-condensed table-bordered">
							<thead>
								<tr>
									<th></th>
									<th>Macro String</th>
									<th>Description</th>
									<th>Current Value</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td>#subject#</td>
									<td>The email subject.</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td rowspan="2">Links</td>
									<td>#site_link#</td>
									<td>The URL of the site, to be used in a link.</td>
									<td><?php e($_->config->full_location); ?></td>
								</tr>
								<tr>
									<td>#unsubscribe_link#</td>
									<td>The URL where the user can unsubscribe from further mailings.</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td rowspan="5">Recipient</td>
									<td>#to_username#</td>
									<td>The recipient's username (or groupname).</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td>#to_name#</td>
									<td>The recipient's full name.</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td>#to_first_name#</td>
									<td>The recipient's first name.</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td>#to_last_name#</td>
									<td>The recipient's last name.</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td>#to_email#</td>
									<td>The recipient's email.</td>
									<td>N/A</td>
								</tr>
								<tr>
									<td rowspan="5">Current User</td>
									<td>#username#</td>
									<td>The current user's username.</td>
									<td><?php e($_SESSION['user']->username); ?></td>
								</tr>
								<tr>
									<td>#name#</td>
									<td>The current user's full name.</td>
									<td><?php e($_SESSION['user']->name); ?></td>
								</tr>
								<tr>
									<td>#first_name#</td>
									<td>The current user's first name.</td>
									<td><?php e($_SESSION['user']->name_first); ?></td>
								</tr>
								<tr>
									<td>#last_name#</td>
									<td>The current user's last name.</td>
									<td><?php e($_SESSION['user']->name_last); ?></td>
								</tr>
								<tr>
									<td>#email#</td>
									<td>The current user's email.</td>
									<td><?php e($_SESSION['user']->email); ?></td>
								</tr>
								<tr>
									<td rowspan="6">Date/Time</td>
									<td>#date_short#</td>
									<td>The date. (Short)</td>
									<td><?php e(format_date(time(), 'date_short')); ?></td>
								</tr>
								<tr>
									<td>#date_med#</td>
									<td>The date. (Medium)</td>
									<td><?php e(format_date(time(), 'date_med')); ?></td>
								</tr>
								<tr>
									<td>#date_long#</td>
									<td>The date. (Long)</td>
									<td><?php e(format_date(time(), 'date_long')); ?></td>
								</tr>
								<tr>
									<td>#time_short#</td>
									<td>The time of day. (Short)</td>
									<td><?php e(format_date(time(), 'time_short')); ?></td>
								</tr>
								<tr>
									<td>#time_med#</td>
									<td>The time of day. (Medium)</td>
									<td><?php e(format_date(time(), 'time_med')); ?></td>
								</tr>
								<tr>
									<td>#time_long#</td>
									<td>The time of day. (Long)</td>
									<td><?php e(format_date(time(), 'time_long')); ?></td>
								</tr>
								<tr>
									<td rowspan="2">System</td>
									<td>#system_name#</td>
									<td>The system name.</td>
									<td><?php e($_->config->system_name); ?></td>
								</tr>
								<tr>
									<td>#page_title#</td>
									<td>The page title.</td>
									<td><?php e($_->config->page_title); ?></td>
								</tr>
							</tbody>
						</table>
						<p>Care should be taken when using values from the current user, because they will be empty when no user is logged in.</p>
						<p>Also, the recipient info will only be available if the message is being sent to a registered user/customer.</p>
					</div>
				</div>
			</div>
			<br class="pf-clearing" />
		</div>
		<div class="tab-pane" id="p_muid_tab_replace">
			<div class="pf-element pf-heading">
				<h3>Search and Replace Strings</h3>
				<p>These strings can be used to replace values in emails generated with this template.</p>
			</div>
			<div class="pf-element pf-full-width">
				<table class="strings_table">
					<thead>
						<tr>
							<th>Order</th>
							<th>Search</th>
							<th>Replace</th>
							<th>Before Macros</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ((array) $this->entity->replacements as $key => $cur_string) { ?>
						<tr>
							<td><?php e($key); ?></td>
							<td><?php e($cur_string['search']); ?></td>
							<td><?php e($cur_string['replace']); ?></td>
							<td><?php echo $cur_string['macros'] ? 'Yes' : 'No'; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<input type="hidden" name="replacements" />
			</div>
			<div class="string_dialog" title="Add a String" style="display: none;">
				<div class="pf-form">
					<div class="pf-element">
						<label>
							<span class="pf-label">Search For</span>
							<textarea class="pf-field" name="cur_string_search" rows="3" cols="24"></textarea>
						</label>
					</div>
					<div class="pf-element">
						<label>
							<span class="pf-label">Replace With</span>
							<textarea class="pf-field" name="cur_string_replace" rows="3" cols="24"></textarea>
						</label>
					</div>
					<div class="pf-element">
						<label><span class="pf-label">Before Macros</span>
							<span class="pf-note">You can replace the string either before or after macros have been processed.</span>
							<input class="pf-field" type="checkbox" name="cur_string_macros" value="ON" /></label>
					</div>
				</div>
				<br style="clear: both; height: 1px;" />
			</div>
			<br class="pf-clearing" />
		</div>
		<div class="tab-pane" id="p_muid_tab_document">
			<div class="pf-element pf-heading">
				<h3>Email Document</h3>
				<p>The email document is the HTML portion surrounding the actual
					email. It doesn't provide any visual styling, so normally it
					doesn't need to be customized. It can't be edited with a
					WYSIWYG editor. The default document is based on the email
					boilerplate from <a href="http://www.emailology.org/#1" target="_blank">Emailology</a>.</p>
			</div>
			<div class="pf-element pf-full-width">
				Put the text "#content#" (without quotes) where you want the template content to go.<br />
				<div class="pf-group pf-full-width" style="margin-left: 0;"><textarea style="width: 100%;" rows="20" cols="35" name="document"><?php e($this->entity->document); ?></textarea></div>
			</div>
			<br class="pf-clearing" />
		</div>
		<div class="tab-pane" id="p_muid_tab_conditions">
			<div class="pf-element pf-heading">
				<h3>Template Conditions</h3>
				<p>This template will only be used if these conditions are met.</p>
			</div>
			<div class="pf-element pf-full-width">
				<?php
				$module = new module('system', 'conditions');
				$module->conditions = $this->entity->conditions;
				echo $module->render();
				unset($module);
				?>
			</div>
			<br class="pf-clearing" />
		</div>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_mailer', 'template/list'))); ?>);" value="Cancel" />
	</div>
</form>