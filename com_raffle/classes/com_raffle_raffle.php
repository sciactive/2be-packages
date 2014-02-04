<?php
/**
 * com_raffle_raffle class.
 *
 * @package Components\raffle
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A raffle.
 *
 * @package Components\raffle
 */
class com_raffle_raffle extends entity {
	protected $tags = array('com_raffle', 'raffle');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->public = true;
		$this->contestants = array();
		$this->public_contestants = array();
		$this->winners = array();
		$this->places = 1;
	}

	public static function etype() {
		return 'com_raffle_raffle';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'raffle';
			case 'types':
				return 'raffles';
			case 'url_view':
				if (gatekeeper('com_raffle/completeraffle') && $this->complete)
					return pines_url('com_raffle', 'raffle/complete', array('id' => $this->guid));
				elseif (!$this->complete && $this->public)
					return pines_url('com_raffle', 'enter', array('id' => $this->guid));
				break;
			case 'url_edit':
				if (gatekeeper('com_raffle/editraffle'))
					return pines_url('com_raffle', 'raffle/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_raffle/listraffles'))
					return pines_url('com_raffle', 'raffle/list');
				break;
			case 'icon':
				return 'picon-games-achievements';
		}
		return null;
	}

	/**
	 * Complete the raffle by selecting winners.
	 * @return bool True on success, false on failure.
	 */
	public function complete() {
		if ($this->complete)
			return true;
		if ($this->public)
			$contestants = array_merge($this->contestants, $this->public_contestants);
		else
			$contestants = $this->contestants;
		$this->winners = array();
		for ($i = 1; $i <= $this->places; $i++) {
			if (!$contestants)
				break;
			$cur_winner = array_rand($contestants);
			$this->winners[$i] = $contestants[$cur_winner];
			unset($contestants[$cur_winner]);
		}
		$this->complete = true;
		return $this->save();
	}

	/**
	 * Delete the raffle.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted raffle $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the raffle.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		global $_;
		if (!isset($this->id))
			$this->id = $_->entity_manager->new_uid('com_raffle_raffle');
		return parent::save();
	}

	/**
	 * Print a complete raffle.
	 * @return module The module.
	 */
	public function print_complete() {
		if (!$this->complete)
			return null;
		global $_;
		$module = new module('com_raffle', 'raffle/complete', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a form to edit the raffle.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_raffle', 'raffle/form', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a public raffle form.
	 * @return module The module.
	 */
	public function print_public() {
		if (!$this->public || $this->complete)
			return null;
		global $_;
		$module = new module('com_raffle', 'enter', 'content');
		$module->entity = $this;

		return $module;
	}
}