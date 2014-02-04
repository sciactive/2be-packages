<?php
/**
 * pform class.
 *
 * @package Components\pform
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

class pform {
	/**
	 * A form element.
	 */
	const ELEMENT_FORM = 1;
	/**
	 * A text input element.
	 */
	const ELEMENT_TEXT = 2;
	/**
	 * A textbox input element.
	 */
	const ELEMENT_TEXTBOX = 3;
	/**
	 * A radio input element.
	 */
	const ELEMENT_RADIO = 4;
	/**
	 * A checkbox input element.
	 */
	const ELEMENT_CHECKBOX = 5;

	private $element = pform::ELEMENT_TEXT;
	public $attributes = array();

	public function __construct($element = pform::ELEMENT_TEXT) {
		$this->element = $element;
		//$this->attributes['input'] = ;
	}
}