<?php
/**
 * View a shop's home page.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = h($this->entity->name);
$this->show_title = false;
?>
<style type="text/css">
	#p_muid_shop .shop_box {
		padding: 5px;
		border: 1px #BBB solid;
		border-radius: 3px;
		margin-bottom: 15px;
	}
</style>
<div id="p_muid_shop">
	<?php $this->entity->print_header(); ?>
	<div class="container-fluid" style="padding-top: 15px;">
		<div class="shop_page row">
			<div class="col-sm-8">
				<div class="shop_short_description shop_box">
					<?php echo $this->entity->short_description; ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="shop_owner shop_box"><?php $this->entity->print_owner(); ?></div>
			</div>
		</div>
		<div class="shop_page row">
			<div class="col-sm-8">
				<h4>Newest Products</h4>
				<div class="shop_products shop_box"><?php $this->entity->print_product_featurette(); ?></div>
			</div>
			<div class="col-sm-4">
				<h4>Browse by Category</h4>
				<div class="shop_categories shop_box"></div>
			</div>
		</div>
	</div>
</div>