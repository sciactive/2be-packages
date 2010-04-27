<?php
/**
 * A view to load Pines Grid's default icon set.
 *
 * @package Pines
 * @subpackage com_pgrid
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	// <![CDATA[
	pines.loadcss("<?php echo $pines->config->rela_location; ?>components/com_pgrid/includes/jquery.pgrid.default.icons.css");
	// ]]>
</script>