<?php
/**
 * List foobars.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_example/listfoobars') )
	punt_user(null, pines_url('com_example', 'foobar/list'));

$_->com_example->list_foobars();