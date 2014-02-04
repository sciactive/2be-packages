<?php
/**
 * configurator_component class.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A configurable component.
 *
 * @package Components\configure
 */
class configurator_component implements configurator_component_interface {
	/**
	 * The configuration defaults.
	 * @var array
	 */
	public $defaults = array();
	/**
	 * The current configuration.
	 * @var array
	 */
	public $config = array();
	/**
	 * The current configuration in an array with key => values.
	 * @var array
	 */
	public $config_keys = array();
	/**
	 * The info object of the component.
	 * @var object
	 */
	public $info;
	/**
	 * The component.
	 * @var string
	 */
	public $name;
	/**
	 * The component.
	 * @var string
	 */
	protected $component;
	/**
	 * The defaults file.
	 * @var string
	 */
	protected $defaults_file;
	/**
	 * The config file.
	 * @var string
	 */
	protected $config_file;
	/**
	 * The info file.
	 * @var string
	 */
	protected $info_file;
	/**
	 * Whether the component is using per user/group/condition config.
	 * @var bool
	 */
	public $per_user;
	/**
	 * "user" or "group".
	 * @var string
	 */
	public $type;

	/**
	 * Load a component's configuration and info.
	 * @param string $component The component to load.
	 */
	public function __construct($component) {
		global $_;
		if (!key_exists($component, $_->configurator->component_files))
			return;
		$this->component = $component;
		$this->name = $component;
		$this->defaults_file = $_->configurator->component_files[$component]['defaults'];
		$this->config_file = $_->configurator->component_files[$component]['config'];
		$this->info_file = $_->configurator->component_files[$component]['info'];
		if (file_exists($this->defaults_file))
			$this->defaults = include($this->defaults_file);
		if (file_exists($this->config_file)) {
			$this->config = include($this->config_file);
			foreach ($this->config as $cur_val) {
				$this->config_keys[$cur_val['name']] = $cur_val['value'];
			}
		}
		if (file_exists($this->info_file))
			$this->info = (object) include($this->info_file);
	}

	/**
	 * Create a new instance.
	 * @param string $component The component to load.
	 * @return configurator_component The new instance.
	 */
	public static function factory($component) {
		global $_;
		$class = get_class();
		$args = func_get_args();
		$object = new $class($args[0]);
		$_->hook->hook_object($object, $class.'->', false);
		return $object;
	}

	/**
	 * Get a full config array. (With defaults replaced.)
	 * @return array The array.
	 */
	public function get_full_config_array() {
		$array = $this->defaults;
		foreach ($array as &$cur_val) {
			if (key_exists($cur_val['name'], $this->config_keys))
				$cur_val['value'] = $this->config_keys[$cur_val['name']];
		}
		return $array;
	}

	/**
	 * Check if a component is configurable.
	 * @return bool True or false.
	 */
	public function is_configurable() {
		return !empty($this->defaults);
	}

	/**
	 * Check if a component is disabled.
	 * @return bool True or false.
	 */
	public function is_disabled() {
		global $_;
		return ($this->component != 'system' && in_array($this->component, array_diff($_->all_components, $_->components)));
	}

	/**
	 * Print a form to edit the configuration.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_configure', 'edit', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Print a view of the configuration.
	 * @return module The view's module.
	 */
	public function print_view() {
		$module = new module('com_configure', 'view', 'content');
		$module->entity = $this;

		return $module;
	}

	/**
	 * Write the configuration to the config file or user/group/condition.
	 * @return bool True on success, false on failure.
	 */
	public function save_config() {
		if ($this->per_user) {
			// Save the config to the user/group/condition in two variables.
			$this->config_keys = array();
			foreach ($this->config as $cur_val) {
				$this->config_keys[$cur_val['name']] = $cur_val['value'];
			}
			if ($this->component == 'system') {
				$this->user->sys_config = $this->config_keys;
				if (empty($this->config_keys))
					unset($this->user->sys_config);
			} else {
				if (!is_array($this->user->com_config))
					$this->user->com_config = array();
				$this->user->com_config[$this->component] = $this->config_keys;
				if (empty($this->config_keys))
					unset($this->user->com_config[$this->component]);
				if (empty($this->user->com_config))
					unset($this->user->com_config);
			}
			return $this->user->save();
		} else {
			// Save the config to a system wide config file.
			if (empty($this->config)) {
				if (file_exists($this->config_file)) {
					return unlink($this->config_file);
				} else {
					return true;
				}
			} else {
				$file_contents = sprintf("<?php\ndefined('P_RUN') or die('Direct access prohibited');\nreturn %s;\n?>",
					var_export($this->config, true)
				);
				return file_put_contents($this->config_file, $file_contents);
			}
		}
	}

	public function set_config($config_keys) {
		$this->config_keys = $config_keys;
		$this->config = array();
		foreach ($config_keys as $key => $value) {
			$this->config[] = array(
				'name' => $key,
				'value' => $value
			);
		}
	}

	/**
	 * Load only user configurable settings.
	 *
	 * The current settings will be updated to reflect the settings of
	 * $condition_obj.
	 *
	 * @param user|group|com_configure_condition &$condition_obj The user, group, or conditional object which is being configured.
	 */
	public function set_per_user(&$condition_obj = null) {
		$this->per_user = true;
		// Unset config that are not per user.
		foreach ($this->defaults as $key => &$cur_entry) {
			if (!$cur_entry['peruser']) {
				unset($this->defaults[$key]);
			} elseif (key_exists($cur_entry['name'], $this->config_keys)) {
				$cur_entry['value'] = $this->config_keys[$cur_entry['name']];
			}
		}
		unset($cur_entry);

		if (!isset($condition_obj)) {
			$this->config = array();
			$this->config_keys = array();
			return;
		}

		// Load the config for the user/group.
		if ($this->component == 'system') {
			$this->config_keys = (array) $condition_obj->sys_config;
		} else {
			$this->config_keys = (array) $condition_obj->com_config[$this->component];
		}
		$this->config = $this->defaults;
		/* This causes PHP (5.3.2) to segfault... ??
		foreach ($this->config as $key => &$cur_entry) {
			if (!key_exists($cur_entry['name'], $this->config_keys)) {
				unset($this->config[$key]);
			} else {
				$cur_entry['value'] = $this->config[$key];
			}
		}*/
		foreach ($this->config as $key => $cur_entry) {
			if (!key_exists($cur_entry['name'], $this->config_keys)) {
				unset($this->config[$key]);
			} else {
				$this->config[$key]['value'] = $this->config[$key];
			}
		}

		// Store the type and object of the user/group.
		if (is_a($condition_obj, 'user') || is_a($condition_obj, 'hook_override_user')) {
			$this->type = 'user';
		} elseif (is_a($condition_obj, 'group') || is_a($condition_obj, 'hook_override_group')) {
			$this->type = 'group';
		} elseif ($condition_obj->is_com_configure_condition) {
			$this->type = 'condition';
		}
		$this->user = $condition_obj;
	}
}