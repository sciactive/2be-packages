<?php
/**
 * Shows shopping cart.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$_->icons->load();
?>
<style type="text/css">
	.com_shop_nobg {
		background-image: none;
		opacity: .3;
	}
	#com_shop_cart_controls .yes_items {
		border-top-width: 1px;
		border-top-style: solid;
		padding: .2em;
		clear: both;
	}
	#com_shop_cart_controls .yes_items .cart_link {
		float: right;
		padding: 1px 3px;
	}
	#com_shop_cart_controls .yes_items .subtotal {
		float: right;
		padding: 1px 3px;
	}
	#com_shop_cart_controls .yes_items .subtotal_label {
		float: left;
		padding: 1px 3px;
	}
	#com_shop_cart_controls .yes_items .empty_cart {
		float: left;
		clear: left;
		margin-top: .5em;
	}
	#com_shop_cart_controls .yes_items .checkout {
		float: right;
		clear: right;
		margin-top: .5em;
	}

	#com_shop_cart .product {
		border-bottom-width: 1px;
		border-bottom-style: solid;
		padding: .2em;
	}
	#com_shop_cart .name {
		float: left;
		padding: 1px 3px;
	}
	#com_shop_cart .qty {
		float: right;
		padding: 1px 3px;
		cursor: pointer;
	}
	#com_shop_cart .price {
		float: right;
		padding: 1px 3px;
		width: 4em;
		text-align: right;
	}
	#com_shop_cart button, #com_shop_cart_controls button {
		display: block;
		float: left;
		padding: 1px 3px;
	}
	#com_shop_cart button *, #com_shop_cart_controls button * {
		margin: 0;
		padding: 0;
	}
</style>
<script type="text/javascript">
	$_(function(){
		var dec = <?php echo (int) $_->config->com_sales->dec; ?>;
		var round_to_dec = function(value){
			var rnd = Math.pow(10, dec);
			var mult = value * rnd;
			value = gaussianRound(mult);
			value /= rnd;
			value = value.toFixed(dec);
			return (value);
		};
		var gaussianRound = function(x){
			var absolute = Math.abs(x);
			var sign     = x == 0 ? 0 : (x < 0 ? -1 : 1);
			var floored  = Math.floor(absolute);
			if (absolute - floored != 0.5) {
				return Math.round(absolute) * sign;
			}
			if (floored % 2 == 1) {
				// Closest even is up.
				return Math.ceil(absolute) * sign;
			}
			// Closest even is down.
			return floored * sign;
		};

		var no_items = $("> div.no_items", "#com_shop_cart_controls");
		var yes_items = $("> div.yes_items", "#com_shop_cart_controls");
		var check_products = function(){
			if ($("> div.product", "#com_shop_cart").length) {
				no_items.fadeOut();
				yes_items.fadeIn();
				total_products();
			} else {
				no_items.fadeIn();
				yes_items.fadeOut();
			}
		};
		var total_products = function(){
			var subtotal = 0;
			$("> div.product", "#com_shop_cart").each(function(){
				var product = $(this);
				var unit_price = parseFloat(product.find("> div.unit_price").text());
				var qty = parseInt(product.find("> div.qty").text());

				var price = unit_price * qty;
				product.find("> div.price").html($_.safe("$"+round_to_dec(price)));
				subtotal += price;
			});
			$("div.subtotal", "#com_shop_cart_controls").html($_.safe("$"+round_to_dec(subtotal)));
		};
		check_products();

		$("#com_shop_cart").on("click", "div.product button", function(){
			var product = $(this).closest("div.product");
			var guid = parseInt(product.children("div.guid").text());
			$.ajax({
				url: <?php echo json_encode(pines_url()); ?>,
				type: "POST",
				dataType: "json",
				data: {option: "com_shop", action: "cart/remove", id: guid},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured while trying to remove the product from your cart:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
				},
				success: function(data){
					if (!data) {
						$_.error("The product could not be removed from your cart.");
						return;
					}
					product.fadeOut("reg", function(){
						product.remove();
						check_products();
					});
				}
			});
		}).on("mouseenter", "div.product", function(){
			$(this).children("div.qty").addClass("btn");
		}).on("mouseleave", "div.product", function(){
			$(this).children("div.qty").removeClass("btn");
		}).on("click", "div.product div.qty", function(){
			var qty = $(this);
			var old_val = qty.text();
			$("<input type=\"text\" class=\"qty ui-corner-all\" />").css({
				"width": qty.width(),
				"height": qty.height()
			})
			.val(old_val)
			.keydown(function(e){
				if (e.keyCode == 13) {
					$(this).blur();
					return false;
				}
			})
			.blur(function(){
				var input = $(this);
				var new_val = input.val();
				input.remove();
				if (new_val < 1)
					new_val = 1;
				qty.html($_.safe(new_val)).show();
				$_.com_shop_adjust_quantity(parseInt(qty.siblings("div.guid").text()), old_val, new_val);
			})
			.keyup(function(){
				var input = $(this);
				qty.html($_.safe(input.val())+"_");
				input.css("width", qty.width());
			}).keyup()
			.insertAfter(qty)
			.select()
			.focus();
			qty.removeClass("ui-state-hover").hide();
		});

		$("#com_shop_cart_controls").on("click", "div.yes_items button.empty_cart", function(){
			if (!confirm("Are you sure you want to remove all items from your cart?"))
				return;
			$.ajax({
				url: <?php echo json_encode(pines_url()); ?>,
				type: "POST",
				dataType: "json",
				data: {option: "com_shop", action: "cart/empty"},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured while trying to empty your cart:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
				},
				success: function(data){
					if (!data) {
						$_.error("Your cart could not be emptied.");
						return;
					}
					$("> div.product", "#com_shop_cart").fadeOut("reg", function(){
						$(this).remove();
					});
					// Skip check_products() for speed.
					no_items.fadeIn();
					yes_items.fadeOut();
				}
			});
		});

		$_.com_shop_adjust_quantity = function(guid, old_qty, new_qty){
			var qty = $("> div.product.guid_"+guid+" div.qty", "#com_shop_cart");
			if (!qty.length) return;
			$.ajax({
				url: <?php echo json_encode(pines_url()); ?>,
				type: "POST",
				dataType: "json",
				data: {option: "com_shop", action: "cart/qty", id: guid, qty: new_qty},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured while trying to adjust quantity:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
					qty.html(old_qty);
				},
				success: function(data){
					if (!data || data == "one_per") {
						if (data == "one_per")
							alert("Only one of this item is allowed per ticket.");
						else
							$_.error("The quantity could not be adjusted.");
						qty.html(old_qty);
						return;
					}
					qty.effect("highlight");
					total_products();
				}
			});
		};

		$_.com_shop_add_to_cart = function(guid, name, unit_price, element){
			if (isNaN(guid))
				return false;
			$.ajax({
				url: <?php echo json_encode(pines_url()); ?>,
				type: "POST",
				dataType: "json",
				data: {option: "com_shop", action: "cart/add", id: guid},
				error: function(XMLHttpRequest, textStatus){
					$_.error("An error occured while trying to add the product to your cart:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
				},
				success: function(data){
					if (!data || data == "one_per") {
						if (data == "one_per")
							alert("Only one of this item is allowed per ticket.");
						else
							$_.error("The product could not be added to your cart.");
						return;
					}
					var product_elem = $("> div.product.guid_"+guid, "#com_shop_cart");
					if (product_elem.length) {
						var qty = parseInt(product_elem.children("div.qty").text());
						product_elem.children("div.qty").html(++qty).effect("highlight");
					} else {
						product_elem = $("> div.template", "#com_shop_cart").clone();
						product_elem
						.removeClass("template")
						.addClass("product guid_"+guid)
						.find("div.guid").html($_.safe(guid)).end()
						.find("div.unit_price").html($_.safe(unit_price)).end()
						.find("div.name").html($_.safe(name)).end()
						.find("div.price").html($_.safe("$"+round_to_dec(unit_price))).end()
						.appendTo("#com_shop_cart")
						.fadeIn();
					}
					if (element)
						element.effect("transfer", {to: product_elem, className: 'ui-state-focus ui-priority-secondary com_shop_nobg'});
					check_products();
				}
			});
		};
	});
</script>
<div id="com_shop_cart">
	<div class="template" style="display: none;">
		<div class="guid" style="display: none;"></div>
		<div class="unit_price" style="display: none;"></div>
		<button type="button" class="btn btn-default" title="Remove"><i class="fa fa-times"></i></button>
		<div class="name"></div>
		<?php if ($_->config->com_shop->cart_prices) { ?>
		<div class="price"></div>
		<?php } ?>
		<div class="qty ui-corner-all">1</div>
		<br style="clear: both; height: 0;" />
	</div>
	<?php foreach ($_->com_shop->cart() as $cur_item) { ?>
	<div class="product guid_<?php e($cur_item['product']->guid); ?>">
		<div class="guid" style="display: none;"><?php e($cur_item['product']->guid); ?></div>
		<div class="unit_price" style="display: none;"><?php e($cur_item['product']->unit_price); ?></div>
		<button type="button" class="btn btn-default" title="Remove"><i class="fa fa-times"></i></button>
		<div class="name"><?php e($cur_item['product']->name); ?></div>
		<?php if ($_->config->com_shop->cart_prices) { ?>
		<div class="price">$<?php e($_->com_sales->round($cur_item['product']->unit_price * $cur_item['quantity'], true)); ?></div>
		<?php } ?>
		<div class="qty ui-corner-all"><?php e($cur_item['quantity']); ?></div>
		<br style="clear: both; height: 0;" />
	</div>
	<?php } ?>
</div>
<div id="com_shop_cart_controls">
	<div class="yes_items"<?php echo !$_->com_shop->cart() ? ' style="display: none;"' : ''; ?>>
		<?php if ($_->config->com_shop->cart_subtotal) { ?>
		<div class="subtotal_label">Subtotal <small>(before tax/fees)</small></div><div class="subtotal"></div>
		<br style="clear: both; height: 0;" />
		<?php } if ($_->config->com_shop->cart_link) { ?>
		<div class="cart_link"><a href="<?php e(pines_url('com_shop', 'cart/view')); ?>">See Cart</a></div>
		<br style="clear: both; height: 0;" />
		<?php } ?>
		<button type="button" class="empty_cart btn btn-danger" title="Empty Cart"><i class="fa fa-trash-o"></i></button>
		<button type="button" class="checkout btn btn-primary" onclick="$_.get(<?php e(json_encode(pines_url('com_shop', 'checkout/login'))); ?>);"><i class="fa fa-check"></i> Check-Out</button>
		<br style="clear: both; height: 0;" />
	</div>
	<div class="no_items"<?php echo $_->com_shop->cart() ? ' style="display: none;"' : ''; ?>>
		There are no items in your cart.
	</div>
</div>