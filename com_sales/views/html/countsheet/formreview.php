<?php
/**
 * Provides a form for the user to review a countsheet.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Reviewing Countsheet ['.h($this->entity->guid).'] at '.h($this->entity->group->name);
$this->note = 'Created by '.h($this->entity->user->name).' on '.h(format_date($this->entity->p_cdate, 'full_long')).'.';
if (isset($this->entity->run_count_date))
	$this->note .= ' Run on '.h(format_date($this->entity->run_count_date, 'full_long')).'.';
$_->com_pgrid->load();
?>
<script type="text/javascript">
	$_(function(){
		var options = {
			pgrid_view_height: "auto",
			pgrid_paginate: false,
			pgrid_select: false,
			pgrid_multi_select: false,
			pgrid_resize: false
		};
		$("#p_muid_missing_table, #p_muid_matched_table, #p_muid_potential_table, #p_muid_duplicate_table, #p_muid_history_table, #p_muid_invalid_table")
		.find("tr.ui-priority-primary").bind("mouseover", function(e){
			e.stopImmediatePropagation();
		}).end()
		.pgrid(options)
		.find("tr.expandme").pgrid_expand_rows().filter("tr.collapseme").pgrid_collapse_rows();
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_sales', 'countsheet/savestatus')); ?>">
	<?php if ($this->entity->missing) { ?>
	<div class="panel-group">
		<div class="panel panel-error">
			<a class="panel-heading" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
				<big class="panel-title">Missing Items</big>
			</a>
			<div class="panel-collapse collapse in">
				<div class="panel-body clearfix">
					<table id="p_muid_missing_table">
						<thead>
							<tr>
								<th style="width: 40%;">Name</th>
								<th style="width: 10%;">Qty</th>
								<th style="width: 25%;">SKU</th>
								<th style="width: 25%;">Serials</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->entity->missing as $cur_entry) {
								if ($missing_counted[$cur_entry->product->guid])
									continue;
								$missing_counted[$cur_entry->product->guid] = true;
								?>
							<tr class="ui-state-error">
								<td><?php e($cur_entry->product->name); ?></td>
								<td><?php e($this->entity->missing_count[$cur_entry->product->guid]); ?></td>
								<td><?php e($cur_entry->product->sku); ?></td>
								<td><?php e(implode(', ', $this->entity->missing_serials[$cur_entry->product->guid])); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } if ($this->entity->matched) { ?>
	<div class="panel-group">
		<div class="panel panel-success">
			<a class="panel-heading" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
				<big class="panel-title">Matched Items</big>
			</a>
			<div class="panel-collapse collapse in">
				<div class="panel-body clearfix">
					<table id="p_muid_matched_table">
						<thead>
							<tr>
								<th style="width: 40%;">Name</th>
								<th style="width: 10%;">Qty</th>
								<th style="width: 25%;">SKU</th>
								<th style="width: 25%;">Serials</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->entity->matched as $cur_entry) {
								if ($matched_counted[$cur_entry->product->guid])
									continue;
								$matched_counted[$cur_entry->product->guid] = true;
								?>
							<tr>
								<td><?php e($cur_entry->product->name); ?></td>
								<td><?php e($this->entity->matched_count[$cur_entry->product->guid]); ?></td>
								<td><?php e($cur_entry->product->sku); ?></td>
								<td><?php e(implode(', ', $this->entity->matched_serials[$cur_entry->product->guid])); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } if ($this->entity->potential) { ?>
	<div class="panel-group">
		<div class="panel panel-info">
			<a class="panel-heading" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
				<big class="panel-title">Potential Matches</big>
			</a>
			<div class="panel-collapse collapse in">
				<div class="panel-body clearfix">
					<table id="p_muid_potential_table">
						<thead>
							<tr>
								<th style="width: 10%;">Entry</th>
								<th style="width: 15%;">Potential Match</th>
								<th style="width: 15%;">SKU</th>
								<th style="width: 15%;">Serial</th>
								<th style="width: 15%;">Available</th>
								<th style="width: 15%;">Last Transaction</th>
								<th style="width: 15%;">Location</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($this->entity->potential as $cur_match) {
								?><tr class="parent expandme collapseme ui-priority-primary" title="<?php e($cur_match['name']); ?>"><td><?php echo $cur_match['count'] > 1 ? h("{$cur_match['name']} x {$cur_match['count']}") : h("{$cur_match['name']}"); ?></td><td></td><td></td><td></td><td></td><td></td><td></td></tr><?php
								if ($cur_match['closest']) {
									?><tr class="parent expandme ui-priority-primary child <?php e($cur_match['name']); ?>" title="<?php e($cur_match['name']); ?>_same"><td>Same Location</td><td></td><td></td><td></td><td></td><td></td><td></td></tr><?php
									foreach ($cur_match['closest'] as $cur_closest) {
										?><tr class="child <?php e($cur_match['name']); ?>_same">
											<td></td>
											<td><?php e($cur_closest->product->name); ?></td>
											<td><?php e($cur_closest->product->sku); ?></td>
											<td><?php e($cur_closest->serial); ?></td>
											<td><?php echo $cur_closest->available ? 'Yes' : 'No'; ?></td>
											<td><?php e($cur_closest->last_reason()); ?></td>
											<td><?php e($cur_closest->location->name); ?></td>
										</tr><?php
									}
								}
								if ($cur_match['entries']) {
									?><tr class="parent expandme ui-priority-primary child <?php e($cur_match['name']); ?>" title="<?php e($cur_match['name']); ?>_else"><td>Other Location</td><td></td><td></td><td></td><td></td><td></td><td></td></tr><?php
									foreach ($cur_match['entries'] as $cur_entry) {
										?><tr class="child <?php e($cur_match['name']); ?>_else">
											<td></td>
											<td><?php e($cur_entry->product->name); ?></td>
											<td><?php e($cur_entry->product->sku); ?></td>
											<td><?php e($cur_entry->serial); ?></td>
											<td><?php echo $cur_entry->available ? 'Yes' : 'No'; ?></td>
											<td><?php e($cur_entry->last_reason()); ?></td>
											<td><?php e($cur_entry->location->name); ?></td>
										</tr><?php
									}
								}
							} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } if ($this->entity->duplicate) { ?>
	<div class="panel-group">
		<div class="panel panel-warning">
			<a class="panel-heading" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
				<big class="panel-title" style="margin-bottom: 0;">Duplicate Items</big>
			</a>
			<div class="panel-collapse collapse in">
				<div class="panel-body clearfix">
					<table id="p_muid_duplicate_table">
						<thead>
							<tr>
								<th style="width: 40%;">Name</th>
								<th style="width: 10%;">Qty</th>
								<th style="width: 25%;">SKU</th>
								<th style="width: 25%;">Serials</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->entity->duplicate as $cur_entry) {
								if ($dupe_counted[$cur_entry->product->guid])
									continue;
								$dupe_counted[$cur_entry->product->guid] = true;
								?>
							<tr>
								<td><?php e($cur_entry->product->name); ?></td>
								<td><?php e($this->entity->duplicate_count[$cur_entry->product->guid]); ?></td>
								<td><?php e($cur_entry->product->sku); ?></td>
								<td><?php e(implode(', ', $this->entity->duplicate_serials[$cur_entry->product->guid])); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } if ($this->entity->history) { ?>
	<div class="panel-group">
		<div class="panel panel-warning">
			<a class="panel-heading" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
				<big class="panel-title" style="margin-bottom: 0;">Past Items</big>
			</a>
			<div class="panel-collapse collapse in">
				<div class="panel-body clearfix">
					<table id="p_muid_history_table">
						<thead>
							<tr>
								<th style="width: 40%;">Name</th>
								<th style="width: 25%;">Serial</th>
								<th style="width: 20%;">Last Transaction</th>
								<th style="width: 15%;">Location</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->entity->history as $cur_entry) { ?>
							<tr>
								<td><?php e($cur_entry->product->name); ?></td>
								<td><?php e($cur_entry->serial); ?></td>
								<td><?php e($cur_entry->last_reason()); ?></td>
								<td><?php e($cur_entry->group->name); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } if ($this->entity->invalid) { ?>
	<div class="panel-group">
		<div class="panel panel-default">
			<a class="panel-heading" href="javascript:void(0);" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
				<big class="panel-title">Invalid/Unknown Entries</big>
			</a>
			<div class="panel-collapse collapse in">
				<div class="panel-body clearfix">
					<table id="p_muid_invalid_table">
						<thead>
							<tr>
								<th style="width: 100%;">Entry</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->entity->invalid as $cur_entry) { ?>
							<tr>
								<td><?php e($cur_entry); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php } if (!empty($this->entity->comments)) {?>
	<div class="pf-element">
		<span class="pf-label">Comments</span>
		<div class="pf-group">
			<div class="pf-field"><?php e($this->entity->comments); ?></div>
		</div>
	</div>
	<?php } ?>
	<br class="pf-clearing" />
	<div class="pf-element">
		<label>
			<span class="pf-label">Update Status</span>
			<select class="pf-field form-control" name="status">
				<option value="approved" <?php echo ($this->entity->status == 'approved') ? 'selected="selected"' : ''; ?>>Approved</option>
				<option value="declined" <?php echo ($this->entity->status == 'declined') ? 'selected="selected"' : ''; ?>>Declined</option>
				<option value="info_requested" <?php echo ($this->entity->status == 'info_requested') ? 'selected="selected"' : ''; ?>>Info Requested</option>
				<option value="pending" <?php echo ($this->entity->status == 'pending') ? 'selected="selected"' : ''; ?>>Pending</option>
			</select>
		</label>
	</div>
	<div class="pf-element pf-full-width">
		<label>
			<span class="pf-label">Review Comments</span>
			<span class="pf-group pf-full-width">
				<span class="pf-field" style="display: block;">
					<textarea style="width: 100%;" rows="3" cols="35" name="review_comments"><?php e($this->entity->review_comments); ?></textarea>
				</span>
			</span>
		</label>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input name="approve" class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_sales', 'countsheet/list'))); ?>);" value="Cancel" />
	</div>
</form>