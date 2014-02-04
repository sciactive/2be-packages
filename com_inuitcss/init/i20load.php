<?php
/**
 * Load Inuit.
 *
 * @package Components\inuitcss
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

// Load the CSS on the currently in construction page.
if ($_->config->com_inuitcss->always_load)
	$_->com_inuitcss->load();

// Tell any editor to load the CSS in the edit view.
if ($_->editor) {
	$_->editor->add_css($_->config->location.'components/com_inuitcss/includes/core/css/inuit.css');
	$_->editor->add_css($_->config->location.'components/com_inuitcss/includes/'.clean_filename($_->config->com_inuitcss->grid_layout));
}