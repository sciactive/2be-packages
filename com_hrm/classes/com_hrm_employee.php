<?php
/**
 * com_hrm_employee class.
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
 * An employee.
 *
 * @package Components\hrm
 */
class com_hrm_employee extends user {
	/**
	 * Load an employee.
	 * @param int $id The ID of the employee to load, 0 for a new employee.
	 */
	public function __construct($id = 0) {
		// Defaults.
		$this->employee = true;
		$this->employee_attributes = array();
		$this->commissions = array();
		$this->hire_date = time();
		$this->employment_history = array();
		$this->pay_type = 'hourly';
		$this->timeclock = com_hrm_timeclock::factory();
		$this->timeclock->user = $this;
		$this->timeclock->group = $this->group;
		parent::__construct($id);
	}

	/**
	 * Create a new instance.
	 *
	 * @param int|string $id The ID or username of the employee to load, 0 for a new employee.
	 * @return com_hrm_employee The new instance.
	 */
	public static function factory($id = 0) {
		global $_;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args[0]);
		$_->hook->hook_object($entity, $class.'->', false);
		if (is_array($entity->timeclock) || empty($entity->timeclock)) {
			// Convert old style timeclocks to new ones.
			// If we can't save the entity, don't bother.
			if (!$entity->save())
				return $entity;
			$old_tc = (array) $entity->timeclock;
			$entity->timeclock = com_hrm_timeclock::factory();
			$entity->timeclock->user = $entity;
			$entity->timeclock->group = $entity->group;
			foreach ($old_tc as $cur_entry) {
				if ($cur_entry['status'] == 'in') {
					$time_in = $cur_entry['time'];
				} elseif ($cur_entry['status'] == 'out' && isset($time_in)) {
					$entity->timeclock->add($time_in, $cur_entry['time']);
					unset($time_in);
				}
			}
			if (isset($time_in))
				$entity->timeclock->time_in = $time_in;
			// Save the new timeclock.
			if (!$entity->timeclock->save() || !$entity->save())
				$entity->timeclock = $old_tc;
		}
		if (is_object($entity->timeclock)) {
			if ($entity->timeclock->ac != (object) array('user' => 3, 'group' => 3, 'other' => 2)) {
				$entity->timeclock->ac = (object) array('user' => 3, 'group' => 3, 'other' => 2);
				$entity->timeclock->save();
			}
			if (!$entity->is($entity->timeclock->user)) {
				$entity->timeclock->user = $entity;
				$entity->timeclock->group = $entity->group;
				$entity->timeclock->save();
			}
		}
		return $entity;
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'employee';
			case 'types':
				return 'employees';
			case 'url_edit':
				if (gatekeeper('com_hrm/editemployee'))
					return pines_url('com_hrm', 'employee/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_hrm/listemployees'))
					return pines_url('com_hrm', 'employee/list');
				break;
			case 'icon':
				return 'picon-meeting-participant';
			default:
				return parent::info($type);
		}
		return null;
	}

	/**
	 * Delete the employee.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted employee $this->name.", 'notice');
		return true;
	}

	/**
	 * Print a form to edit the employee.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_hrm', 'employee/form', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * View the employment history for this employee.
	 * @return module The form's module.
	 */
	public function print_history() {
		global $_;
		$module = new module('com_hrm', 'employee/history', 'content');
		$module->entity = $this;
		$module->issues = $_->nymph->getEntities(array('class' => com_hrm_issue, 'skip_ac' => true), array('&', 'tag' => array('com_hrm', 'issue'), 'ref' => array('employee', $this)));
		$module->sales = $_->nymph->getEntities(array('class' => com_sales_sale, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'sale'), 'ref' => array('user', $this)));
		$module->returns = $_->nymph->getEntities(array('class' => com_sales_return, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'return'), 'ref' => array('user', $this)));
		if ($_->config->com_hrm->com_reports)
			$module->paystubs = $_->nymph->getEntities(array('class' => com_reports_paystub, 'skip_ac' => true), array('&', 'tag' => array('com_reports', 'paystub')));

		return $module;
	}
}