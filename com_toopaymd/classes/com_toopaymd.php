<?php
/**
 * com_toopaymd class.
 *
 * @package Components\toopaymd
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_toopaymd main class.
 *
 * A standard editor using Toopay Bootstrap Markdown Editor.
 *
 * @package Components\toopaymd
 */
class com_toopaymd extends component implements editor_interface {
	/**
	 * Whether the Toopay Bootstrap Markdown Editor JavaScript has been loaded.
	 * @access private
	 * @var bool $js_loaded
	 */
	private $js_loaded = false;
	/**
	 * The CSS files to load.
	 * @access private
	 * @var array $css_files
	 */
	private $css_files = array();

	public function add_css($url) {
		$this->css_files[] = clean_filename($url);
	}

	/**
	 * Get the CSS file array.
	 * @return array The CSS file array.
	 */
	public function get_css() {
		return $this->css_files;
	}

	public function load() {
		if (!$this->js_loaded) {
			$module = new module('com_toopaymd', 'toopaymd', 'head');
			$module->render();
			$this->js_loaded = true;
		}
	}

	/**
	 * Parse the user input into its code equivalent.
	 *
	 * @param string $input The origin user input.
	 * @return string The converted code.
	 */
	public function parse_input($input) {
		include 'components/com_toopaymd/includes/php-markdown-lib-1.4.1/Michelf/MarkdownExtra.inc.php';
		return \Michelf\MarkdownExtra::defaultTransform($input);
	}
}