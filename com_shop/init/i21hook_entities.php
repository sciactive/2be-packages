<?php
/**
 * Hook Entity functions for shop control.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Add the current user's shop to new com_sales entities.
 *
 * This occurs right before an entity is saved. It only alters the entity if:
 * - There is a user logged in and has a shop.
 * - The entity is new (doesn't have a GUID.)
 * - The entity is not a user or group.
 *
 * If you want a new entity to have a different user and/or group than the
 * current user, you must first save it to the database, then change the
 * user/group, then save it again.
 *
 * Default access control is
 * - user = 3
 * - group = 3
 * - other = 0
 *
 * @param array &$array An array of either an entity or another array of entities.
 */
function com_shop__add_shop(&$array) {
	if (
			(object) $_SESSION['shop'] === $_SESSION['shop'] &&
			!isset($array[0]->guid) &&
			$array[0]->hasTag('com_sales') &&
			(
				$array[0]->hasTag('cashcount') ||
				$array[0]->hasTag('cashcount_audit') ||
				$array[0]->hasTag('category') ||
				$array[0]->hasTag('countsheet') ||
				$array[0]->hasTag('manufacturer') ||
				$array[0]->hasTag('payment_type') ||
				$array[0]->hasTag('po') ||
				$array[0]->hasTag('product') ||
				$array[0]->hasTag('return') ||
				$array[0]->hasTag('return_checklist') ||
				$array[0]->hasTag('sale') ||
				$array[0]->hasTag('shipment') ||
				$array[0]->hasTag('shipper') ||
				$array[0]->hasTag('special') ||
				$array[0]->hasTag('stock') ||
				$array[0]->hasTag('tax_fee') ||
				$array[0]->hasTag('transfer') ||
				$array[0]->hasTag('vendor')
			)
		) {
		$array[0]->shop = $_SESSION['shop'];
	}
}

$_->hook->add_callback('$_->nymph->saveEntity', -100, 'com_shop__add_shop');
