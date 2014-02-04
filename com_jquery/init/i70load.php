<?php
/**
 * Load jQuery.
 *
 * @package Components\jquery
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

$module = new module('com_jquery', 'jquery', 'head');
unset ($module);
$module = new module('com_jquery', 'jquery-ui', 'head');
unset ($module);