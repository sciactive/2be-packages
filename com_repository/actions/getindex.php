<?php
/**
 * Get an index.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$_->page->override = true;
header('Content-Type: application/json');

$publisher = $_REQUEST['pub'];

$user = user::factory($publisher);
if (!isset($user->guid))
	$user = null;

$_->page->override_doc($_->com_repository->get_index($user, false));