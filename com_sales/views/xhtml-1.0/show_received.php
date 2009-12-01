<?php
/**
 * Shows the result of received inventory processing.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Received Inventory';
?>
<?php if (empty($this->success)) { ?>
<p>No inventory was received.</p>
<?php return; } ?>

<?php foreach($this->success as $cur_success) { ?>
<div class="pform">
	<div class="element">
		<span class="label">Product</span>
		<span class="field"><?php echo $config->run_sales->get_product_name($cur_success[0]->product_guid); ?></span>
	</div>
	<div class="element">
		<span class="label">Vendor</span>
		<span class="field"><?php echo $config->run_sales->get_vendor_name($cur_success[0]->vendor_guid); ?></span>
	</div>
	<div class="element">
		<span class="label">Serial</span>
		<span class="field"><?php echo $cur_success[0]->serial; ?></span>
	</div>
	<div class="element">
		<span class="label">Received On</span>
		<?php if ($cur_success[1]->has_tag('po')) { ?>
		<span class="field"><?php echo 'PO: '.$cur_success[1]->po_number; ?></span>
		<?php } elseif($cur_success[1]->has_tag('transfer')) { ?>
		<span class="field"><?php echo 'Transfer: '.$cur_success[1]->transfer_number; ?></span>
		<?php } ?>
	</div>
</div>
<?php } ?>