<?php
/**
 * com_oxygenicons class.
 *
 * @package Components\oxygenicons
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_oxygenicons main class.
 *
 * A WonderPHP Icon theme using the Oxygen icon library.
 *
 * @package Components\oxygenicons
 */
class com_oxygenicons extends component implements icons_interface {
	/**
	 * Whether the Oxygen CSS has been loaded.
	 * @access private
	 * @var bool $css_loaded
	 */
	private $css_loaded = false;

	public function load() {
		if (!$this->css_loaded) {
			$module = new module('com_oxygenicons', 'oxygenicons', 'head');
			$module->render();
			$this->css_loaded = true;
		}
	}
}

?>