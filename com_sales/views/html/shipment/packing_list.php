<?php
/**
 * Provides a packing list for a shipment.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Packing List for Shipment '.h($this->entity->id);

if ($_->current_template != 'tpl_print') {
?>
<style type="text/css" media="print">
	#p_muid_print_link {display: none;}
</style>
<div id="p_muid_print_link" style="text-align: right;">
	<a href="<?php e(pines_url('com_sales', 'shipment/packinglist', array('id' => $this->entity->guid, 'template' => 'tpl_print'))); ?>" target="_blank">Printer Friendly Version</a>
</div>
<?php } ?>
<div class="pf-form" id="p_muid_form">
	<div class="jumbotron pull-right" style="padding-top: 30px; padding-bottom: 30px;">
		<h2><?php e($this->entity->ref->info('name')); ?></h2>
	</div>
	<div class="pf-element" style="margin: 2em;">
		<span class="pf-label"><span class="label label-default">Ship To</span></span>
		<div class="pf-group">
			<div class="pf-field">
				<strong><?php e($this->entity->shipping_address->name); ?></strong><br />
				<?php if ($this->entity->shipping_address->address_type == 'us') { if (!empty($this->entity->shipping_address->address_1)) { ?>
				<?php e($this->entity->shipping_address->address_1.' '.$this->entity->shipping_address->address_2); ?><br />
				<?php e($this->entity->shipping_address->city); ?>, <?php e($this->entity->shipping_address->state); ?> <?php e($this->entity->shipping_address->zip); ?>
				<?php } } else { ?>
				<?php echo str_replace("\n", '<br />', h($this->entity->shipping_address->address_international)); ?>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="pf-element pf-full-width">
		<table class="table table-bordered table-condensed" style="border-left: none; border-right: none; border-bottom: none;">
			<tr>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">Shipment ID</span></td>
				<td style="width: 23%;"><?php e($this->entity->id); ?></td>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">Ship Date</span></td>
				<td style="width: 23%;"><?php e(format_date($this->entity->cdate, 'date_med')); ?></td>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">Fulfilled By</span></td>
				<td style="width: 23%;"><?php e($this->entity->user->name); ?></td>
			</tr>
			<tr>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">Shipper</span></td>
				<td style="width: 23%;"><?php e($this->entity->shipper->name); ?></td>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">ETA</span></td>
				<td style="width: 23%;"><?php echo ($this->entity->eta ? h(format_date($this->entity->eta, 'date_med')) : ''); ?></td>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">Reference</span></td>
				<td style="width: 23%;"><?php e($this->entity->ref->info('name')); ?></td>
			</tr>
			<tr>
				<td style="width: 10%; text-align: right; border-left: none;"><span class="label label-default">Tracking #</span></td>
				<td colspan="5"><div style="white-space: pre; font-family: monospace;"><?php echo isset($this->entity->tracking_numbers) ? h(implode("\n", $this->entity->tracking_numbers)) : ''; ?></div></td>
			</tr>
		</table>
	</div>
	<div class="pf-element pf-full-width">
		<table class="table table-bordered table-condensed">
			<tr>
				<th>Quantity</th>
				<th>SKU</th>
				<th>Serial</th>
				<th>Item</th>
			</tr>
			<?php foreach ($this->entity->products as $key => $cur_product) { ?>
			<tr>
				<td style="text-align: right;"><?php e(count($cur_product['stock_entities'])); ?></td>
				<td><?php e($cur_product['entity']->sku); ?></td>
				<td><?php echo $cur_product['stock_entities'][0]->serial ? h($cur_product['stock_entities'][0]->serial) : 'N/A'; ?></td>
				<td style="width: 85%;"><?php e($cur_product['entity']->name); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	<?php if (!empty($this->entity->notes)) { ?>
	<div class="pf-element" style="margin: 2em;">
		<span class="pf-label"><span class="label label-default">Notes</span></span>
		<div class="pf-group">
			<div class="pf-field" style="white-space: pre; font-family: monospace;"><?php e($this->entity->notes); ?></div>
		</div>
	</div>
	<?php } ?>
</div>