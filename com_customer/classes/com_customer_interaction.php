<?php
/**
 * com_customer_interaction class.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A customer interaction.
 *
 * @package Components\customer
 */
class com_customer_interaction extends entity {
	protected $tags = array('com_customer', 'interaction');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->status = 'open';
		$this->review_comments = array();
	}

	public static function etype() {
		return 'com_customer_interaction';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return 'Interaction '.$this->guid;
			case 'type':
				return 'interaction';
			case 'types':
				return 'interactions';
			case 'icon':
				return 'picon-meeting-participant-optional';
		}
		return null;
	}

	/**
	 * Delete the interaction.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted interaction $this->guid.", 'notice');
		return true;
	}
}