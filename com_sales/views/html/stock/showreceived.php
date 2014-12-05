<?php
/**
 * Shows the result of received inventory processing.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Received Inventory';
$comments = array();
$i = 1;

if (empty($this->success)) { ?>
<p>No inventory was received.</p>
<?php return; } ?>
<div class="pf-form">
	<div class="pf-element">
		<span class="pf-label">Location</span>
		<span class="pf-field"><a data-entity="<?php e($this->location->guid); ?>" data-entity-context="group"><?php e("{$this->location->name} [{$this->location->groupname}]"); ?></a></span>
	</div>
	<?php foreach ($this->success as $cur_success) {
		$context = '';
		if ($cur_success[1]->hasTag('po'))
			$context = 'com_sales_po';
		elseif ($cur_success[1]->hasTag('transfer'))
			$context = 'com_sales_transfer';
		if (!empty($cur_success[1]->comments) && !$cur_success[1]->inArray($comments))
			$comments[] = $cur_success[1];
		?>
	<div class="pf-element pf-heading">
		<h3>Item <?php echo $i; $i++; ?> (<a data-entity="<?php e($cur_success[0]->guid); ?>" data-entity-context="com_sales_stock">Stock Entry <?php e($cur_success[0]->guid); ?></a>)</h3>
	</div>
	<div class="pf-element">
		<span class="pf-label">Product</span>
		<span class="pf-field"><a data-entity="<?php e($cur_success[0]->product->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_success[0]->product->name); ?></a></span>
	</div>
	<?php if ($cur_success[0]->vendor->guid) { ?>
	<div class="pf-element">
		<span class="pf-label">Vendor</span>
		<span class="pf-field"><a data-entity="<?php e($cur_success[0]->vendor->guid); ?>" data-entity-context="com_sales_vendor"><?php e($cur_success[0]->vendor->name); ?></a></span>
	</div>
	<?php } if (isset($cur_success[0]->serial)) { ?>
	<div class="pf-element">
		<span class="pf-label">Serial</span>
		<span class="pf-field"><?php e($cur_success[0]->serial); ?></span>
	</div>
	<?php } ?>
	<div class="pf-element">
		<span class="pf-label">Received On</span>
		<span class="pf-field"><a data-entity="<?php e($cur_success[1]->guid); ?>" data-entity-context="<?php echo $context; ?>"><?php e($cur_success[1]->info('name')); ?></a></span>
	</div>
	<?php } if ($comments) { ?>
	<div class="pf-element pf-heading">
		<h3>Comments</h3>
	</div>
	<div class="pf-element pf-full-width">
		<ul>
			<?php foreach($comments as $cur_comment) {
				echo '<li>'.h($cur_comment->info('name').' - '.$cur_comment->comments).'</li>';
			} ?>
		</ul>
	</div>
	<?php } ?>
</div>