<?php
/**
 * Lists groups to choose default customer groups.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Default Customer Groups';
$_->com_pgrid->load();
?>
<script type="text/javascript">
	$_(function(){
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
					$("input:checkbox", rows).attr("checked", "true");
				}},
				{type: 'button', text: 'None', title: 'Check None', extra_class: 'picon picon-dialog-cancel', selection_optional: true, return_all_rows: true, click: function(e, rows){
					$("input:checkbox", rows).removeAttr("checked");
				}}
			],
			pgrid_sort_col: 3,
			pgrid_sort_ord: "asc",
			pgrid_child_prefix: "ch_",
			pgrid_paginate: false,
			pgrid_view_height: "300px"
		});
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_customer', 'defaultgroupssave')); ?>">
	<div class="pf-element pf-full-width">
		<span class="pf-label">Default Groups</span>
		<span class="pf-note">New customers created through the CRM will be placed in these groups. Newly registering customers will use the user manager's defaults.</span>
		<div class="pf-group pf-full-width">
			<label><input type="radio" class="pf-field" name="group" value="0" /> No default primary group.</label>
			<br />
			<div class="pf-field">
				<table id="p_muid_group_grid">
					<thead>
						<tr>
							<th>Primary</th>
							<th>Secondary</th>
							<th>Name</th>
							<th>Groupname</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($this->groups as $cur_group) { ?>
						<tr title="<?php e($cur_group->guid); ?>" class="<?php echo $cur_group->get_children() ? 'parent ' : ''; ?><?php echo (isset($cur_group->parent) && $cur_group->parent->inArray($this->groups)) ? h("child ch_{$cur_group->parent->guid} ") : ''; ?>">
							<td><input type="radio" name="group" value="<?php e($cur_group->guid); ?>" <?php echo $cur_group->default_customer_primary ? 'checked="checked" ' : ''; ?>/></td>
							<td><input type="checkbox" name="groups[]" value="<?php e($cur_group->guid); ?>" <?php echo $cur_group->default_customer_secondary ? 'checked="checked" ' : ''; ?>/></td>
							<td><?php e($cur_group->name); ?></td>
							<td><a data-entity="<?php e($cur_group->guid); ?>" data-entity-context="group"><?php e($cur_group->groupname); ?></a></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-primary" type="submit" value="Save" />
	</div>
</form>