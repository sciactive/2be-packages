<?php
/**
 * Provides actions to perform with a receipt.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Receipt Actions';

if (!isset($this->entity->customer->email) && !$_->config->com_sales->receipt_printer) {
	$this->detach();
	return;
}

$sale = $this->entity->hasTag('sale');
?>
<script type="text/javascript">
	$_(function(){
		$("#p_muid_email").click(function(){
			$_.get(<?php echo json_encode(pines_url('com_sales', $sale ? 'sale/sendreceipt' : 'return/sendreceipt', array('id' => $this->entity->guid))); ?>);
		});
		<?php if ($_->config->com_sales->receipt_printer) { ?>
		$("#p_muid_print").click(function(){
			// Use window.location so an AJAX wrapper won't try to wrap this.
			window.location = <?php echo json_encode(pines_url('com_sales', $sale ? 'sale/printreceipt' : 'return/printreceipt', array('id' => $this->entity->guid))); ?>;
		});
		<?php if ($_->config->com_sales->auto_receipt_printer && $this->auto_print_ok) { ?>
		$("#p_muid_print").click();
		<?php } } ?>
	});
</script>
<div style="text-align: center;">
	<?php if (isset($this->entity->customer->email)) { ?>
	<button id="p_muid_email" class="btn btn-default"><i class="fa fa-envelope"></i> Email Customer</button>
	<br /><br />
	<?php } if ($_->config->com_sales->receipt_printer) { ?>
	<button id="p_muid_print" class="btn btn-default"><i class="fa fa-print"></i> Receipt Printer</button>
	<?php } ?>
</div>