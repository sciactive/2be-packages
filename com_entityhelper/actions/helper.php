<?php
/**
 * Get the dialog contents.
 *
 * @package Components\entityhelper
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

// TODO: Provide a method to define a context. (So non-entities would still work.)

$entity = $_->nymph->getEntity(
		array('class' => $_REQUEST['context']),
		array('&',
			'guid' => (int) $_REQUEST['id']
		)
	);
if (!$entity->guid) {
	$_->page->ajax(json_encode(false));
	return;
}

if (is_callable(array($entity, 'helper'))) {
	$response = $entity->helper();
	if (is_a($response, 'module')) {
		$response->render = 'body';
		$response->entity = $entity;
		$body = $response->render();
		$result = array(
			'title' => empty($response->title) ? $entity->info('name') : $response->title,
			'body' => $body
		);
		$response = $entity->helper();
		$response->render = 'footer';
		$response->entity = $entity;
		$result['footer'] = $response->render();
		$_->page->ajax(json_encode($result));
		return;
	} elseif ((array) $response === $response && isset($response['title']) && isset($response['body']) && isset($response['footer'])) {
		$_->page->ajax(json_encode($response));
		return;
	}
}

$module = new module('com_entityhelper', 'default_helper');
$module->render = 'body';
$module->entity = $entity;
$result = array(
	'title' => $entity->info('name'),
	'body' => $module->render()
);
$module = new module('com_entityhelper', 'default_helper');
$module->render = 'footer';
$module->entity = $entity;
$result['footer'] = $module->render();
$_->page->ajax(json_encode($result));