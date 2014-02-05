<?php
/**
 * A view to load the company selector.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	$_.loadjs("<?php e($_->config->location); ?>components/com_customer/includes/jquery.companyselect.js");
	$_.com_customer_autocompany_url = <?php echo json_encode(pines_url('com_customer', 'company/search')); ?>;
</script>