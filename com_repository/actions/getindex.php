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

$publisher = $_REQUEST['pub'];

$user = user::factory($publisher);
if (!isset($user->guid))
	$user = null;

$_->page->ajax($_->com_repository->get_index($user, false));