<?php
/**
 * Row layout of products.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style type="text/css">
#p_muid_products .product {cursor: pointer;}
#p_muid_products .product_right, #p_muid_products .product_main {margin: 0; padding: 0; width: 25%;}
#p_muid_products .product_main {width: 74%;}
#p_muid_products .product_left, #p_muid_products .product_info {margin: 0; padding: 0; float: left;}
#p_muid_products .product_left {width: 25%; text-align: center;}
#p_muid_products .size_less {width: 75%;}
#p_muid_products .product_right {float: right; text-align: center;}
#p_muid_products .padding_box {padding: .8em;}
#p_muid_products .product_info .padding_box {padding: 0;}
#p_muid_products .price {font-size: 1.4em; font-weight: bold; margin-bottom: .5em;}
#p_muid_products .name {font-size: 1.2em;}
#p_muid_products .info, #p_muid_products .desc {font-size: .9em;}
#p_muid_products .desc ul, #p_muid_products .desc ol {padding-left: 2em;}
#p_muid_products .price .value {display: none;}
</style>
<script type="text/javascript">
$_(function(){
	$("#p_muid_products").on("click", ".product", function(){
		$_.get(<?php echo json_encode(pines_url('com_shop', 'product', array('a' => '__alias__'))); ?>.replace("__alias__", $(this).children(".product_alias").text()));
	}).on("click", "button.add_cart", function(e){
		<?php if (!$_->config->com_shop->catalog_mode) { ?>
		var button = $(this), product = button.closest(".product"), guid = parseInt(product.find(".product_guid").text());
		$_.com_shop_add_to_cart(guid, product.find(".name").text(), parseFloat(product.find(".price > .value").text()), product);
		e.stopPropagation();
		<?php } ?>
		e.preventDefault();
	});
});
</script>
<div style="height: .5em;">&nbsp;</div>
<?php foreach ($this->products as $key => $cur_product) { ?>
<div class="product page-header">
	<div class="product_guid" style="display: none;"><?php e($cur_product->guid); ?></div>
	<div class="product_alias" style="display: none;"><?php e($cur_product->alias); ?></div>
	<div class="product_right alert alert-info">
		<div class="padding_box">
			<div class="price"><?php echo $_->com_shop->format_price($cur_product->unit_price); ?><span class="value"><?php echo isset($cur_product->unit_price) ? round($cur_product->unit_price, 2) : ''; ?></span></div>
			<?php if (!$_->config->com_shop->catalog_mode) { ?>
			<div class="product_button">
				<button class="add_cart btn btn-primary"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="product_main">
		<?php if (isset($cur_product->thumbnail)) { ?>
		<div class="product_left">
			<div class="padding_box">
				<img class="thumb" alt="<?php e($cur_product->name); ?>" src="<?php e($_->config->location.$cur_product->thumbnail); ?>" />
			</div>
		</div>
		<?php } ?>
		<div class="product_info<?php echo isset($cur_product->thumbnail) ? ' size_less' : ''; ?>">
			<div class="padding_box">
				<div class="name"><a href="<?php e(pines_url('com_shop', 'product', array('a' => $cur_product->alias))); ?>"><?php e($cur_product->name); ?></a></div>
				<div class="info">
					<?php if ($_->config->com_sales->enable_manufacturers && !empty($cur_product->manufacturer_sku)) { ?>
					<strong>Model:</strong> <?php echo format_content(h($cur_product->manufacturer_sku)); ?> |
					<?php } ?>
					<strong>SKU:</strong> <?php echo format_content(h($cur_product->sku)); ?>
				</div>
				<br />
				<div class="desc"><?php echo format_content($cur_product->short_description); ?></div>
			</div>
		</div>
	</div>
	<div style="clear: both; height: 0; font-size: 0;">&nbsp;</div>
</div>
<?php } ?>
<br style="clear: both; height: 0; font-size: 0;" />