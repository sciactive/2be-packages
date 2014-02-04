<?php
/**
 * amount class.
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
 * An amount class to handle monetary amounts.
 *
 * @package Components\sales
 */
class Amount {
	private $currency;
	private $locale;
	private $amount;
	private $decimal;

	public function __construct($value = 0, $decimal = 2) {
		$this->currency = $pines->config->com_sales->currency;
		$this->locale = $pines->config->com_sales->locale;
		if ((int) $value === $value) {
			$this->setIntValue($value, $decimal);
		} elseif ((string) $value === $value) {
			$this->setStringValue($value);
		} else {
			throw new UnsupportedTypeException('Value type not supported.');
		}
	}
	
	public function setCurrency($currency) {
		$this->currency = $currency;
	}
	
	public function getCurrency($currency) {
		return $this->currency;
	}
	
	public function setLocale($locale) {
		$this->locale = $locale;
	}
	
	public function getLocale($locale) {
		return $this->locale;
	}
	
	public function setIntValue($value, $decimal = 2) {
		$mult = pow(10, $decimal);
		$this->amount = floor($value * $mult);
		$this->decimal = (int) substr("$value", -1*$decimal);
	}
	
	public function setStringValue($value) {
		$fmt = new NumberFormatter($this->locale, NumberFormatter::DECIMAL );
		echo $fmt->parse($value)."\n";
		echo $fmt->parse($value, NumberFormatter::TYPE_INT32)."\n";
	}
}

class UnsupportedTypeException extends Exception {}

class IncorrectStringValueFormatException extends Exception {}