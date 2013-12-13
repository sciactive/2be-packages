<?php
/**
 * Provides actions for a shipment.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Shipment Actions';
?>
<script type="text/javascript">
	pines(function(){
		$("#p_muid_email").click(function(){
			pines.get(<?php echo json_encode(pines_url('com_sales', 'shipment/sendemail', array('id' => $this->entity->guid))); ?>);
		});
		$("#p_muid_print").click(function(){
			window.print();
		});
		$("#p_muid_delivered").click(function(){
			pines.get(<?php echo json_encode(pines_url('com_sales', 'shipment/delivered', array('id' => $this->entity->guid))); ?>);
		});
	});
</script>
<div style="text-align: center;">
	<?php if (isset($this->entity->ref->customer->email)) { ?>
	<button id="p_muid_email" class="btn"><i class="icon-envelope"></i> Email Customer</button>
	<br /><br />
	<?php } ?>
	<button id="p_muid_print" class="btn"><i class="icon-print"></i> Print This Page</button>
	<br /><br />
	<button id="p_muid_delivered" class="btn"><i class="icon-truck"></i> Mark <?php echo $this->entity->delivered ? 'Not ' : ''; ?>Delivered</button>
</div>