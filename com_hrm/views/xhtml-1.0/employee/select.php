<?php
/**
 * A view to load the employee selector.
 *
 * @package Pines
 * @subpackage com_hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	// <![CDATA[
	pines.loadjs("<?php echo htmlspecialchars($pines->config->location); ?>components/com_hrm/includes/jquery.employeeselect.js");
	pines.com_hrm_autoemployee_url = "<?php echo addslashes(pines_url('com_hrm', 'employee/search')); ?>";
	// ]]>
</script>