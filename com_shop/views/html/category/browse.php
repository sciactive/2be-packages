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
	if (!$products) { ?>
<div class="com_shop_no_products">
	No matching products were found.
</div>
<?php } else {
		ob_start(); ?>
<div class="com_shop_paginate">
	<?php if ($pages != 1) { ?>
	<div style="float: left; margin: 1em 1em 1em 0;" class="pagination">
		<ul>
			<?php if ($this->page - 1 >= 1) { if ($this->page - 1 != 1) { ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => 1, 'sort' => $this->sort))); ?>">&#8676;</a></li>
			<?php } ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page - 1, 'sort' => $this->sort))); ?>">&larr;</a></li>
			<?php } if ($this->page - 2 >= 1) {
				if ($this->page - 2 > 1) { ?>
			<li class="disabled"><a href="javascript:void(0);">&hellip;</a></li>
				<?php } ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page - 2, 'sort' => $this->sort))); ?>"><?php echo $this->page - 2; ?></a></li>
			<?php } if ($this->page - 1 >= 1) { ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page - 1, 'sort' => $this->sort))); ?>"><?php echo $this->page - 1; ?></a></li>
			<?php } ?>
			<li class="active"><a href="javascript:void(0);"><?php e($this->page); ?></a></li>
			<?php if ($this->page + 1 <= $pages) { ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page + 1, 'sort' => $this->sort))); ?>"><?php echo $this->page + 1; ?></a></li>
			<?php } ?>
			<?php if ($this->page + 2 <= $pages) { ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page + 2, 'sort' => $this->sort))); ?>"><?php echo $this->page + 2; ?></a></li>
			<?php if ($this->page + 2 < $pages) { ?>
			<li class="disabled"><a href="javascript:void(0);">&hellip;</a></li>
				<?php }
				} ?>
			<?php if ($this->page + 1 <= $pages) { ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page + 1, 'sort' => $this->sort))); ?>">&rarr;</a></li>
			<?php if ($this->page + 1 != $pages) { ?>
			<li><a href="<?php e(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $pages, 'sort' => $this->sort))); ?>">&#8677;</a></li>
			<?php } } ?>
		</ul>
	</div>
	<?php } ?>
	<div style="float: right; margin: 1em 0 1em 1em;">
		Showing <?php echo $offset + 1; ?>-<?php echo $offset + count($products); ?> of <?php echo (int) $count; ?>.
		Sorted by <select class="form-control" name="sort" onchange="$_.get(<?php e(json_encode(pines_url('com_shop', 'category/browse', array('a' => $this->entity->alias, 'page' => $this->page)))); ?>, {sort: $(this).val()})">
			<option value="name"<?php echo ($this->sort == 'name') ? ' selected="selected"' : '' ?>>Name (A to Z)</option>
			<option value="name_r"<?php echo ($this->sort == 'name_r') ? ' selected="selected"' : '' ?>>Name (Z to A)</option>
			<option value="unit_price"<?php echo ($this->sort == 'unit_price') ? ' selected="selected"' : '' ?>>Price (Low to High)</option>
			<option value="unit_price_r"<?php echo ($this->sort == 'unit_price_r') ? ' selected="selected"' : '' ?>>Price (High to Low)</option>
			<option value="p_cdate"<?php echo ($this->sort == 'p_cdate') ? ' selected="selected"' : '' ?>>Date (Oldest First)</option>
			<option value="p_cdate_r"<?php echo ($this->sort == 'p_cdate_r') ? ' selected="selected"' : '' ?>>Date (Newest First)</option>
		</select>
	</div>
</div>
<?php $header = ob_get_clean(); echo $header; ?>
<div id="p_muid_products" class="com_shop_products" style="clear: both;">
	<?php
	/**
	 * Include the category template.
	 */
	include(__DIR__.'/templates/'.clean_filename($_->config->com_shop->category_template).'.php'); ?>
</div>
<?php echo $header; } }