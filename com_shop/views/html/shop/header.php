<?php
/**
 * View a shop's header.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$can_edit = false;
if (isset($_SESSION['user']) && $_SESSION['user']->is($this->entity->user)) {
	// This shop is owned by the user.
	$can_edit = true;
} elseif ($_->user_manager->check_permissions($entity, 2)) {
	// User has permission to view.
	$can_edit = true;
}

$is_starred = false;
if (isset($_SESSION['user']) && isset($_SESSION['user']->com_shop_starred) && $this->entity->inArray($_SESSION['user']->com_shop_starred)) {
	$is_starred = true;
}

?>
<style type="text/css">
	#p_muid_header {
		background: url(<?php e($_->config->location.$this->entity->header); ?>) no-repeat center center;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
		padding-bottom: 25%;
		position: relative;
		margin-bottom: <?php e($_->config->com_sales->product_thumbnail_height / 2); ?>px;
	}
	#p_muid_header .shop_title {
		position: absolute;
		bottom: -<?php e($_->config->com_sales->product_thumbnail_height / 2 + 8); ?>px;
		left: 25px;
		font-size: <?php e($_->config->com_sales->product_thumbnail_height / 2 - 4); ?>px;
		line-heigt: <?php e($_->config->com_sales->product_thumbnail_height / 2 - 4); ?>px;
	}
	#p_muid_header .shop_title img {
		vertical-align: bottom;
		padding: 4px;
		border: 1px #AAA solid;
		border-radius: 4px;
		background-color: #fff;
	}
	#p_muid_header .shop_actions {
		position: absolute;
		bottom: 10px;
		right: 25px;
	}
	#p_muid_header .shop_menu {
		position: absolute;
		bottom: -<?php e($_->config->com_sales->product_thumbnail_height / 2 + 8); ?>px;
		right: 25px;
	}
	#p_muid_header .shop_menu a {
		font-size: <?php e($_->config->com_sales->product_thumbnail_height / 4 - 2); ?>px;
		line-heigt: <?php e($_->config->com_sales->product_thumbnail_height / 2 - 4); ?>px;
	}
	@media (max-width: 768px){
		#p_muid_header {
			margin-bottom: 0;
		}
		#p_muid_header .shop_menu {
			position: relative;
			left: 0;
			right: 0;
			bottom: 0;
			top: 100%;
			float: left;
			width: 100%;
			padding: 0 25px;
			margin-top: 25%;
			padding-top: <?php e($_->config->com_sales->product_thumbnail_height / 2 + 8); ?>px;
		}
	}
</style>
<script type="text/javascript">
	$_(function(){
		$("#p_muid_star").click(function(){
			$.ajax({
				url: <?php echo json_encode(pines_url('com_shop', 'shop/star', array('id' => $this->entity->guid))); ?>,
				type: "POST",
				dataType: "json",
				beforeSend: function(){
					$("#p_muid_star").prop("disabled", true).addClass("disabled");
				},
				complete: function(){
					$("#p_muid_star").prop("disabled", false).removeClass("disabled");
				},
				error: function(){
					$_.error("Error occurred trying to star this shop.");
				},
				success: function(data){
					if (!data && data == null) {
						$_.error("Error occurred trying to star this shop.");
						return;
					}
					if (data.starred) {
						$("#p_muid_star").html('<i class="fa fa-star"></i> Starred');
					} else {
						$("#p_muid_star").html('<i class="fa fa-star-o"></i> Star');
					}
				}
			});
		});
	});
</script>
<div id="p_muid_header" class="shop_header">
	<div class="shop_title">
		<h1><a href="<?php e(pines_url('com_shop', null, array('id' => $this->entity->guid))); ?>"><img alt="Shop Thumbnail" src="<?php e($_->config->location.$this->entity->thumbnail); ?>" /></a> <?php e($this->entity->name); ?></h1>
	</div>
	<div class="shop_actions">
		<?php if ($can_edit) { ?>
		<a href="<?php e(pines_url('com_shop', 'shop/edit', array('id' => $this->entity->guid))); ?>" class="btn btn-default">Edit</a>
		<?php } if (gatekeeper()) { ?>
		<a id="p_muid_star" href="javascript:void(0);" class="btn btn-default"><i class="fa <?php echo ($is_starred ? 'fa-star' : 'fa-star-o'); ?>"></i> Star<?php echo ($is_starred ? 'red' : ''); ?></a>
		<?php } else { ?>
		<a href="<?php e(pines_url('com_user', 'login', array('url' => pines_url('com_shop', 'shop', array('id' => $this->entity->guid))))); ?>" class="btn btn-default"><i class="fa fa-star-o"></i> Star</a>
		<?php } ?>
	</div>
	<div class="shop_menu">
		<a class="btn btn-link">Specials</a>
		<a class="btn btn-link">Products</a>
		<a class="btn btn-link">About</a>
		<a class="btn btn-link">Policies</a>
	</div>
</div>