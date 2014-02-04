<?php
/**
 * Includes for the sales report calendar.
 *
 *
 * Built upon:
 *
 * FullCalendar Created by Adam Shaw
 * http://arshaw.com/fullcalendar/
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	pines.loadcss("<?php e($pines->config->location); ?>components/com_reports/includes/fullcalendar.css");
	pines.loadjs("<?php e($pines->config->location); ?>components/com_reports/includes/fullcalendar.min.js");
</script>
