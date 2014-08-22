<?php
/**
 * Determine whether to integrate with other components.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_sales->com_customer && !$_->depend->check('component', 'com_customer'))
	$_->config->com_sales->com_customer = false;

if ($_->config->com_sales->com_esp && !$_->depend->check('component', 'com_esp'))
	$_->config->com_sales->com_esp = false;

if ($_->config->com_sales->com_hrm && !$_->depend->check('component', 'com_hrm'))
	$_->config->com_sales->com_hrm = false;

if ($_->config->com_sales->per_item_salesperson && !$_->config->com_sales->com_hrm)
	$_->config->com_sales->per_item_salesperson = false;

if ($_->config->com_sales->com_shop && !$_->depend->check('component', 'com_shop&com_content'))
	$_->config->com_sales->com_shop = false;

$check_com_sales__config = function($value, $help = false){
	global $_, $check_com_sales__config;
	if ($help) {
		$return = array();
		$return['cname'] = 'POS Config Checker';
		$return['description'] = <<<'EOF'
Check against the current configuration of the Point of Sales component.
EOF;
		$return['syntax'] = <<<'EOF'
Provide the name of a true/false configuration variable of the POS component.
EOF;
		$return['examples'] = <<<'EOF'
enable_manufacturers
:	Check that manufacturers are enabled.

enable_manufacturers|unique_serials
:	Check that manufacturers are enabled and unique serials are required.
EOF;
		$return['simple_parse'] = true;
		return $return;
	}
	if ($value == '')
		return false;
	if (
			strpos($value, '&') !== false ||
			strpos($value, '|') !== false ||
			strpos($value, '!') !== false ||
			strpos($value, '(') !== false ||
			strpos($value, ')') !== false
		)
		return $_->depend->simple_parse($value, $check_com_sales__config);
	return (bool) $_->config->com_sales->$value;
};

$_->depend->checkers['com_sales__config'] = $check_com_sales__config;