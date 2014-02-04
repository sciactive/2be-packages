<?php
/**
 * List Reviews, a type of testimonial.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_testimonials/listtestimonials') )
	punt_user(null, pines_url('com_testimonials', 'testimonial/list'));

switch ($_REQUEST['type']) {
	case 'denied':
	case 'pending':
	case 'approved':
		break;
	default: 
		$default = true;
}

if (!$default)
	$_->com_testimonials->list_reviews($_REQUEST['type']);
else
	$_->com_testimonials->list_reviews();