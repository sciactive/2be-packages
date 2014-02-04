<?php
/**
 * Fake Joomla! module classes.
 *
 * @package Templates\joomlatemplates
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Fake module class.
 *
 * @package Templates\joomlatemplates
 */
class jmodule {
	public $title = '';
	public $content = '';
	public $showtitle = true;
	public $position = '';
	public $style = '';
}

/**
 * Fake module params class.
 *
 * @package Templates\joomlatemplates
 */
class jmodule_params {
	public $classes = '';
	public function get($name) {
		switch ($name) {
			case 'moduleclass_sfx':
				return " $this->classes";
				break;
			default:
				pines_log("Unknown jmodule parameter requested: $name", 'info');
				return null;
				break;
		}
	}
}