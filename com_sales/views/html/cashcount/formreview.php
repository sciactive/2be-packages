<?php
/**
 * Provides a form for the user to review a cash count.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Reviewing Cash Count ['.h($this->entity->guid).']';
if (isset($this->entity->guid))
	$this->note = 'Created by ' . h($this->entity->user->name) . ' on ' . h(format_date($this->entity->p_cdate, 'date_short')) . ' - Last Modified on ' . h(format_date($this->entity->p_mdate, 'date_short'));
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_sales/cashcount/formreview']);
?>
<script type="text/javascript">

	var p_muid_notice;

	pines(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: false,
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_sales/cashcount/formreview", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		$("#p_muid_grid").pgrid(cur_options);

		p_muid_notice = $.pnotify({
			text: "",
			hide: false,
			closer: false,
			sticker: false,
			history: false,
			animate_speed: 100,
			icon: "ui-icon ui-icon-comment",
			// Setting stack to false causes PNotify to ignore this notice when positioning.
			stack: false,
			after_init: function(pnotify){
				// Remove the notice if the user mouses over it.
				pnotify.mouseout(function(){
					pnotify.pnotify_remove();
				});
			},
			before_open: function(pnotify){
				// This prevents the notice from displaying when it's created.
				pnotify.pnotify({
					before_open: null
				});
				return false;
			}
		});
		$("tbody", "#p_muid_grid").mouseenter(function(){
			p_muid_notice.pnotify_display();
		}).mouseleave(function(){
			p_muid_notice.pnotify_remove();
		}).mousemove(function(e){
			p_muid_notice.css({"top": e.clientY+12, "left": e.clientX+12});
		});
		p_muid_notice.com_sales_update = function(comments){
			if (comments == "") {
				p_muid_notice.pnotify_remove();
			} else {
				p_muid_notice.pnotify({text: pines.safe(comments)});
				if (!p_muid_notice.is(":visible"))
					p_muid_notice.pnotify_display();
			}
		};
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_sales', 'cashcount/savestatus')); ?>">
	<table id="p_muid_grid">
		<thead>
			<tr>
				<th>Time</th>
				<th>Type</th>
				<th>User</th>
				<?php foreach ($this->entity->currency as $cur_denom) { ?>
					<th><?php e($this->entity->currency_symbol . $cur_denom); ?></th>
				<?php } ?>
				<th>Total in Till</th>
				<th>Transaction Total</th>
				<th>Variance</th>
			</tr>
		</thead>
		<tbody>
			<tr onmouseover="p_muid_notice.com_sales_update(<?php e(json_encode($this->entity->comments)); ?>);">
				<td><?php e(format_date($this->entity->p_cdate)); ?></td>
				<td>Cash-In</td>
				<td><?php e($this->entity->user->name); ?></td>
				<?php foreach ($this->entity->count as $cur_float_count) { ?>
				<td><?php e($cur_float_count); ?></td>
				<?php } ?>
				<td>$<?php e($this->entity->float); ?></td>
				<td>$<?php e($this->entity->float); ?></td>
				<td>$0</td>
			</tr>
			<?php foreach ($this->entity->audits as $cur_audit) { ?>
			<tr onmouseover="p_muid_notice.com_sales_update(<?php e(json_encode($cur_audit->comments)); ?>);" <?php echo (($cur_audit->till_total - $cur_audit->total) != 0) ? 'class="ui-state-error"' : ''; ?>>
				<td><?php e(format_date($cur_audit->p_cdate)); ?></td>
				<td>Audit</td>
				<td><?php e($cur_audit->user->name); ?></td>
				<?php foreach ($cur_audit->count as $cur_audit_count) { ?>
				<td><?php e($cur_audit_count); ?></td>
				<?php } ?>
				<td>$<?php e($cur_audit->till_total); ?></td>
				<td>$<?php e($cur_audit->total); ?></td>
				<td>$<?php e($cur_audit->till_total - $cur_audit->total); ?></td>
			</tr>
			<?php } foreach ($this->entity->skims as $cur_skim) { ?>
			<tr onmouseover="p_muid_notice.com_sales_update(<?php e(json_encode($cur_skim->comments)); ?>);">
				<td><?php e(format_date($cur_skim->p_cdate)); ?></td>
				<td>Skim</td>
				<td><?php e($cur_skim->user->name); ?></td>
				<?php foreach ($cur_skim->count as $cur_skim_count) { ?>
				<td><?php e($cur_skim_count); ?></td>
				<?php } ?>
				<td>$<?php e($cur_skim->till_total); ?></td>
				<td>$<?php e($cur_skim->total); ?></td>
				<td>$<?php e(-1 * $cur_skim->total); ?></td>
			</tr>
			<?php } foreach ($this->entity->deposits as $cur_deposit) { ?>
			<tr onmouseover="p_muid_notice.com_sales_update(<?php e(json_encode($cur_deposit->comments)); ?>);" <?php echo ($cur_deposit->status == 'flagged') ? 'class="ui-state-error"' : ''; ?>>
				<td><?php e(format_date($cur_deposit->p_cdate)); ?></td>
				<td>Deposit</td>
				<td><?php e($cur_deposit->user->name); ?></td>
				<?php foreach ($cur_deposit->count as $cur_deposit_count) { ?>
				<td><?php e($cur_deposit_count); ?></td>
				<?php } ?>
				<td>$<?php e($cur_deposit->till_total); ?></td>
				<td>$<?php e($cur_deposit->total); ?></td>
				<td>$<?php e($cur_deposit->total); ?></td>
			</tr>
			<?php } ?>
			<?php if ($this->entity->cashed_out) { ?>
			<tr onmouseover="p_muid_notice.com_sales_update(<?php e(json_encode($this->entity->comments)); ?>);">
				<td><?php e(format_date($this->entity->cashed_out_date)); ?></td>
				<td>Cash-Out</td>
				<td><?php e($this->entity->cashed_out_user->name); ?></td>
				<?php foreach ($this->entity->count_out as $cur_out_count) { ?>
				<td><?php e($cur_out_count); ?></td>
				<?php } ?>
				<td>$<?php e($this->entity->total); ?></td>
				<td>$<?php e($this->entity->total_out); ?></td>
				<td>$<?php e($this->entity->total_out - $this->entity->total); ?></td>
			</tr>
			<?php } else { ?>
			<tr onmouseover="p_muid_notice.com_sales_update(<?php e(json_encode($this->entity->comments)); ?>);">
				<td><?php e(format_date(time())); ?></td>
				<td>Current</td>
				<td></td>
				<?php foreach ($this->entity->count as $cur_count) { ?>
				<td></td>
				<?php } ?>
				<td>$<?php e($this->entity->total); ?></td>
				<td>$0</td>
				<td>$0</td>
			</tr>
			<?php } ?>
		</tbody>
	</table><br />
	<?php if (!empty($this->entity->comments)) { ?>
	<div class="pf-element">
		<span class="pf-label">Comments</span>
		<div class="pf-group">
			<div class="pf-field"><?php e($this->entity->comments); ?></div>
		</div>
	</div>
	<?php } ?>
	<div class="pf-element">
		<label>
			<span class="pf-label">Update Status</span>
			<select class="pf-field" name="status" size="1">
				<option value="closed" <?php echo ($this->entity->status == 'closed') ? 'selected="selected"' : ''; ?>>Closed (Approved)</option>
				<option value="flagged" <?php echo ($this->entity->status == 'flagged') ? 'selected="selected"' : ''; ?>>Flagged (Declined)</option>
				<option value="info_requested" <?php echo ($this->entity->status == 'info_requested') ? 'selected="selected"' : ''; ?>>Info Requested</option>
				<option value="pending" <?php echo ($this->entity->status == 'pending') ? 'selected="selected"' : ''; ?>>Pending</option>
			</select>
		</label>
	</div>
	<div class="pf-element pf-heading">
		<h3>Review Comments</h3>
	</div>
	<div class="pf-element pf-full-width">
		<div class="pf-group pf-full-width" style="margin-left: 0;"><textarea style="width: 100%;" rows="3" cols="35" name="review_comments"><?php e($this->entity->review_comments); ?></textarea></div>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input name="approve" class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn" type="button" onclick="pines.get(<?php e(json_encode(pines_url('com_sales', 'cashcount/list'))); ?>);" value="Cancel" />
	</div>
</form>