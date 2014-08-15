<?php
/**
 * View a shop.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

$entity = com_shop_shop::factory((int) $_REQUEST['id']);
if (!$entity->guid)
	throw new HttpClientException(null, 404);
$entity->home();
