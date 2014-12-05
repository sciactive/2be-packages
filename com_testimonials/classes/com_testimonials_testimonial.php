<?php
/**
 * com_testimonials_testimonial class.
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A testimonial.
 *
 * @package Components\testimonials
 */
class com_testimonials_testimonial extends Entity {
	const etype = 'com_testimonials_testimonial';
	protected $tags = array('com_testimonials', 'testimonial');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->ac = (object) array('user' => 3, 'group' => 3, 'other' => 2);
		$this->attributes = array();
	}

//	public function info($type) {
//		switch ($type) {
//			case 'name':
//				return "Application {$this->app_id}";
//			case 'type':
//				return 'application';
//			case 'types':
//				return 'applications';
//			case 'url_view':
//				if (gatekeeper('com_mifi/listapps') || gatekeeper('com_mifi/apply'))
//					return pines_url('com_mifi', 'viewoffer', array('id' => $this->guid));
//				break;
//			case 'url_edit':
//				if (gatekeeper('com_mifi/listapps'))
//					return pines_url('com_mifi', 'application/list', array('show' => 'app:'.$this->app_id));
//				break;
//			case 'url_list':
//				if (gatekeeper('com_mifi/listapps'))
//					return pines_url('com_mifi', 'application/list');
//				break;
//			case 'icon':
//				return 'picon-document-edit-verify';
//		}
//		return null;
//	}
	
	/**
	 * Return the entity helper module.
	 * @return module Entity helper module.
	 */
	public function helper() {
		return new module('com_testimonials', 'testimonial/helper');
	}
	
	public function info($type) {
		switch ($type) {
			case 'name':
				return "Testimonial $this->id";
			case 'type':
				return 'testimonial';
			case 'types':
				return 'testimonials';
			case 'url_edit':
				if (gatekeeper('com_testimonials/edittestimonials'))
					return pines_url('com_testimonials', 'testimonial/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_testimonials/listtestimonials'))
					return pines_url('com_testimonials', 'testimonials/list');
				break;
			case 'icon':
				return 'picon-bookmark-new-list';
		}
		return null;
	}

	/**
	 * Delete the testimonial.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted testimonial with ID $this->id.", 'notice');
		return true;
	}

	/**
	 * Save with an incremental ID.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		global $_;
		if (!isset($this->id))
			$this->id = $_->nymph->newUID('com_testimonials_testimonial');
		return parent::save();
	}

	/**
	 * Print a form to edit the loan.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_testimonials', 'testimonial/edit', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a form to change the status on a testimonial.
	 *
	 * Uses a page override to only print the form.
	 *
	 * @return module The form's module.
	 */
	public function changestatus_form() {
		global $_;

		$module = new module('com_testimonials', 'forms/changestatus', 'content');
		$module->entity = $this;

		$_->page->ajax($module->render(), 'text/html');
		return $module;
	}
	
	/**
	 * Creates and attaches a module which views a testimonial.
	 * @return module The module.
	 */
	public function print_view() {
		$module = new module('com_testimonials', 'testimonial/view', 'content');
		$module->entity = $this;

		return $module;
	}
	
	
	/**
	 * Creates the Author information on a testimonial
	 * based on Anon Preferences and Location
	 * 
	 * @return string.
	 */
	public function create_author() {
		$first_name = $this->customer->name_first;
		// Customer's Address Info
		$city = $this->customer->city;
		$state = $this->customer->state;

		if (!empty($city) && !empty($state)) {
			if ($this->anon) {
				return false;
			} else {
				return $first_name.' in '.$city.', '.$state;
			}
		} else {
			if ($this->anon) {
				return false;
			} else {
				return $first_name;
			}
		}
		
	}
}