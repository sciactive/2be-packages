<?php
/**
 * Display "about" information for the current installation.
 *
 * @package Components\about
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_about/show') )
	punt_user(null, pines_url('com_about'));

$mod1 = new module('com_about', 'about1', 'content');
if ( $_->config->com_about->describe_system )
	$mod2 = new module('com_about', 'about2', 'content');

