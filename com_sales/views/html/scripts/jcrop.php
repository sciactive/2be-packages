<?php
/**
 * A view to load Jcrop.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
$_.loadcss("<?php e($_->config->location); ?>components/com_sales/includes/jcrop/css/<?php echo $_->config->debug_mode ? 'jquery.Jcrop.css' : 'jquery.Jcrop.min.css'; ?>");
$_.loadjs("<?php e($_->config->location); ?>components/com_sales/includes/jcrop/js/<?php echo $_->config->debug_mode ? 'jquery.Jcrop.js' : 'jquery.Jcrop.min.js'; ?>");
</script>