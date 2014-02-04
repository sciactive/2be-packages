<?php
/**
 * A view to load the elFinder file manager.
 *
 * @package Components\elfinder
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
pines.loadjs("<?php e($_->config->location); ?>components/com_elfinder/includes/js/elfinder.min.js");
pines.loadcss("<?php e($_->config->location); ?>components/com_elfinder/includes/css/elfinder.min.css");
<?php if ($_->config->com_elfinder->theme) { ?>
pines.loadcss("<?php e($_->config->location); ?>components/com_elfinder/includes/css/theme.css");
<?php } ?>
pines(function(){
elFinder.prototype._options.cookie = {expires: <?php $params = session_get_cookie_params(); echo (int) $params['lifetime']; ?>, domain: '', path: <?php echo json_encode($_->config->rela_location); ?>, secure: false};
});
</script>
<style type="text/css">
.elfinder-dialog label {
display: inline;
}
<?php if (!$_->config->com_elfinder->theme) { ?>
.elfinder-cwd-wrapper .elfinder-cwd-file.ui-selected {
outline-width: 1px;
outline-style: dotted;
}
<?php } ?>
</style>