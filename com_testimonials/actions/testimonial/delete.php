<?php
/**
 * Delete a set of testimonials.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_testimonials/deletetestimonials') )
	punt_user(null, pines_url('com_testimonials', 'testimonial/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_testimonial) {
	$cur_entity = com_testimonials_testimonial::factory((int) $cur_testimonial);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_testimonial;
}
if (empty($failed_deletes)) {
	pines_notice('Selected testimonial(s) deleted successfully.');
} else {
	pines_error('Could not delete testimonials with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_testimonials', 'testimonial/list'));