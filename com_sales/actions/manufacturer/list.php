<?php
/**
 * List manufacturers.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_sales->enable_manufacturers)
	throw HttpClientException(null, 404);

if ( !gatekeeper('com_sales/listmanufacturers') )
	punt_user(null, pines_url('com_sales', 'manufacturer/list'));

$_->com_sales->list_manufacturers();