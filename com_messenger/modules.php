<?php
/**
 * com_messenger's modules.
 *
 * @package Components\messenger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'messenger' => array(
		'cname' => 'Instant Messenger',
		'description' => 'Chat with other users on the system.',
		'view' => 'modules/messenger',
		'form' => 'modules/messenger_form',
		'type' => 'module imodule widget',
		'widget' => array(
			'default' => false,
		),
	),
);