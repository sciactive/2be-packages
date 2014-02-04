<?php
/**
 * com_menueditor_entry class.
 *
 * @package Components\menueditor
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * An entry.
 *
 * @package Components\menueditor
 */
class com_menueditor_entry extends entity {
	protected $tags = array('com_menueditor', 'entry');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->top_menu = 'main_menu';
	}

	public static function etype() {
		return 'com_menueditor_entry';
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'menu entry';
			case 'types':
				return 'menu entries';
			case 'url_edit':
				if (gatekeeper('com_menueditor/editentry'))
					return pines_url('com_menueditor', 'entry/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_menueditor/listentries'))
					return pines_url('com_menueditor', 'entry/list');
				break;
			case 'icon':
				return 'picon-go-jump-locationbar';
		}
		return null;
	}

	/**
	 * Delete the entry.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted menu entry $this->name.", 'notice');
		return true;
	}

	/**
	 * Build and return a menu array to go in the menu service.
	 * @return array The menu entry array.
	 */
	public function menu_array() {
		if (isset($this->top_menu)) {
			$array = array(
				'path' => $this->location.'/'.$this->name,
				'text' => $this->text
			);
		} else {
			$array = array(
				'path' => $this->name,
				'text' => $this->text,
				'position' => $this->position
			);
		}
		if ($this->sort)
			$array['sort'] = true;
		if (!empty($this->link))
			$array['href'] = $this->link;
		if (!empty($this->onclick))
			$array['onclick'] = $this->onclick;
		$depend = $this->conditions;
		if ($this->children)
			$depend['children'] = true;
		$array['depend'] = $depend;
		return $array;
	}

	/**
	 * Save the entry.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the entry.
	 * 
	 * @param bool $override_page Whether to override the page with the output of the module. (This is needed because the menu arrays aren't available until after the kill scripts.)
	 * @return module The form's module.
	 */
	public function print_form($override_page = false) {
		global $_;
		$module = new module('com_menueditor', 'entry/form', 'content');
		$module->entity = $this;
		// Set up a hook to capture the menu entries before they get destroyed.
		if ($override_page)
			$callback = array($this, 'capture_menu_override');
		else
			$callback = array($this, 'capture_menu');
		$_->hook->add_callback('$_->menu->render', -1, $callback);
		$this->cur_module = $module;

		return $module;
	}

	/**
	 * Capture the menu entries before they are destroyed.
	 */
	public function capture_menu() {
		global $_;
		$this->cur_module->captured_menu_arrays = $_->menu->menu_arrays;
	}

	/**
	 * Capture the menu entries before they are destroyed.
	 * 
	 * Then override the page.
	 */
	public function capture_menu_override() {
		global $_;
		$this->cur_module->captured_menu_arrays = $_->menu->menu_arrays;
		$_->page->override = true;
		$_->page->override_doc($this->cur_module->render());
	}
}