<?php
/**
 * com_reports_paystub class.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A list of employee payroll for a given pay period.
 *
 * @package Components\reports
 */
class com_reports_paystub extends entity {
	protected $tags = array('com_reports', 'paystub');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->payroll = array();
	}

	public static function etype() {
		return 'com_reports_paystub';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Paystub $this->guid";
			case 'type':
				return 'paystub';
			case 'types':
				return 'paystubs';
			case 'url_view':
				if (gatekeeper('com_reports/reportpayroll'))
					return pines_url('com_reports', 'reportpayroll', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_reports/reportpayroll'))
					return pines_url('com_reports', 'listpaystubs');
				break;
			case 'icon':
				return 'picon-view-time-schedule-calculus';
		}
		return null;
	}

	/**
	 * Delete the sales ranking.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted paystub [$this->name].", 'notice');
		return true;
	}

	/**
	 * Print a form to edit the sales ranking.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;

		$module = new module('com_reports', 'form_paystub', 'content');
		$module->entity = $this;
		$module->employees = $_->com_hrm->get_employees();

		return $module;
	}

	/**
	 * Creates and attaches a module which reports payroll.
	 *
	 * @param bool $entire_company Whether or not to show the entire company
	 * @param group $location The group to report on.
	 * @param bool $descendants Whether to show descendant locations.
	 * @return module The paystub report module.
	 */
	function show($entire_company = true, $location = null, $descendants = false) {
		global $_;

		$module = new module('com_reports', 'report_paystub', 'content');
		$module->entity = $this;
		$module->entire_company = $entire_company;
		$module->location = $location;
		$module->descendants = $descendants;

		return $module;
	}

	/**
	 * Sort by the total payment amount.
	 *
	 * @param array $a The first entry.
	 * @param array $b The second entry.
	 * @return int The sort order.
	 * @access private
	 */
	private function sort_payroll($a, $b) {
		if ($a['total'] > $b['total'])
			return -1;
		if ($a['total'] < $b['total'])
			return 1;
		return 0;
	}
}