<?php
/**
 * Print global meta tags.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_content->global_meta_tags)
	return;

$module = new module('com_content', 'global_meta_tags', 'head');
unset($module);