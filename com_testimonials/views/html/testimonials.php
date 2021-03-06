<?php
/**
 * The view to load into the head section to attach css and javascript for a testimonial module.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$save_testimonial = pines_url('com_testimonials', 'testimonial/save');
$get_testimonials = pines_url('com_testimonials', 'testimonial/get_testimonials');
$show_entity_help = gatekeeper('com_testimonials/showentityhelp');
$viewanonauthor = gatekeeper('com_testimonials/viewanonauthor');
$logged_in = !empty($_SESSION['user']);
$customer = ($logged_in && $_SESSION['user']->hasTag('customer'));
$customer_guid = ($logged_in && $customer) ? $_SESSION['user']->guid : '';
?>

<link rel="stylesheet" type="text/css" href="<?php echo pines_url('com_testimonials', 'testimonial_css'); ?>">
<script type="text/javascript">
	var save_testimonial_url = <?php echo json_encode($save_testimonial); ?>;
	var get_testimonials_url = <?php echo json_encode($get_testimonials); ?>;
	var show_entity_help = <?php echo json_encode($show_entity_help); ?>;
	var viewanonauthor = <?php echo json_encode($viewanonauthor); ?>;
	var logged_in = <?php echo json_encode($logged_in); ?>;
	var customer = <?php echo json_encode($customer); ?>;
	var customer_guid = <?php echo json_encode($customer_guid); ?>;
</script>
<script type="text/javascript" src="<?php e($_->config->location); ?>components/com_testimonials/includes/<?php echo ($_->config->debug_mode) ? 'testimonial' : 'testimonial.min'; ?>.js"></script>
