<?php
/**
 * Return entity helper.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if ($this->render == 'body' && gatekeeper('com_sales/listreturns')) {
$module = new module('com_entityhelper', 'default_helper');
$module->render = $this->render;
$module->entity = $this->entity;
echo $module->render();
?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">
		Properties
		<img style="float: right;" src="<?php e(pines_url('com_barcode', 'image', array('code' => "RE{$this->entity->id}", 'height' => '60', 'width' => '300', 'style' => '850'), true)); ?>" alt="Barcode" />
	</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">GUID</td>
				<td><?php e($this->entity->guid); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Return ID</td>
				<td><?php e($this->entity->id); ?></td>
			</tr>
			<?php if (isset($this->entity->sale->guid)) { ?>
			<tr>
				<td style="font-weight:bold;">Attached Sale</td>
				<td><a data-entity="<?php e($this->entity->sale->guid); ?>" data-entity-context="com_sales_sale"><?php e($this->entity->sale->id); ?></a></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Status</td>
				<td><?php e(ucwords($this->entity->status)); ?></td>
			</tr>
			<?php if (!empty($this->entity->process_date)) { ?>
			<tr>
				<td style="font-weight:bold;">Process Date</td>
				<td><?php e(format_date($this->entity->process_date, 'full_short')); ?></td>
			</tr>
			<?php } if (!empty($this->entity->void_date)) { ?>
			<tr>
				<td style="font-weight:bold;">Void Date</td>
				<td><?php e(format_date($this->entity->void_date, 'full_short')); ?></td>
			</tr>
			<?php } if ($_->config->com_sales->com_customer && isset($this->entity->customer->guid)) { ?>
			<tr>
				<td style="font-weight:bold;">Customer</td>
				<td><a data-entity="<?php e($this->entity->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e($this->entity->customer->name); ?></a></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Subtotal</td>
				<td><?php echo isset($this->entity->subtotal) ? '$'.h(number_format($this->entity->subtotal, 2)) : ''; ?></td>
			</tr>
			<?php if (!empty($this->entity->total_specials)) { ?>
			<tr>
				<td style="font-weight:bold;">Specials</td>
				<td><?php echo '$'.h(number_format($this->entity->total_specials, 2)); ?></td>
			</tr>
			<?php } if (!empty($this->entity->item_fees)) { ?>
			<tr>
				<td style="font-weight:bold;">Item Fees</td>
				<td><?php echo '$'.h(number_format($this->entity->item_fees, 2)); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Tax</td>
				<td><?php echo isset($this->entity->taxes) ? '$'.h(number_format($this->entity->taxes, 2)) : ''; ?></td>
			</tr>
			<?php if (!empty($this->entity->return_fees)) { ?>
			<tr>
				<td style="font-weight:bold;">Return Fees</td>
				<td><?php echo '$'.h(number_format($this->entity->return_fees, 2)); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Total</td>
				<td><?php echo isset($this->entity->total) ? '$'.h(number_format($this->entity->total, 2)) : ''; ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Refunded</td>
				<td><?php echo isset($this->entity->amount_tendered) ? '$'.h(number_format($this->entity->amount_tendered, 2)) : ''; ?></td>
			</tr>
		</tbody>
	</table>
</div>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Products <small>(<?php
		$number = 0;
		foreach ($this->entity->products as $cur_product) {
			$number += (int) $cur_product['quantity'];
		}
		echo $number; ?>)</small></h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Item</th>
				<th>Delivery</th>
				<th>Serial</th>
				<th style="text-align: right;">Qty</th>
				<th style="text-align: right;">Price</th>
				<th style="text-align: right;">Return Fee</th>
				<th style="text-align: right;">Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ((array) $this->entity->products as $cur_product) { ?>
			<tr>
				<td><?php e($cur_product['entity']->sku); ?></td>
				<td><a data-entity="<?php e($cur_product['entity']->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_product['entity']->name); ?></a></td>
				<td><?php e(ucwords(str_replace('-', ' ', $cur_product['delivery']))); ?></td>
				<td><?php
				$text = array();
				if (isset($cur_product['serial']))
					$text[] = $cur_product['serial'];
				e(implode(' ', $text));
				?></td>
				<td style="text-align: right;"><?php e($cur_product['quantity']); ?></td>
				<td style="text-align: right;">$<?php echo $_->com_sales->round($cur_product['price'], true); ?><?php echo empty($cur_product['discount']) ? '' : h(" - {$cur_product['discount']}"); ?></td>
				<td style="text-align: right;">$<?php echo $_->com_sales->round($cur_product['return_fee'], true); ?></td>
				<td style="text-align: right;">$<?php echo $_->com_sales->round($cur_product['line_total'] - (float) $cur_product['return_fee'], true); ?></td>
			</tr>
			<?php
			$stock_entities = array();
			foreach ((array) $cur_product['stock_entities'] as $cur_stock) {
				if (!isset($cur_stock->guid) || $cur_stock->inArray((array) $cur_product['returned_stock_entities']))
					continue;
				$stock_entities[] = '<a data-entity="'.h($cur_stock->guid).'" data-entity-context="com_sales_stock">'.h($cur_stock->guid.($cur_stock->inArray((array) $cur_product['shipped_entities']) ? ' (Shipped)' : '')).'</a>';
			}
			if ($stock_entities) { ?>
			<tr>
				<td colspan="2" style="text-align: right;">Stock Entries</td>
				<td colspan="6"><?php echo implode(', ', $stock_entities); ?></td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>
<?php if ($this->entity->specials) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Specials</h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr>
				<th>Special</th>
				<th>Applied</th>
				<th style="text-align: right;">Discount</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->entity->specials as $cur_special) { ?>
			<tr>
				<td style="width: 80%;"><a data-entity="<?php e($cur_special['entity']->guid); ?>" data-entity-context="com_sales_special"><?php e(($cur_special['entity']->hide_code ? '' : "{$cur_special['code']} - ").$cur_special['name']); ?></a></td>
				<td style="white-space: pre;"><?php echo $cur_special['before_tax'] ? 'Before Tax' : 'After Tax'; ?></td>
				<td style="text-align: right;">$<?php echo $_->com_sales->round($cur_special['discount'], true); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } if ($this->entity->payments) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Returned Payments</h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr>
				<th>Type</th>
				<th style="text-align: right;">Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->entity->payments as $cur_payment) { ?>
			<tr>
				<td><a data-entity="<?php e($cur_payment['entity']->guid); ?>" data-entity-context="com_sales_payment_type"><?php e($cur_payment['label']); ?></a></td>
				<td style="text-align: right;">$<?php echo $_->com_sales->round($cur_payment['amount'], true); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } if (!empty($this->entity->comments)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Comments</h3>
	<div style="font-size: 75%;"><?php e($this->entity->comments); ?></div>
</div>
<?php } ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Transaction History</h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr>
				<th>GUID</th>
				<th>Type</th>
				<th>Date</th>
				<th>User</th>
				<th>Reference</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$txs = (array) $_->nymph->getEntities(
					array('class' => com_sales_tx),
					array('&',
						'tag' => array('com_sales', 'transaction'),
						'ref' => array('ticket', $this->entity)
					),
					array('|',
						'tag' => array('sale_tx', 'payment_tx')
					)
				);
			foreach ($txs as $cur_tx) {
				$ref = isset($cur_tx->item->guid) ? $cur_tx->item : $cur_tx->ref; ?>
			<tr>
				<td><a data-entity="<?php e($cur_tx->guid); ?>" data-entity-context="com_sales_tx"><?php e($cur_tx->guid); ?></a></td>
				<td><?php e(ucwords(str_replace('_', ' ', $cur_tx->type))); ?></td>
				<td><?php e(format_date($cur_tx->cdate, 'full_short')); ?></td>
				<td><a data-entity="<?php e($cur_tx->user->guid); ?>" data-entity-context="user"><?php e($cur_tx->user->name); ?></a></td>
				<td><a data-entity="<?php e($ref->guid); ?>" data-entity-context="<?php echo isset($ref) ? h(str_replace('hook_override_', '', get_class($ref))) : ''; ?>"><?php echo isset($ref) ? h($ref->info('name')) : ''; ?></a></td>
			</tr>
			<?php if (isset($cur_tx->amount)) { ?>
			<tr>
				<td colspan="2" style="text-align: right;">Amount</td>
				<td colspan="3">$<?php echo $_->com_sales->round($cur_tx->amount, true); ?></td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>
<?php } elseif ($this->render == 'footer') { ?>
<a href="<?php e(pines_url('com_sales', 'return/receipt', array('id' => $this->entity->guid))); ?>" class="btn btn-default">Receipt</a>
<?php if (gatekeeper('com_sales/listreturns')) { ?>
<a href="<?php e(pines_url('com_sales', 'return/list', array('location' => $this->entity->group->guid, 'descendants' => 'false', 'all_time' => 'false', 'start_date' => format_date($this->entity->cdate, 'custom', 'Y-m-d'), 'end_date' => format_date(strtotime('+1 day', $this->entity->cdate), 'custom', 'Y-m-d')))); ?>" class="btn btn-default">View in List</a>
<?php } if (gatekeeper('com_sales/editreturn')) { ?>
<a href="<?php e(pines_url('com_sales', 'return/edit', array('id' => $this->entity->guid))); ?>" class="btn btn-default">Edit</a>
<?php } }