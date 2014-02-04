<?php
/**
 * A view to load jQueryUI Touch.
 *
 * @package Components\jqueryuitouch
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript" src="<?php e($_->config->location); ?>components/com_jqueryuitouch/includes/<?php echo $_->config->debug_mode ? 'jquery.ui.touch-punch.js' : 'jquery.ui.touch-punch.min.js'; ?>"></script>