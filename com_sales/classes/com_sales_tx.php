<?php
/**
 * com_sales_tx class.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A transaction.
 *
 * @package Components\sales
 */
class com_sales_tx extends entity {
	/**
	 * Create a new instance.
	 * @return com_sales_tx The new instance.
	 */
	public static function factory() {
		global $pines;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args);
		$entity->add_tag('com_sales', 'transaction');
		$entity->ac = (object) array('user' => 2, 'group' => 2, 'other' => 2);
		$pines->hook->hook_object($entity, $class.'->', false);
		return $entity;
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Transaction $this->guid";
			case 'type':
				return 'transaction';
			case 'types':
				return 'transactions';
			case 'icon':
				return 'picon-document-swap';
		}
		return null;
	}
}