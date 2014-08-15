<?php
/**
 * Shows category products.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if ($this->entity->show_title)
	$this->title = empty($this->entity->replace_title) ? h($this->entity->name) : h($this->entity->replace_title);

// Get the products in this category.
if ($this->entity->show_products)
	$products = $_->com_shop->get_cat_products($this->entity, $this->page, $this->products_per_page, $offset, $count, $pages, $this->sort_var, $this->sort_reverse);

foreach ((array) $this->show_page_modules as $cur_module) {
	echo $cur_module->render();
} if ($this->entity->show_children) { ?>
<div id="p_muid_children" class="com_shop_children">
	In this category:
	<ul>
		<?php foreach ($this->entity->children as $cur_child) {
			if (!isset($cur_child) || !$cur_child->enabled)
				continue;
			?>
		<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $cur_child->alias))); ?>"><?php e($cur_child->name); ?></a></li>
		<?php } ?>
	</ul>
</div>
<?php } if ($this->entity->show_products) {
	$module = new module('com_shop', 'products/list');
	$module->products = $products;
	$module->url_component = 'com_shop';
	$module->url_action = 'category/browse';
	$module->url_query = array('a' => $this->entity->alias);
	$module->pages = $pages;
	$module->page = $this->page;
	$module->sort = $this->sort;
	$module->offest = $this->page;
	$module->count = $this->count;
	echo $module->render();
}