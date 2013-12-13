<?php
/**
 * com_newsletter's configuration defaults.
 *
 * @package Components\newsletter
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'default_from',
		'cname' => 'Default From',
		'description' => 'The default "from" email.',
		'value' => 'Nowhere <nowhere@example.com>',
		'peruser' => true,
	),
	array(
		'name' => 'default_reply_to',
		'cname' => 'Default Reply To',
		'description' => 'The "reply-to" email.',
		'value' => 'webmaster@example.com',
		'peruser' => true,
	),
);
