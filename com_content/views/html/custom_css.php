<?php
/**
 * Include custom CSS.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
if (!$_->com_content->get_custom_css()) {
	$this->detach();
	return;
}
?>
<script type="text/javascript">
	<?php foreach ($_->com_content->get_custom_css() as $cur_file) { ?>
	pines.loadcss(<?php echo json_encode($_->config->location.$cur_file); ?>);
	<?php } ?>
</script>