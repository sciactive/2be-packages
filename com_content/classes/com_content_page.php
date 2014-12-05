<?php
/**
 * com_content_page class.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A page.
 *
 * @package Components\content
 */
class com_content_page extends Entity {
	const etype = 'com_content_page';
	protected $tags = array('com_content', 'page');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->enabled = true;
		$this->title_use_name = true;
		$this->content_tags = array();
		$this->com_menueditor_entries = array();
		$this->conditions = array();
		$this->publish_end = null;
		$this->variants = array();
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return $this->name;
			case 'type':
				return 'content page';
			case 'types':
				return 'content pages';
			case 'url_view':
				return pines_url('com_content', 'page', array('a' => $this->alias));
			case 'url_edit':
				if (gatekeeper('com_content/editpage'))
					return pines_url('com_content', 'page/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_content/listpages'))
					return pines_url('com_content', 'page/list');
				break;
			case 'icon':
				return 'picon-text-html';
		}
		return null;
	}

	/**
	 * Delete the page.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		global $_;
		// Remove page from categories.
		$cats = $_->nymph->getEntities(
				array('class' => com_content_category, 'skip_ac' => true),
				array('&',
					'tag' => array('com_content', 'category'),
					'ref' => array('pages', $this)
				)
			);
		foreach ($cats as &$cur_cat) {
			while (($key = $this->arraySearch($cur_cat->pages)) !== false) {
				unset($cur_cat->pages[$key]);
				$cur_cat->pages = array_values($cur_cat->pages);
			}
			if (!$cur_cat->save()) {
				pines_error("Couldn't remove page from category, {$cur_cat->name}.");
				pines_log("Couldn't remove page from category, {$cur_cat->name}.", 'error');
				return false;
			}
		}
		unset($cur_cat);
		if (!parent::delete())
			return false;
		pines_log("Deleted page $this->name.", 'notice');
		return true;
	}

	/**
	 * Get an array of categories' GUIDs this page belongs to.
	 * @return array An array of GUIDs.
	 */
	public function get_categories_guid() {
		$categories = $this->get_categories($page);
		foreach ($categories as &$cur_cat) {
			$cur_cat = $cur_cat->guid;
		}
		unset($cur_cat);
		return $categories;
	}

	/**
	 * Get an array of categories this page belongs to.
	 * @return array An array of categories.
	 */
	public function get_categories() {
		global $_;
		$categories = (array) $_->nymph->getEntities(array('class' => com_content_category), array('&', 'tag' => array('com_content', 'category'), 'ref' => array('pages', $this)));
		return $categories;
	}

	/**
	 * Get an option if it's set, the default otherwise.
	 * @param string $name The name of the option.
	 * @return mixed The value.
	 */
	public function get_option($name) {
		if (isset($this->$name))
			return $this->$name;
		global $_;
		$config_name = "def_page_$name";
		return $_->config->com_content->$config_name;
	}

	/**
	 * Save the page.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print the page intro.
	 * @return module The form's module.
	 */
	public function print_intro() {
		if (!$this->ready())
			return null;
		$module = new module('com_content', 'page/intro', 'content');
		$module->entity = $this;

		return $module;	var_dump($cur_entity->content_tags);
	}

	/**
	 * Print a form to edit the page.
	 * @return module The form's module.
	 */
	public function print_form() {
		global $_;
		$module = new module('com_content', 'page/form', 'content');
		$module->entity = $this;
		$module->categories = (array) $_->nymph->getEntities(
				array('class' => com_content_category),
				array('&',
					'tag' => array('com_content', 'category'),
					'data' => array('enabled', true)
				)
			);
		if (isset($_->editor)) {
			foreach ($_->com_content->get_custom_css() as $cur_file)
				$_->editor->add_css($cur_file);
		}

		return $module;
	}

	/**
	 * Print the page content.
	 * @return module The page's module.
	 */
	public function print_page() {
		if (!$this->ready())
			return null;
		$module = new module('com_content', 'page/page', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Determine if this page is ready to print.
	 *
	 * This function will check the publish date against today's date. It will
	 * then check the conditions of the page. If the page is disabled, the date
	 * is outside the publish date range, or any of the conditions aren't met,
	 * it will return false.
	 *
	 * @return bool True if the page is ready, false otherwise.
	 */
	public function ready() {
		if (!$this->enabled)
			return false;
		// Check the publish date.
		$time = time();
		if ($this->publish_begin > $time)
			return false;
		if (isset($this->publish_end) && $this->publish_end <= $time)
			return false;
		if (!$this->conditions)
			return true;
		global $_;
		// Check that all conditions are met.
		foreach ($this->conditions as $cur_type => $cur_value) {
			if (!$_->depend->check($cur_type, $cur_value))
				return false;
		}
		return true;
	}
}