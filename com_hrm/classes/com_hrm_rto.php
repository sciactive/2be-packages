<?php
/**
 * com_hrm_rto class.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A request for time off from work.
 *
 * @package Components\hrm
 */
class com_hrm_rto extends Entity {
	const etype = 'com_hrm_rto';
	protected $tags = array('com_hrm', 'rto');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->status = 'pending';
		$this->all_day = true;
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "RTO $this->guid";
			case 'type':
				return 'RTO';
			case 'types':
				return 'RTOs';
			case 'icon':
				return 'picon-view-calendar-upcoming-events';
		}
		return null;
	}

	/**
	 * Check if the request is during a time when the employee is scheduled.
	 *
	 * @return bool True or false.
	 */
	public function conflicting() {
		global $_;

		if ($_->config->com_hrm->com_calendar) {
			$selector = array('&', 'ref' => array('employee', $this->employee), 'tag' => array('com_calendar', 'event'));
			$selector1 = array('&', 'gte' => array('start', $this->start), 'lte' => array('start', $this->end));
			$selector2 = array('&', 'gte' => array('end', $this->start), 'lte' => array('end', $this->end));
			$selector3 = array('&', 'gte' => array('end', $this->end), 'lte' => array('start', $this->start));

			$conflicts = array_merge(
				$_->nymph->getEntities(array('limit' => 1, 'class' => com_calendar_event), $selector, $selector1),
				$_->nymph->getEntities(array('limit' => 1, 'class' => com_calendar_event), $selector, $selector2),
				$_->nymph->getEntities(array('limit' => 1, 'class' => com_calendar_event), $selector, $selector3)
			);
		} else {
			$conflicts = array();
		}
		return (!empty($conflicts));
	}

	/**
	 * Delete the rto.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted RTO $this->guid.", 'notice');
		return true;
	}

	/**
	 * Save the rto.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->start))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the rto.
	 */
	public function print_form() {
		global $_;

		$module = new module('com_hrm', 'timeoff/request', 'content');
		$module->entity = $this;
		$module->requests = array();
		// Load all pending time off requests so they can be edited if needed.
		$module->requests = $_->nymph->getEntities(array('class' => com_hrm_rto), array('!&', 'data' => array('status', 'approved')), array('&', 'tag' => array('com_hrm', 'rto'), 'ref' => array('user', $_SESSION['user'])));
		$_->page->ajax($module->render(), 'text/html');
	}
}