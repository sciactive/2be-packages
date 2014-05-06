<?php
/**
 * Provides a review of a sale.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Review Your Order';
?>
<style type="text/css">
	#p_muid_item_list {
		text-align: left;
		border-collapse: collapse;
	}
	#p_muid_item_list th {
		padding: 2px;
	}
	#p_muid_item_list tr td p {
		margin: 0;
	}
	#p_muid_item_list .right_text {
		text-align: right;
	}
</style>
<form id="p_muid_review" class="pf-form pf-form-twocol" method="POST" action="<?php e(pines_url('com_shop', 'checkout/reviewsave')); ?>">
	<div class="pf-element">
		<span class="pf-label" style="text-align: right;">Ship To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<span class="pf-note" style="text-align: right;"><a href="<?php e(pines_url('com_shop', 'checkout/shipping', array('noskip' => 'true'))); ?>">Edit Address</a><span style="font-size: 143%; line-height: 1px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></span>
		<div class="pf-group">
			<div class="pf-field">
				<strong><?php e($this->entity->shipping_address->name); ?></strong><br />
				<?php if ($this->entity->shipping_address->address_type == 'us') { ?>
				<?php e("{$this->entity->shipping_address->address_1}\n{$this->entity->shipping_address->address_2}"); ?><br />
				<?php e($this->entity->shipping_address->city); ?>, <?php e($this->entity->shipping_address->state); ?> <?php e($this->entity->shipping_address->zip); ?>
				<?php } else { ?>
				<?php echo str_replace("\n", '<br />', h($this->entity->shipping_address->address_international)); ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="pf-element">
		<span class="pf-label" style="text-align: right;">Bill To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<div class="pf-group">
			<div class="pf-field">
				<strong>
					<?php e($this->entity->customer->name); ?>
					<?php if (isset($this->entity->customer->company->name)) {
						e(" ( {$this->entity->customer->company->name} )");
					} ?>
				</strong><br />
				<?php if ($this->entity->customer->address_type == 'us') { if (!empty($this->entity->customer->address_1)) { ?>
				<?php e($this->entity->customer->address_1.' '.$this->entity->customer->address_2); ?><br />
				<?php e($this->entity->customer->city); ?>, <?php e($this->entity->customer->state); ?> <?php e($this->entity->customer->zip); ?><br />
				<?php } } else {?>
				<?php echo str_replace("\n", '<br />', h($this->entity->customer->address_international)); ?><br />
				<?php } ?>
				<?php e(format_phone($this->entity->customer->phone)); ?>
			</div>
		</div>
	</div>
	<div class="pf-element pf-heading">
		<h3>Products</h3>
	</div>
	<div class="pf-element pf-full-width">
		<table id="p_muid_item_list" style="width: 100%;">
			<thead>
				<tr>
					<th>SKU</th>
					<th>Item</th>
					<th>Description</th>
					<th class="right_text">Qty</th>
					<th class="right_text">Price</th>
					<th class="right_text">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php if (is_array($this->entity->products)) { foreach ($this->entity->products as $cur_product) {
					if ($cur_product['entity']->hide_on_invoice)
						continue;
					?>
				<tr>
					<td><?php e($cur_product['entity']->sku); ?></td>
					<td><?php e($cur_product['entity']->name); ?></td>
					<td><?php echo !empty($cur_product['entity']->receipt_description) ? $cur_product['entity']->receipt_description : $cur_product['entity']->short_description; ?></td>
					<td class="right_text"><?php e($cur_product['quantity']); ?></td>
					<td class="right_text">$<?php echo $_->com_sales->round($cur_product['price'], true); ?><?php echo empty($cur_product['discount']) ? '' : h(" - {$cur_product['discount']}"); ?></td>
					<td class="right_text">$<?php echo $_->com_sales->round($cur_product['line_total'], true); ?></td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>
	<div class="pf-element pf-heading" style="height: 1px; font-size: 1px;">&nbsp;</div>
	<div class="pf-element pf-full-width" style="text-align: right;">
		<div>
			<span class="pf-label" style="width: 80%;">Subtotal</span>
			<span class="pf-field">$<?php echo $_->com_sales->round($this->entity->subtotal, true); ?></span>
		</div>
		<?php if ($this->entity->item_fees > 0) { ?>
		<div>
			<span class="pf-label" style="width: 80%;">Item Fees</span>
			<span class="pf-field">$<?php echo $_->com_sales->round($this->entity->item_fees, true); ?></span>
		</div>
		<?php } ?>
		<div>
			<span class="pf-label" style="width: 80%;">Tax</span>
			<span class="pf-field">$<?php echo $_->com_sales->round($this->entity->taxes, true); ?></span>
		</div>
		<div>
			<strong class="pf-label" style="width: 80%;">Total</strong>
			<strong class="pf-field">$<?php echo $_->com_sales->round($this->entity->total, true); ?></strong>
		</div>
	</div>
	<?php if (!$this->no_form) { ?>
	<div class="pf-element pf-heading">
		<h3>Paying With</h3>
	</div>
	<?php if (is_array($this->entity->payments)) { foreach ($this->entity->payments as $cur_payment) { ?>
	<div class="pf-element">
		<span class="pf-label"><?php e($cur_payment['type']); ?></span>
		<span class="pf-field">$<?php echo $_->com_sales->round($cur_payment['amount'], true); ?></span>
	</div>
	<?php } } ?>
	<div class="pf-element pf-full-width">
		<span class="pf-label">Order Comments</span>
		<textarea class="pf-field" rows="1" cols="35" name="comments"><?php e($this->entity->comments); ?></textarea>
	</div>
	<div class="pf-element pf-buttons">
		<script type="text/javascript">
			$_(function(){
				var buttons = $(":button, :submit, :reset", "#p_muid_review .pf-buttons").click(function(){
					buttons.attr("disabled", "disabled").addClass("disabled");
				});
			});
		</script>
		<input class="pf-button btn btn-primary" type="submit" value="<?php e($_->config->com_shop->complete_order_text); ?>" />
	</div>
	<?php } ?>
</form>