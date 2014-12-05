<?php
/**
 * Provides a form for the user to edit a cash count.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Cash-In' : (($this->entity->final) ? 'Viewing' : 'Editing').' Float for Cash-In ['.h($this->entity->guid).']';
if (isset($this->entity->guid))
	$this->note = 'Created by ' . h($this->entity->user->name) . ' on ' . h(format_date($this->entity->cdate, 'date_short')) . ' - Last Modified on ' .  h(format_date($this->entity->mdate, 'date_short'));
?>
<style type="text/css" >
	#p_muid_form .amount {
		font-weight: bold;
		display: inline-block;
		width: 3em;
		text-align: right;
	}
	#p_muid_form .entry {
		width: 2em;
		text-align: right;
	}
	#p_muid_form .total {
		border-width: .2em;
		font-weight: bold;
		font-size: 2em;
		position: absolute;
		right: 0;
		top: 0;
		padding: 50px;
		text-align: center;
	}
	/* Add and Remove Classes to show recent changes. */
	#p_muid_form .added {
		border: green solid 1px;
		color: green;
	}
	#p_muid_form .removed {
		border: red solid 1px;
		color: red;
	}
</style>
<script type="text/javascript">
	$_(function(){
		var cash_symbol = <?php echo json_encode($this->entity->currency_symbol); ?>;

		// Update the cash count as money is counted.
		$("#p_muid_form .entry").change(function(){
			update_total();
		}).focus(function(){
			$(this).select();
		});

		var update_total = function(){
			var total_count = 0;
			$("#p_muid_form .entry").each(function() {
				//This looks complicated but it simply multiplies the number of
				//bills/coins for each denomition by its respective value.
				//ex: 5 x 0.25 for 5 quarters that have been counted
				var cur_entry = $(this);
				var subtotal = parseInt(cur_entry.val()) * parseFloat(cur_entry.attr("title"));
				if (isNaN(subtotal))
					cur_entry.val('0');
				else
					total_count += subtotal;
				cur_entry.removeClass("added removed");
			});
			$("#p_muid_total_cashcount").html($_.safe(cash_symbol+total_count.toFixed(2)));
		};

		$("button.clear_btn", "#p_muid_form").click(function(){
			if (confirm("Clear all entered cash counts?")) {
				$("#p_muid_form .entry").each(function() { $(this).val(0); });
				update_total();
			}
			$("#p_muid_form [name=clear_btn]").blur();
		});

		$("button.add_btn", "#p_muid_form").click(function(){
			var cur_button = $(this);
			var cur_input = cur_button.siblings("input.entry");
			cur_input.val(parseInt(cur_input.val()) + 1).change().addClass('added');
			cur_button.blur();
		});
		$("button.remove_btn", "#p_muid_form").click(function(){
			var cur_button = $(this);
			var cur_input = cur_button.siblings("input.entry");
			cur_input.val(parseInt(cur_input.val()) - 1).change().addClass('removed');
			cur_button.blur();
		});

		update_total();
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_sales', 'cashcount/save')); ?>">
	<?php if (!empty($this->entity->review_comments)) {?>
	<div class="pf-element pf-heading">
		<h3>Reviewer Comments</h3>
	</div>
	<div class="pf-element pf-full-width">
		<div class="pf-field"><?php e($this->entity->review_comments); ?></div>
	</div>
	<?php } ?>
	<div class="pf-element pf-heading">
		<?php if (!$this->entity->final) { ?><button class="btn btn-danger clear_btn" type="button" style="display: block; float: right;">Clear All</button><?php } ?>
		<h3>Cash Drawer Contents</h3>
	</div>
	<div class="pf-element pf-full-width" style="position: relative;">
		<?php foreach ($this->entity->currency as $key => $cur_denom) { ?>
		<div class="pf-element">
			<input class="pf-field form-control entry" type="text" name="count_<?php e($key); ?>" title="<?php e($cur_denom); ?>" value="<?php echo (int) $this->entity->count[$key]; ?>" <?php echo $this->entity->final ? 'readonly="readonly"' : ''; ?>/>
			x <span class="amount"><?php e($this->entity->currency_symbol . $cur_denom); ?></span>
			<?php if (!$this->entity->final) { ?>
			<button class="pf-field btn btn-success add_btn" type="button"><i class="fa fa-plus"></i></button>
			<button class="pf-field btn btn-danger remove_btn" type="button"><i class="fa fa-minus"></i></button>
			<?php } ?>
		</div>
		<?php } ?>
		<div class="alert alert-info total">
			<div class="alert-heading">Float Total</div>
			<div id="p_muid_total_cashcount"></div>
		</div>
	</div>
	<div class="pf-element pf-heading">
		<h3>Comments</h3>
	</div>
	<div class="pf-element pf-full-width">
		<div class="pf-group pf-full-width" style="margin-left: 0;"><textarea style="width: 100%;" rows="3" cols="35" name="comments" <?php echo $this->entity->final ? 'readonly="readonly"' : ''; ?>><?php e($this->entity->comments); ?></textarea></div>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } if (!$this->entity->final) { ?>
		<input type="hidden" id="p_muid_save" name="save" value="" />
		<input class="pf-button btn btn-primary" type="submit" name="submit" value="Save" onclick="$('#p_muid_save').val('save');" />
		<input class="pf-button btn btn-primary" type="submit" name="submit" value="Cash In" onclick="$('#p_muid_save').val('commit');" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_sales', 'cashcount/list'))); ?>);" value="Cancel" />
		<?php } else { ?>
		<input class="pf-button btn btn-primary" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_sales', 'cashcount/list'))); ?>);" value="&laquo; Close" />
		<?php } ?>
	</div>
</form>