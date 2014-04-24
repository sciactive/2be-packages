<?php
/**
 * Load Bootstrap.
 *
 * @package Components\bootstrap
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

// Load the scripts on the currently in construction page.
if ($_->config->com_bootstrap->always_load)
	$_->com_bootstrap->load();

// Tell any editor to load the CSS in the edit view.
if ($_->editor) {
	$themes = json_decode(file_get_contents('components/com_bootstrap/includes/css/themes.json'), true);
	$theme = $themes[$_->config->com_bootstrap->theme];
	unset($themes);
	if ($theme['andnormal']) {
		$_->editor->add_css($_->config->location.'components/com_bootstrap/includes/css/'.($_->config->debug_mode ? 'bootstrap.css' : 'bootstrap.min.css'));
	}
	$_->editor->add_css($_->config->location.'components/com_bootstrap/includes/css/'.clean_filename($_->config->debug_mode ? $theme['file'] : $theme['minfile']));
	unset($theme);
}