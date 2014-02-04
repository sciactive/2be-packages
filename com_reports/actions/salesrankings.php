<?php
/**
 * List the monthly sales rankings or show the current one.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_reports/listsalesrankings') ) {
	if ( !gatekeeper('com_reports/viewsalesranking') )
		punt_user(null, pines_url('com_reports', 'salesrankings'));
	$current_rankings = $_->entity_manager->get_entities(array('class' => com_reports_sales_ranking), array('&', 'tag' => array('com_reports', 'sales_ranking')));
	$current_rankings = end($current_rankings);
	if (isset($current_rankings->guid)) {
		$current_rankings->rank();
	} else {
		pines_notice('No rankings are accessible.');
	}
} else {
	$_->com_reports->list_sales_rankings();
}