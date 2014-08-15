<?php
/**
 * List of products, layout according to a template.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if (!$this->products) { ?>
<div class="com_shop_no_products">
	No matching products were found.
</div>
<?php } else { if (!$this->no_controls) {
		ob_start(); ?>
<div class="com_shop_paginate">
	<?php if ($this->pages != 1) { ?>
	<div style="float: left; margin: 1em 1em 1em 0;" class="pagination">
		<ul>
			<?php if ($this->page - 1 >= 1) { if ($this->page - 1 != 1) { ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => 1, 'sort' => $this->sort)))); ?>">&#8676;</a></li>
			<?php } ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page - 1, 'sort' => $this->sort)))); ?>">&larr;</a></li>
			<?php } if ($this->page - 2 >= 1) {
				if ($this->page - 2 > 1) { ?>
			<li class="disabled"><a href="javascript:void(0);">&hellip;</a></li>
				<?php } ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page - 2, 'sort' => $this->sort)))); ?>"><?php echo $this->page - 2; ?></a></li>
			<?php } if ($this->page - 1 >= 1) { ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page - 1, 'sort' => $this->sort)))); ?>"><?php echo $this->page - 1; ?></a></li>
			<?php } ?>
			<li class="active"><a href="javascript:void(0);"><?php e($this->page); ?></a></li>
			<?php if ($this->page + 1 <= $this->pages) { ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page + 1, 'sort' => $this->sort)))); ?>"><?php echo $this->page + 1; ?></a></li>
			<?php } ?>
			<?php if ($this->page + 2 <= $this->pages) { ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page + 2, 'sort' => $this->sort)))); ?>"><?php echo $this->page + 2; ?></a></li>
			<?php if ($this->page + 2 < $this->pages) { ?>
			<li class="disabled"><a href="javascript:void(0);">&hellip;</a></li>
				<?php }
				} ?>
			<?php if ($this->page + 1 <= $this->pages) { ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page + 1, 'sort' => $this->sort)))); ?>">&rarr;</a></li>
			<?php if ($this->page + 1 != $this->pages) { ?>
			<li><a href="<?php e(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->pages, 'sort' => $this->sort)))); ?>">&#8677;</a></li>
			<?php } } ?>
		</ul>
	</div>
	<?php } ?>
	<div style="float: right; margin: 1em 0 1em 1em;">
		Showing <?php echo $this->offset + 1; ?>-<?php echo $this->offset + count($this->products); ?> of <?php echo (int) $this->count; ?>.
		Sorted by
		<select class="form-control" name="sort" onchange="$_.get(<?php e(json_encode(pines_url($this->url_component, $this->url_action, array_merge($this->url_query, array('page' => $this->page))))); ?>, {sort: $(this).val()})">
			<option value="name"<?php echo ($this->sort == 'name') ? ' selected="selected"' : '' ?>>Name (A to Z)</option>
			<option value="name_r"<?php echo ($this->sort == 'name_r') ? ' selected="selected"' : '' ?>>Name (Z to A)</option>
			<option value="unit_price"<?php echo ($this->sort == 'unit_price') ? ' selected="selected"' : '' ?>>Price (Low to High)</option>
			<option value="unit_price_r"<?php echo ($this->sort == 'unit_price_r') ? ' selected="selected"' : '' ?>>Price (High to Low)</option>
			<option value="p_cdate"<?php echo ($this->sort == 'p_cdate') ? ' selected="selected"' : '' ?>>Date (Oldest First)</option>
			<option value="p_cdate_r"<?php echo ($this->sort == 'p_cdate_r') ? ' selected="selected"' : '' ?>>Date (Newest First)</option>
		</select>
	</div>
</div>
<?php $header = ob_get_clean(); echo $header; } ?>
<div id="p_muid_products" class="com_shop_products" style="clear: both;">
	<?php
	/**
	 * Include the category template.
	 */
	include(__DIR__.'/templates/'.clean_filename($this->template ?: $_->config->com_shop->category_template).'.php'); ?>
</div>
<?php if (!$this->no_controls) echo $header; }