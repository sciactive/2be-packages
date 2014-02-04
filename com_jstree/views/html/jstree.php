<?php
/**
 * A view to load jsTree.
 *
 * @package Components\jstree
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadjs("<?php e($_->config->location); ?>components/com_jstree/includes/<?php echo $_->config->debug_mode ? 'jquery.jstree.js' : 'jquery.jstree.min.js'; ?>");
	pines(function(){
		$.jstree.defaults.core.animation = 100;
		$.jstree._themes = "<?php e($_->config->location); ?>components/com_jstree/includes/themes/";
		<?php if ($_->depend->check('component', 'com_uasniffer') && $_->depend->check('browser', 'mobile')) { ?>
		$.jstree.defaults.themes.theme = "mobile";
		<?php } else { ?>
		$.jstree.defaults.themes.theme = "apple";
		<?php } ?>
	});
</script>