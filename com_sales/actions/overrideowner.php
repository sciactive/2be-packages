<?php
/**
 * Override a user/location for a sale/return.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/overrideowner') )
	punt_user(null, pines_url('com_sales', 'sale/overrideowner'));

$_->page->override = true;
header('Content-Type: application/json');

$entity = com_sales_sale::factory((int) $_REQUEST['id']);
if (!isset($entity->guid))
	$entity = com_sales_return::factory((int) $_REQUEST['id']);

if (!isset($entity->guid)) {
	$_->page->override_doc('false');
	return;
}

$location = group::factory(intval($_REQUEST['location']));
if (!isset($location->guid)) {
	$_->page->override_doc('false');
	return;
}

$user = user::factory(intval($_REQUEST['user']));
if (!isset($user->guid)) {
	$_->page->override_doc('false');
	return;
}

$entity->group = $location;
$entity->user = $user;

// Change the entity's transactions too.
$transactions = $_->entity_manager->get_entities(
		array('class' => com_sales_tx),
		array('&', 'tag' => array('com_sales', 'transaction')),
		array('|',
			'ref' => array(
				array('ticket', $entity),
				array('ref', $entity)
			)
		)
	);
foreach ($transactions as $cur_tx) {
	$cur_tx->group = $location;
	$cur_tx->user = $user;
	$cur_tx->save();
}

if ($entity->save()) {
	pines_notice("[{$entity->guid}] has been overridden.");
	$_->page->override_doc('true');
} else {
	pines_notice('The entity could not be overridden.');
	$_->page->override_doc('false');
}