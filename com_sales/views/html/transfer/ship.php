<?php
/**
 * Provides a form for the user to ship a transfer.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Shipping Transfer ['.h($this->entity->guid).']';
$products = array();
foreach ($this->entity->products as $cur_product) {
	if (isset($products[$cur_product->guid])) {
		$products[$cur_product->guid]['quantity']++;
	} else {
		$products[$cur_product->guid] = array(
			'entity' => $cur_product,
			'quantity' => 1
		);
	}
}
?>
<style type="text/css">
	#p_muid_product_table td {
		vertical-align: top;
	}
	#p_muid_product_table td textarea {
		width: 100%;
		margin: -.2em;
	}
</style>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_sales', 'transfer/saveship')); ?>">
	<div class="pf-element">
		<span class="pf-label">Reference #</span>
		<span class="pf-field"><?php e($this->entity->reference_number); ?></span>
	</div>
	<div class="row" style="clear: both;">
		<div class="col-sm-6">
			<div class="pf-element">
				<strong class="pf-label">Origin</strong>
				<div class="pf-group">
					<div class="pf-field">
						<?php e($this->entity->origin->name); ?>
						<address>
							<?php if ($this->entity->origin->address_type == 'us') {
								e($this->entity->origin->address_1);
								if (!empty($this->entity->origin->address_2))
									e($this->entity->origin->address_2);
								e("{$this->entity->origin->city}, {$this->entity->origin->state} {$this->entity->origin->zip}");
							} else { ?>
							<pre><?php e($this->entity->origin->address_international); ?></pre>
							<?php } ?>
						</address>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="pf-element">
				<strong class="pf-label">Destination</strong>
				<div class="pf-group">
					<div class="pf-field">
						<?php e($this->entity->destination->name); ?>
						<address>
							<?php if ($this->entity->destination->address_type == 'us') {
								e($this->entity->destination->address_1);
								if (!empty($this->entity->destination->address_2))
									e($this->entity->destination->address_2);
								e("{$this->entity->destination->city}, {$this->entity->destination->state} {$this->entity->destination->zip}");
							} else { ?>
							<pre><?php e($this->entity->destination->address_international); ?></pre>
							<?php } ?>
						</address>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="pf-element">
		<span class="pf-label">Shipper</span>
		<span class="pf-field"><?php e($this->entity->shipper->name); ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">ETA</span>
		<span class="pf-field"><?php e(format_date($this->entity->eta, 'date_long')); ?></span>
	</div>
	<div class="pf-element pf-full-width">
		<span class="pf-label">Products</span>
		<span class="pf-note">Please enter one serial number per line in the spaces provided.</span>
		<div class="pf-group">
			<div class="pf-field">
				<table id="p_muid_product_table" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Product</th>
							<th>Quantity</th>
							<th>Serials</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($products as $cur_product) { ?>
						<tr>
							<td><?php e($cur_product['entity']->sku); ?></td>
							<td><?php e($cur_product['entity']->name); ?></td>
							<td><?php e($cur_product['quantity']); ?></td>
							<td>
								<?php if ($cur_product['entity']->serialized) { ?>
								<textarea cols="20" rows="2" name="serials_<?php e($cur_product['entity']->guid); ?>"></textarea>
								<?php } else { ?>
								Not serialized.
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php if (!empty($this->entity->comments)) { ?>
	<div class="pf-element pf-heading">
		<h3>Comments</h3>
	</div>
	<div class="pf-element pf-full-width">
		<div style="white-space: pre-wrap; padding-bottom: .5em;"><?php e($this->entity->comments); ?></div>
	</div>
	<?php } ?>
	<div class="pf-element pf-buttons">
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<input type="hidden" id="p_muid_save" name="save" value="" />
		<input class="pf-button btn btn-primary" type="submit" name="submit" value="Ship" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_sales', 'transfer/list'))); ?>);" value="Cancel" />
	</div>
</form>