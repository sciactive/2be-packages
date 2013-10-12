<?php
/**
 * Save a testimonial.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ($_REQUEST['type'] == 'module') {
	// if we need to use and return json
	$pines->page->override = true;
	header('Content-Type: application/json');
	
	if ( isset($_REQUEST['id']) ) {
		if ( gatekeeper('com_testimonials/edittestimonials') ) {
			$testimonial = com_testimonials_testimonial::factory((int) $_REQUEST['id']);
		}
		
		if (!isset($testimonial->guid)) {
			pines_log("Requested testimonial id {$_REQUEST['id']} is not accessible.", 'error');
			$result = array('result' => false, 'message' => 'Lack of Permissions or Unavailable Testimonial ID.');
		}
	} else {
		if ( gatekeeper('com_testimonials/newtestimonial') )
			$testimonial = com_testimonials_testimonial::factory();
		else {
			$result = array('result' => false, 'message' => 'Lack of Permissions or Unavailable Testimonial ID.');
			$pines->page->override_doc(json_encode($result));
			return;
		}
	}
} else {
	// created from the grid form
	if ( isset($_REQUEST['id']) ) {
		if ( !gatekeeper('com_testimonials/edittestimonials') )
			punt_user(null, pines_url('com_testimonials', 'testimonial/list'));
		$testimonial = com_testimonials_testimonial::factory((int) $_REQUEST['id']);
		if (!isset($testimonial->guid)) {
			pines_error('Requested testimonial id is not accessible.');
			return;
		}
	} else {
		if ( !gatekeeper('com_testimonials/newtestimonial') )
			punt_user(null, pines_url('com_testimonials', 'testimonial/list'));
		$testimonial = com_testimonials_testimonial::factory();
	}
}

// There's always a customer associated with a review - in order to review,
// for now, anyway, you have to be a customer

// Get customer.
$testimonial->customer = com_customer_customer::factory((int) $_REQUEST['customer']);
if (!isset($testimonial->customer->guid))
	$testimonial->customer = null;

// Check for mandatory fields
if ($testimonial->customer == null) {
	if ($_REQUEST['type'] == 'module')
		$result = array('result' => false, 'message' => 'Missing Associated Customer');
	else {
		pines_error('Missing Associated Customer');
		$testimonial->print_form();
		return;
	}
}
// This is the review, this is always required: testimonial or review.
if (empty($testimonial->feedback)) {
	if ($_REQUEST['type'] == 'module')
		$result = array('result' => false, 'message' => 'Missing Testimonial');
	else {
		pines_error('Missing Testimonial');
		$testimonial->print_form();
		return;
	}
}

// Get user input variables.
$testimonial->feedback = $_REQUEST['feedback'];
$testimonial->quotefeedback = $_REQUEST['quotefeedback'];

// Only save a rating if it matches the accepted values.
if (in_array((int) $_REQUEST['rating'], array(1,2,3,4,5)))
	$testimonial->rating = (int) $_REQUEST['rating'];

$testimonial->share = ($_REQUEST['share'] == 'ON');
$testimonial->anon = ($_REQUEST['anon'] == 'ON'); 
$testimonial->type = $_REQUEST['type']; // this type refers to form or module and should not be confused with review or testimonial
$testimonial->author = $testimonial->create_author();

// Begin the testimonial with a pending tag
if (!isset($testimonial->guid)) {
	$testimonial->add_tag('pending');
	$testimonial->date = strtotime('now');
}

// If rated, let's also give it a tag
if (isset($testimonial->rating)) {
	$testimonial->add_tag('rated');
}


// Here's where we can add some of the review specific code
// These variables are usedd for both submission and retrieval
// I need to save the review with the entities and tags necessary to distinguish them
// And I need them to find them again. The values come from hidden inputs either
// hardcoded into the sales component on products, or from hidden inputs that are
// manually inserted on content pages you want reviews to show up on.
if ($_REQUEST['review_option_type'] == 'review') {
	$testimonial->add_tag('review');
	
	$review_entity_class = isset($_REQUEST['review_option_entity']) ? 'review_'.$_REQUEST['review_option_entity'] : false;
	$review_entity_guid = isset($_REQUEST['review_option_entity_id']) ? $_REQUEST['review_option_entity_id'] : false;
	$list_of_tags = isset($_REQUEST['review_option_additional_tags']) ? $_REQUEST['review_option_additional_tags'] : false;
	if (!empty($list_of_tags)) {
		$review_option_additional_tags = explode(" ", $list_of_tags);
	}
	$review_option_name = (isset($_REQUEST['review_option_name'])) ? preg_replace('/ /', '-', $_REQUEST['review_option_name']) : false; // replaces spaces with -
	
	// so now, add tags where needed
	if ($review_entity_class) {
		$testimonial->add_tag($review_entity_class);
	}
	if ($review_entity_guid) {
		$testimonial->add_tag($review_entity_guid);
	}
	if (!empty($review_option_additional_tags)) {
		foreach($review_option_additional_tags as $cur_tag) {
			$cur_tag = preg_replace('/ /', '-', $cur_tag);
			if (!in_array($cur_tag, array('pending', 'denied', 'approved', 'share', 'review'))) // tags we don't want to add, but were probably used for retrieval
				$testimonial->add_tag($cur_tag);
		}
	}
	// Need to be careful not to add reviews for different products/entities under the same name
	if ($review_option_name) {
		$testimonial->add_tag($review_option_name);
	}
	
	// Reviews get auto approved, but they CAN be denied later.
	if (!isset($testimonial->guid)) {
		$testimonial->add_tag('approved');
		$testimonial->status = true;
		$testimonial->remove_tag('pending');
	}
	// Changing status happens elsewhere on edits.
}

// Auto deny non-shared ones
if (!$testimonial->share) {
	$testimonial->add_tag('denied');
	$testimonial->status = false;
	$testimonial->remove_tag('pending', 'approved', 'share');
}

// Save and output appropriately
if ($testimonial->type == "form") {
	if ($testimonial->save()) {
		pines_notice('Saved testimonial '.$testimonial->id.' for customer '.$testimonial->customer->name.'.');
		pines_log("Saved testimonial $testimonial->id for customer {$testimonial->customer->name}.", 'notice');
	} else
		pines_error('Error saving testimonial. Do you have permission?');
	pines_redirect(pines_url('com_testimonials', 'testimonial/list'));
} else if ($testimonial->type == "module") {
	// JSON
	if (!$testimonial->save()) {
		$result = array('result' => false, 'message' => 'Failed to save Testimonial.');
	} else {
		$result = true;
	}
	
	$pines->page->override_doc(json_encode($result));
}


?>