<?php
/**
 * Import entities from a file into the entity manager.
 *
 * @package Components\entitytools
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_entitytools/import') )
	punt_user(null, pines_url('com_entitytools', 'import'));

if (!is_callable(array($_->entity_manager, 'import'))) {
	pines_notice('The currently installed entity manager doesn\'t support importing.');
	return;
}

if (!empty($_FILES['entity_import']['tmp_name'])) {
	set_time_limit(28800);
	if ($_REQUEST['reset_entities'] == 'ON') {
		// First log the user out.
		$_->user_manager->logout();
		// Delete all current entities.
		$last_entity = $_->entity_manager->get_entity(array('reverse' => true, 'sort' => 'guid'));
		if (!$last_entity->guid) {
			pines_error('Couldn\'t find last entity.');
			return;
		}
		for ($i = 1; $i <= $last_entity->guid; $i++)
			$_->entity_manager->delete_entity_by_id($i);
	}
	if ($_->entity_manager->import($_FILES['entity_import']['tmp_name']))
		pines_notice('Import complete.');
	else
		pines_notice('Import failed.');
}

if ($_REQUEST['reset_entities'] == 'ON')
	$_->user_manager->punt_user('You have been logged out.');
else
	$module = new module('com_entitytools', 'import', 'content');