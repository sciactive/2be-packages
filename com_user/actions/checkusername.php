<?php
/**
 * Determine if a username is available.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_user->check_username)
	throw new HttpClientException(null, 404);

if (!empty($_REQUEST['id']))
	$id = intval($_REQUEST['id']);

$_->page->ajax(json_encode($_->user_manager->check_username($_REQUEST['username'], $id)));