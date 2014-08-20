<?php
/**
 * com_calendar_event class.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * An event for the company calendar.
 *
 * @package Components\calendar
 */
class com_calendar_event extends entity {
	protected $tags = array('com_calendar', 'event');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->private = false;
	}

	public static function etype() {
		return 'com_calendar_event';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->title;
			case 'type':
				return 'event';
			case 'types':
				return 'events';
			case 'url_list':
				if (gatekeeper('com_calendar/viewcalendar'))
					return pines_url('com_calendar', 'editcalendar');
				break;
			case 'icon':
				return 'picon-view-pim-calendar';
		}
		return null;
	}

	/**
	 * Delete the event.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted event $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the event.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->title))
			return false;
		if (!isset($this->event_id))
			$this->event_id = 0;
		return parent::save();
	}

	/**
	 * Print a form to edit the event.
	 * @param group $location The location to create the event for.
	 * @param string $timezone The timezone to edit the event in.
	 */
	public function print_form($location = null, $timezone = null) {
		global $_;

		if (empty($timezone)) {
			if (isset($this->user->guid))
				$timezone = $this->user->get_timezone();
			else
				$timezone = $_SESSION['user']->get_timezone();
		}

		$module = new module('com_calendar', 'form_event', 'content');
		$module->entity = $this;
		$module->timezone = $timezone;
		// Should work like this, we need to have the employee's group update upon saving it to a user.
		$module->employees = $_->com_hrm->get_employees();
		$event_location = $this->group->guid;
		if (empty($event_location))
			$event_location = $location->guid;
		$module->location = $event_location;
		$_->page->ajax($module->render(), 'text/html');
	}
}