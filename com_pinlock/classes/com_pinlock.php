<?php
/**
 * com_pinlock class.
 *
 * @package Components\pinlock
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_pinlock main class.
 *
 * @package Components\pinlock
 */
class com_pinlock extends component {
	/**
	 * The component originally requested.
	 * @var string $component
	 */
	public $component;
	/**
	 * The action originally requested.
	 * @var string $action
	 */
	public $action;
	/**
	 * A unique session ID used to remember POST and GET data.
	 * @var string $sessionid
	 */
	public $sessionid;
}