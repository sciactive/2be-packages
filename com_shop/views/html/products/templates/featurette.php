<?php
/**
 * Featurette layout of products.
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
#p_muid_products .product {cursor: pointer; border-right: 1px #ccc solid;}
#p_muid_products .product:hover .product_main {outline: 1px dotted #ccc;}
#p_muid_products .product_main {padding: 0 .6em;}
#p_muid_products .thumb {margin: .5em;}
#p_muid_products .name {font-size: 1.2em;}
#p_muid_products .desc ul, #p_muid_products .desc ol {padding-left: 2em;}
</style>
<script type="text/javascript">
$_(function(){
	$("#p_muid_products").on("click", ".product", function(){
		$_.get(<?php echo json_encode(pines_url('com_shop', 'product', array('a' => '__alias__'))); ?>.replace("__alias__", $(this).children(".product_alias").text()));
	});
});
</script>
<div class="row">
	<?php $i = 0; foreach ($this->products as $key => $cur_product) {
		if ($i && !($i % 4)) { ?>
</div>
<hr />
<div class="row">
	<?php } $i++; ?>
	<div class="col-sm-3 product">
		<div class="product_guid" style="display: none;"><?php e($cur_product->guid); ?></div>
		<div class="product_alias" style="display: none;"><?php e($cur_product->alias); ?></div>
		<div class="product_main">
			<?php if (isset($cur_product->thumbnail)) { ?>
			<div style="text-align: center;">
				<img class="thumb" alt="<?php e($cur_product->name); ?>" src="<?php e($_->config->location.$cur_product->thumbnail); ?>" />
			</div>
			<?php } ?>
			<div class="name"><a href="<?php e(pines_url('com_shop', 'product', array('a' => $cur_product->alias))); ?>"><?php e($cur_product->name); ?></a></div>

			<div class="desc"><?php echo format_content($cur_product->short_description); ?></div>
		</div>
	</div>
	<?php } ?>
</div>
<br style="clear: both; height: 0; font-size: 0;" />