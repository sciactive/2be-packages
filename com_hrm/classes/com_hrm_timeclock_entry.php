<?php
/**
 * com_hrm_timeclock_entry class.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A timeclock entry. The employee should be the user of this entity.
 *
 * @package Components\hrm
 * @property int $in In time.
 * @property int $out Out time.
 * @property string $comment An optional comment.
 * @property array $extras Any extra information.
 */
class com_hrm_timeclock_entry extends entity {
	protected $tags = array('com_hrm', 'timeclock_entry');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->extras = array();
		$this->ac = (object) array('user' => 3, 'group' => 3, 'other' => 2);
	}

	public static function etype() {
		return 'com_hrm_timeclock_entry';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Timeclock Entry $this->guid";
			case 'type':
				return 'timeclock entry';
			case 'types':
				return 'timeclock entries';
			case 'icon':
				return 'picon-view-calendar-time-spent';
		}
		return null;
	}
}