<?php
/**
 * com_customer_customer class.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A customer.
 *
 * @package Components\customer
 */
class com_customer_customer extends user {
	/**
	 * Load a customer.
	 * @param int|string $id The ID or username of the customer to load, 0 for a new customer.
	 */
	public function __construct($id = 0) {
		parent::__construct();
		$this->add_tag('com_user', 'user', 'com_customer', 'customer');
		$this->remove_tag('enabled');
		if ($id > 0 || (string) $id === $id) {
			global $_;
			if ((int) $id === $id)
				$entity = $_->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'guid' => $id, 'tag' => array('com_user', 'user', 'com_customer', 'customer')));
			else
				$entity = $_->entity_manager->get_entity(array('class' => get_class($this)), array('&', 'tag' => array('com_user', 'user', 'com_customer', 'customer'), 'data' => array('username', $id)));
			if (isset($entity)) {
				$this->guid = $entity->guid;
				$this->tags = $entity->tags;
				$this->put_data($entity->get_data(), $entity->get_sdata());
				if (isset($this->secret))
					$this->verify_email = $this->email;
				return;
			}
		}
		// Defaults.
		$this->abilities = array();
		$this->groups = array();
		$this->inherit_abilities = true;
		$this->points = 0;
		$this->peak_points = 0;
		$this->total_points = 0;
		$this->address_type = 'us';
		$this->addresses = array();
		$this->attributes = array();
		// Load default groups.
		global $_;
		$group = $_->entity_manager->get_entity(
				array('class' => group, 'skip_ac' => true),
				array('&',
					'tag' => array('com_user', 'group'),
					'data' => array('default_customer_primary', true)
				)
			);
		if (isset($group->guid))
			$this->group = $group;
		if ($_->config->com_customer->follow_um_rules && $_->config->com_user->verify_email) {
			if ($_->config->com_user->unverified_access) {
				// Use unverified user groups.
				$groups = $_->entity_manager->get_entities(
						array('class' => group, 'skip_ac' => true),
						array('&',
							'tag' => array('com_user', 'group'),
							'data' => array('unverified_secondary', true)
						)
					);
				$this->com_customer__unverified = true;
				$this->com_customer__unverified_groups = true;
				if ($groups)
					$this->groups = $groups;
				$this->add_tag('enabled');
				return;
			}
			$this->com_customer__unverified = true;
			$this->remove_tag('enabled');
		} else
			$this->add_tag('enabled');
		$groups = $_->entity_manager->get_entities(
				array('class' => group, 'skip_ac' => true),
				array('&',
					'tag' => array('com_user', 'group'),
					'data' => array('default_customer_secondary', true)
				)
			);
		if ($groups)
			$this->groups = $groups;
	}

	/**
	 * Return the entity helper module.
	 * @return module Entity helper module.
	 */
	public function helper() {
		return new module('com_customer', 'customer/helper');
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'customer';
			case 'types':
				return 'customers';
			case 'url_edit':
				if (gatekeeper('com_customer/editcustomer'))
					return pines_url('com_customer', 'customer/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_customer/listcustomers'))
					return pines_url('com_customer', 'customer/list');
				break;
			case 'icon':
				return 'picon-x-office-contact';
			default:
				return parent::info($type);
		}
		return null;
	}

	/**
	 * Add days to the customer's profile.
	 *
	 * If the customer's membership expires in the future, $day_adjust will be
	 * added to that date. If not, $day_adjust will be added to today's date.
	 * 
	 * @param int $day_adjust The positive or negative number of days to add.
	 */
	public function adjust_membership($day_adjust) {
		$day_adjust = (int) $day_adjust;
		$date_string = ($day_adjust < 0 ? '' : '+')."$day_adjust days 00:00";
		if (time() < $this->member_exp) {
			$this->member_exp = strtotime($date_string, $this->member_exp);
		} else {
			$this->member_exp = strtotime($date_string);
		}
	}

	/**
	 * Add to or subtract from the customer's points.
	 *
	 * @param int $point_adjust The positive or negative point value to add.
	 */
	public function adjust_points($point_adjust) {
		global $_;
		$point_adjust = (int) $point_adjust;
		// Check that there is a point value.
		if (!is_int($this->points))
			$this->points = 0;
		// Check the total value.
		if (!is_int($this->total_points))
			$this->total_points = $this->points;
		// Check the peak value.
		if (!is_int($this->peak_points))
			$this->peak_points = $this->points;
		// Do the adjustment.
		if ($point_adjust != 0) {
			if ($point_adjust > 0)
				$this->total_points += $point_adjust;
			$this->points += $point_adjust;
			if ($this->points > $this->peak_points)
				$this->peak_points = $this->points;
		}
		// Did their points go negative?
		if ($this->points < 0 && !$_->config->com_customer->negpoints)
			$this->points = 0;
	}

	/**
	 * Delete the customer.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted customer $this->name.", 'notice');
		return true;
	}

	/**
	 * Print a report of the customer's history.
	 * @return module The report's module.
	 */
	public function print_history() {
		global $_;
		$module = new module('com_customer', 'customer/history', 'content');
		$module->entity = $this;
		$module->com_sales = $_->depend->check('component', 'com_sales');

		$module->interactions = $_->entity_manager->get_entities(
				array('class' => com_customer_interaction),
				array('&',
					'ref' => array('customer', $this),
					'tag' => array('com_customer', 'interaction')
				)
			);
		if ($module->com_sales) {
			$module->sales = $_->entity_manager->get_entities(
					array('class' => com_sales_sale),
					array('&',
						'ref' => array('customer', $this),
						'tag' => array('com_sales', 'sale')
					)
				);
			$module->returns = $_->entity_manager->get_entities(
					array('class' => com_sales_return),
					array('&',
						'ref' => array('customer', $this),
						'tag' => array('com_sales', 'return')
					)
				);
		}

		return $module;
	}

	/**
	 * Make the customer a member.
	 *
	 * If the customer is already a member, make_member() does nothing. It not,
	 * make_member() will set $this->member to true and set $this->member_since
	 * to the current timestamp.
	 */
	public function make_member() {
		if ($this->member)
			return;
		$this->member = true;
		$this->member_since = time();
	}

	/**
	 * Save the customer.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		global $_;
		if (!isset($this->name))
			return false;
		if (!isset($this->guid) && $this->com_customer__unverified) {
			// Remember to send the verification email.
			$this->secret = uniqid('', true);
			$send_verification = true;
		}
		$return = parent::save();
		if ($return && $send_verification) {
			// Now send the verification email.
			if ($this->send_email_verification())
				pines_notice('A link was emailed to the provided address for verification. The new account will be limited until the address is verified.');
			else
				pines_error('Couldn\'t send verification email.');
		}
		return $return;
	}

	/**
	 * Print a form to edit the customer.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_customer', 'customer/form', 'content');
		$module->entity = $this;
		$module->com_sales = $_->depend->check('component', 'com_sales');
		$module->interactions = $_->entity_manager->get_entities(
				array('class' => com_customer_interaction),
				array('&',
					'ref' => array('customer', $this),
					'tag' => array('com_customer', 'interaction')
				)
			);
		if ($module->com_sales) {
			$module->sales = $_->entity_manager->get_entities(
					array('class' => com_sales_sale),
					array('&',
						'ref' => array('customer', $this),
						'tag' => array('com_sales', 'sale')
					)
				);
			$module->returns = $_->entity_manager->get_entities(
					array('class' => com_sales_return),
					array('&',
						'ref' => array('customer', $this),
						'tag' => array('com_sales', 'return')
					)
				);
		}

		return $module;
	}

	/**
	 * Schedule follow-up interactions for a customer.
	 * 
	 * @param user $employee The employee expected to follow-up.
	 * @param com_sales_sale $sale The sale to follow-up on.
	 * @param bool $warehouse Whether or not it is a warehouse shipping followup.
	 * @return bool Whether or not the follow-ups were scheduled.
	 */
	public function schedule_follow_up($employee = null, $sale = null, $warehouse = false) {
		global $_;

		if (!$_->config->com_customer->com_calendar || !isset($employee->guid))
			return false;
		// Change the timezone to enter the event with the user's timezone.
		date_default_timezone_set($employee->get_timezone());
		if ($warehouse) {
			$wh_follow_up = explode('|', $_->config->com_customer->wh_follow_up);
			$interaction = com_customer_interaction::factory();
			$interaction->customer = $this;
			$interaction->employee = $employee;
			if (isset($sale->guid))
				$interaction->sale = $sale;
			$interaction->action_date = strtotime('-1 day +'.$wh_follow_up[1]);
			$interaction->type = 'Follow-Up '.$wh_follow_up[0];
			$interaction->status = 'open';
			$interaction->comments = $wh_follow_up[2];
			if ($_->config->com_customer->com_calendar) {
				// Create the interaction calendar event.
				$event = com_calendar_event::factory();
				$event->employee = $employee;
				$event->appointment = true;
				$event->label = $interaction->type;
				$event->title = $wh_follow_up[0].' '.$this->name;
				$event->private = true;
				$event->all_day = false;
				$event->start = $interaction->action_date;
				$event->end = strtotime('+1 hour', $interaction->action_date);
				$event->color = 'greenyellow';
				$event->information = $employee->name." (".ucwords($interaction->status).") \n".$interaction->comments;
				$event->ac->other = 2;
				if (!$event->save())
					return false;

				$interaction->event = $event;
			}

			$interaction->ac->other = 2;
			if (!$interaction->save())
				return false;
			$event->appointment = $interaction;
			$event->group = $employee->group;
			$event->save();
		} else {
			foreach ($_->config->com_customer->follow_ups as $cur_follow_up) {
				$cur_follow_up = explode('|', $cur_follow_up);
				$interaction = com_customer_interaction::factory();
				$interaction->customer = $this;
				$interaction->employee = $employee;
				if (isset($sale->guid))
					$interaction->sale = $sale;
				$interaction->action_date = strtotime('-1 day +'.$cur_follow_up[1]);
				$interaction->type = 'Follow-Up '.$cur_follow_up[0];
				$interaction->status = 'open';
				$interaction->comments = $cur_follow_up[2];
				if ($_->config->com_customer->com_calendar) {
					// Create the interaction calendar event.
					$event = com_calendar_event::factory();
					$event->employee = $employee;
					$event->appointment = true;
					$event->label = $interaction->type;
					$event->title = $cur_follow_up[0].' '.$this->name;
					$event->private = true;
					$event->all_day = false;
					$event->start = $interaction->action_date;
					$event->end = strtotime('+1 hour', $interaction->action_date);
					$event->color = 'greenyellow';
					$event->information = $employee->name." (".ucwords($interaction->status).") \n".$interaction->comments;
					$event->ac->other = 2;
					if (!$event->save())
						return false;

					$interaction->event = $event;
				}

				$interaction->ac->other = 2;
				if (!$interaction->save())
					return false;
				$event->appointment = $interaction;
				$event->group = $employee->group;
				$event->save();
			}
		}
		return true;
	}

	/**
	 * Check whether a customer is a valid member (not expired).
	 * @return bool
	 */
	public function valid_member() {
		if (!$this->member)
			return false;
		return (time() < $this->member_exp);
	}
}