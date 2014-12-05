<?php
/**
 * com_sales_cashcount class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A cash count.
 *
 * @package Components\sales
 */
class com_sales_cashcount extends Entity {
	const etype = 'com_sales_cashcount';
	protected $tags = array('com_sales', 'cashcount');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		global $_;
		$this->status = 'pending';
		$this->audits = $this->deposits = $this->skims = $this->count = $this->count_out = array();
		$this->currency_symbol = $_->config->com_sales->currency_symbol;
		// Create a currency array.
		foreach ($_->config->com_sales->currency_denominations as $cur_currency) {
			$key = str_replace('.', '_', $cur_currency);
			$this->currency[$key] = $cur_currency;
		}
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Cash Count $this->guid";
			case 'type':
				return 'cash count';
			case 'types':
				return 'cash counts';
			case 'url_edit':
				if (gatekeeper('com_sales/editcashcount'))
					return pines_url('com_sales', 'cashcount/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_sales/listcashcounts'))
					return pines_url('com_sales', 'cashcount/list');
				break;
			case 'icon':
				return 'picon-office-chart-line';
		}
		return null;
	}

	/**
	 * Print a form to cash out a cashcount.
	 * @return module The form's module.
	 */
	public function cash_out() {
		global $_;
		$module = new module('com_sales', 'cashcount/formcashout', 'content');
		$module->entity = $this;
		return $module;
	}

	/**
	 * Delete the cash count.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		// Delete all audits and skims for this cash count as well.
		foreach ($this->audits as &$cur_audit)
			$cur_audit->delete();
		foreach ($this->skims as &$cur_skim)
			$cur_skim->delete();
		foreach ($this->deposits as &$cur_deposit)
			$cur_deposit->delete();

		if (!parent::delete())
			return false;
		pines_log("Deleted Cash Count {$this->guid}.", 'notice');
		return true;
	}

	/**
	 * Print a form to edit the cash count.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_sales', 'cashcount/form', 'content');
		$module->entity = $this;
		return $module;
	}

	/**
	 * Print a form to review the cash count.
	 * @return module The form's module.
	 */
	public function print_review() {
		global $_;
		$this->update_total();
		$module = new module('com_sales', 'cashcount/formreview', 'content');
		$module->entity = $this;
		return $module;
	}

	/**
	 * Save the Cash Count.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		$this->update_total();
		return parent::save();
	}

	/**
	 * Update the Cash Count total to include new sales, skims, and deposits.
	 * @return bool True on success, false on failure.
	 */
	public function update_total() {
		global $_;
		$this->total = $this->float;
		// Update the total in the drawer for each skim, deposit or sale made.
		if (isset($this->guid)) {
			$new_txs = (array) $_->nymph->getEntities(
					array('class' => com_sales_tx),
					array('&',
						'tag' => array('com_sales', 'transaction', 'payment_tx'),
						'gte' => array('cdate', (int) $this->cdate),
						'ref' => array('group', $this->group)
					)
				);
			// Look for all transactions that resulted in cash being tendered.
			foreach ($new_txs as $cur_tx) {
				if (!$cur_tx->ref->change_type)
					continue;
				if ($cur_tx->type == 'payment_received') {
					$this->total += $cur_tx->amount;
				} elseif ($cur_tx->type == 'change_given' || $cur_tx->type == 'payment_voided' || $cur_tx->type == 'payment_returned') {
					$this->total -= $cur_tx->amount;
				}
			}
			// Subtract all the skims.
			foreach ($this->skims as $cur_skim) {
				$this->total -= $cur_skim->total;
			}
			// And add all the deposits.
			foreach ($this->deposits as $cur_deposit) {
				$this->total += $cur_deposit->total;
			}
		}
	}
}