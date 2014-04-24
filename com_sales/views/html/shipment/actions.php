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
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Shipment Actions';
?>
<script type="text/javascript">
	$_(function(){
		$("#p_muid_email").click(function(){
			$_.get(<?php echo json_encode(pines_url('com_sales', 'shipment/sendemail', array('id' => $this->entity->guid))); ?>);
		});
		$("#p_muid_print").click(function(){
			window.print();
		});
		$("#p_muid_delivered").click(function(){
			$_.get(<?php echo json_encode(pines_url('com_sales', 'shipment/delivered', array('id' => $this->entity->guid))); ?>);
		});
	});
</script>
<div style="text-align: center;">
	<?php if (isset($this->entity->ref->customer->email)) { ?>
	<button id="p_muid_email" class="btn btn-default"><i class="fa fa-envelope"></i> Email Customer</button>
	<br /><br />
	<?php } ?>
	<button id="p_muid_print" class="btn btn-default"><i class="fa fa-print"></i> Print This Page</button>
	<br /><br />
	<button id="p_muid_delivered" class="btn btn-default"><i class="fa fa-truck"></i> Mark <?php echo $this->entity->delivered ? 'Not ' : ''; ?>Delivered</button>
</div>