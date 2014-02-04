<?php
/**
 * Determine if an email is available.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_user->check_phone)
	throw new HttpClientException(null, 404);

$_->page->override = true;
header('Content-Type: application/json');

if (!empty($_REQUEST['id']))
	$id = intval($_REQUEST['id']);

$_->page->override_doc(json_encode($_->user_manager->check_phone($_REQUEST['phone'], $id)));