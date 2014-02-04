<?php
/**
 * List replacements.
 *
 * @package Components\replace
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_replace/listreplacements') )
	punt_user(null, pines_url('com_replace', 'replacement/list'));

$_->com_replace->list_replacements();