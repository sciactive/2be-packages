<?php
/**
 * Stock Entry entity helper.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if ($this->render == 'body' && gatekeeper('com_sales/seestock')) {
$module = new module('com_entityhelper', 'default_helper');
$module->render = $this->render;
$module->entity = $this->entity;
echo $module->render();
?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Properties</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">GUID</td>
				<td><?php e($this->entity->guid); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">SKU</td>
				<td><?php e($this->entity->product->sku); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Product</td>
				<td><a data-entity="<?php e($this->entity->product->guid); ?>" data-entity-context="com_sales_product"><?php e($this->entity->product->name); ?></a></td>
			</tr>
			<?php if (!empty($this->entity->serial)) { ?>
			<tr>
				<td style="font-weight:bold;">Serial</td>
				<td><?php e($this->entity->serial); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Vendor</td>
				<td><a data-entity="<?php e($this->entity->vendor->guid); ?>" data-entity-context="com_sales_vendor"><?php e($this->entity->vendor->name); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Location</td>
				<td>
					<?php if (isset($this->entity->location->guid)) { ?>
					<a data-entity="<?php e($this->entity->location->guid); ?>" data-entity-context="group"><?php echo $this->entity->location->guid ? h($this->entity->location->info('name')) : ''; ?></a>
					<?php } else { ?>
					Removed from inventory.
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Cost</td>
				<td><?php echo isset($this->entity->cost) ? '$'.h(number_format($this->entity->cost, 2)) : ''; ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Available</td>
				<td><?php echo $this->entity->available ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php if (gatekeeper('com_sales/managestock')) { ?>
			<tr>
				<td style="font-weight:bold;">Last Transaction</td>
				<td><?php e($this->entity->last_reason()); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
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
			$txs = (array) $_->entity_manager->get_entities(
					array('class' => com_sales_tx),
					array('&',
						'tag' => array('com_sales', 'transaction', 'stock_tx'),
						'ref' => array('stock', $this->entity)
					)
				);
			foreach ($txs as $cur_tx) { ?>
			<tr>
				<td><a data-entity="<?php e($cur_tx->guid); ?>" data-entity-context="com_sales_tx"><?php e($cur_tx->guid); ?></a></td>
				<td><?php e(ucwords($cur_tx->type)); ?></td>
				<td><?php e(format_date($cur_tx->p_cdate, 'full_short')); ?></td>
				<td><a data-entity="<?php e($cur_tx->user->guid); ?>" data-entity-context="user"><?php e($cur_tx->user->name); ?></a></td>
				<td><a data-entity="<?php e($cur_tx->ref->guid); ?>" data-entity-context="<?php echo isset($cur_tx->ref) ? h(str_replace('hook_override_', '', get_class($cur_tx->ref))) : ''; ?>"><?php echo isset($cur_tx->ref) ? h($cur_tx->ref->info('name')) : ''; ?></a></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;">Reason</td>
				<td colspan="3"><?php e($cur_tx->reason); ?></td>
			</tr>
			<?php if ($cur_tx->old_available !== $cur_tx->new_available) { ?>
			<tr>
				<td colspan="2" style="text-align: right;">Availability</td>
				<td colspan="3"><?php echo $cur_tx->new_available ? 'Became Available' : 'Became Unavailable'; ?></td>
			</tr>
			<?php } if ($cur_tx->old_location->guid !== $cur_tx->new_location->guid) { ?>
			<tr>
				<td colspan="2" style="text-align: right;">Location</td>
				<td colspan="3"><?php echo ($cur_tx->old_location->guid ? '<a data-entity="'.h($cur_tx->old_location->guid).'" data-entity-context="group">'.h($cur_tx->old_location->name).'</a> to ' : 'Entered stock at ').($cur_tx->new_location->guid ? '<a data-entity="'.h($cur_tx->new_location->guid).'" data-entity-context="group">'.h($cur_tx->new_location->name).'</a>' : 'Removed from Inventory'); ?></td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
</div>
<?php } elseif ($this->render == 'footer') {
	if (gatekeeper('com_sales/seestock')) { ?>
<a href="<?php e(pines_url('com_sales', 'stock/list', array('location' => $this->entity->location->guid, 'removed' => isset($this->entity->location->guid) ? 'false' : 'true'))); ?>" class="btn btn-default">View in List</a>
<?php } if (gatekeeper('com_sales/managestock')) { ?>
<a href="<?php e(pines_url('com_sales', 'stock/edit', array('id' => $this->entity->guid))); ?>" class="btn btn-default">Edit</a>
<a href="<?php e(pines_url('com_sales', 'stock/transfer', array('id' => $this->entity->guid))); ?>" class="btn btn-default">Transfer</a>
<?php } }