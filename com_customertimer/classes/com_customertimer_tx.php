<?php
/**
 * com_customertimer_tx class.
 *
 * @package Components\customertimer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A transaction.
 *
 * @package Components\customertimer
 */
class com_customertimer_tx extends entity {
	public function __construct() {
		$args = func_get_args();
		if (!empty($args))
			call_user_func_array(array($this, 'add_tag'), $args);
	}

	/**
	 * Create a new instance.
	 * @return com_customertimer_tx The new instance.
	 */
	public static function factory() {
		global $_;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args);
		$_->hook->hook_object($entity, $class.'->', false);
		return $entity;
	}

	public static function etype() {
		return 'com_customertimer_tx';
	}
}