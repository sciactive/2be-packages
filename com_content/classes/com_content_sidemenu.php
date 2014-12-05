<?php
/**
 * com_content_sidemenu class.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Grey Vugrin <greyvugrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A sidemenu.
 *
 * @package Components\content
 */
class com_content_sidemenu extends Entity {
	const etype = 'com_content_sidemenu';
	protected $tags = array('com_content', 'sidemenu');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
	}

	/**
	 * Save the sidemenu.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (empty($this->tags))
			return false;
		return parent::save();
	}
}