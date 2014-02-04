<?php
/**
 * Print multiple payroll reports, one per page.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Kirk Johnson <kirk@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->show_title = false;
?>
<style type="text/css">
	#p_muid_pages .page {
		page-break-after: always;
	}
</style>
<div id="p_muid_pages">
	<?php foreach ($this->pages as $cur_page) { ?>
	<div class="page">
	<?php echo $cur_page; ?>
	</div>
	<?php } ?>
</div>