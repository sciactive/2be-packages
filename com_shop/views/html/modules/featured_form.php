<?php
/**
 * Provides a form for the user to choose featured options.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$products = $_->nymph->getEntities(
		array('class' => com_sales_product),
		array('&',
			'tag' => array('com_sales', 'product'),
			'data' => array('featured', true)
		)
	);
$_->nymph->sort($products, 'name');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Product</span>
			<select class="pf-field form-control" name="id">
				<option value="random"<?php echo $this->id == 'random' ? ' selected="selected"' : ''; ?>>-- Random --</option>
				<?php foreach ($products as $cur_product) { ?>
				<option value="<?php e($cur_product->guid); ?>"<?php echo $this->id == "$cur_product->guid" ? ' selected="selected"' : ''; ?>><?php e($cur_product->name); ?></option>
				<?php } ?>
			</select></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Alt Text</span>
			<span class="pf-note">Leave blank to use product name.</span>
			<input class="pf-field form-control" type="text" name="alt_text" size="24" value="<?php e($this->alt_text); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Show Name</span>
			<input class="pf-field" type="checkbox" name="show_name" value="true"<?php echo !isset($this->show_name) || ($this->show_name == 'true') ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Show Thumbnail</span>
			<span class="pf-note">Will show the featured image if any, or regular thumbnail otherwise.</span>
			<input class="pf-field" type="checkbox" name="show_thumbnail" value="true"<?php echo !isset($this->show_thumbnail) || ($this->show_thumbnail == 'true') ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Show Price</span>
			<input class="pf-field" type="checkbox" name="show_price" value="true"<?php echo !isset($this->show_price) || ($this->show_price == 'true') ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Show Add to Cart Button</span>
			<input class="pf-field" type="checkbox" name="show_button" value="true"<?php echo !isset($this->show_button) || ($this->show_button == 'true') ? ' checked="checked"' : ''; ?> /></label>
	</div>
</div>