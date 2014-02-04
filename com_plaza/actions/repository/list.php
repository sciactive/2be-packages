<?php
/**
 * List repositories.
 *
 * @package Components\plaza
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_plaza/editrepositories') )
	punt_user(null, pines_url('com_plaza', 'repository/list'));

$_->com_plaza->list_repositories();