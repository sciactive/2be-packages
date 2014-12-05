<?php
/**
 * com_testimonials' information.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Testimonials',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Users can provide feedback displayed as testimonials.',
	'description' => 'Takes user feedback and allows approval of that feedback to be displayed as testimonials.',
	'depend' => array(
		'core' => '<3',
		'service' => 'user_manager',
		'component' => 'com_customer&com_jquery&com_bootstrap&com_pgrid&com_pform'
	),
	'abilities' => array(
		array('newtestimonial', 'Create a Testimonial', 'User can create a testimonial (Give to Customers).'),
		array('newtestimonials', 'Create Backend Testimonials', 'User can create new testimonials. (Give to Employees)'),
		array('listtestimonials', 'List Testimonials', 'User can see testimonials.'),
		array('listalltestimonials', 'List All Testimonials', 'User can see testimonials in grids by typing *.'),
		array('edittestimonials', 'Edit Testimonials', 'User can edit current testimonials.'),
		array('toggletestimonials', 'Toggle Testimonials', 'User can view pending/approved/denied testimonials and reviews.'),
		array('viewanonauthor', 'View Anonymous Authors', 'User can view authors on anonymous testimonials.'),
		array('showentityhelp', 'Show Entity Helpers', 'User can view entity references on authored testimonials/reviews.'),
		array('edittags', 'Edit Testimonial Tags', 'User can add/remove tags on testimonials/reviews.'),
		array('quotetestimonials', 'Quote Testimonial', 'User can define a quote from the testimonial.'),
		array('changestatus', 'Change Testimonial Status', 'User can approve or disapprove testimonials.'),
		array('deletetestimonials', 'Delete Testimonials', 'User can delete testimonials.'),
		array('search', 'Search Testimonials', 'User can search testimonials.'),
		array('help', 'Help With Testimonials', 'User can use the help section to learn how to create testimonial modules.')
	),
);
