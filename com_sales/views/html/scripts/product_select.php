<?php
/**
 * A view to load the product selector.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
$_.loadjs("<?php e($_->config->location); ?>components/com_sales/includes/jquery.productselect.js");
$_.com_sales_autoproduct_url = <?php echo json_encode(pines_url('com_sales', 'product/autocomplete')); ?>;
</script>