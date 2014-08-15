<?php
/**
 * Shows category/product breadcrumbs.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$bc = '<li class="active"><span class="breadcrumb_item">'.h($this->entity->name).'</span></li>';

if ($this->entity->has_tag('product')) {
	$categories = (array) $_->entity_manager->get_entities(
			array('class' => com_sales_category),
			array('&',
				'tag' => array('com_sales', 'category'),
				'strict' => array('enabled', true),
				'ref' => array('products', $this->entity)
			)
		);
	foreach ($categories as $cur_entity) {
		if ($cur_entity->show_menu)
			break;
		$cur_parent = $cur_entity->parent;
		while (isset($cur_parent)) {
			if ($cur_parent->show_menu)
				break 2;
			$cur_parent = $cur_parent->parent;
		}
	}
} elseif ($this->entity->has_tag('category')) {
	$cur_entity = $this->entity->parent;
}

while (isset($cur_entity)) {
	$bc = '<li><a href="'.h(pines_url('com_shop', 'category/browse', array('a' => $cur_entity->alias))).'" class="breadcrumb_item">'.h($cur_entity->name).'</a></li> ' . $bc;
	if ($cur_entity->show_menu)
		unset($cur_entity);
	else
		$cur_entity = $cur_entity->parent;
}

if (isset($this->entity->shop)) {
	$bc = '<li><a href="'.h(pines_url('com_shop', null, array('id' => $this->entity->shop->guid))).'" class="breadcrumb_item">'.h($this->entity->shop->name).'</a></li> ' . $bc;
}

$bc = '<ul class="breadcrumb"><li><a href="'.h(pines_url()).'" class="breadcrumb_item">Home</a></li> ' . $bc . '</ul>';

echo $bc;