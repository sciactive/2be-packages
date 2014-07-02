<?php
/**
 * Show the selected shop.
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
	#p_muid_thumbnail {
		width: 16px;
		height: 16px;
		border: 1px #AAA solid;
	}
	#p_muid_box {
		display: table;
		height: 100%;
	}
	#p_muid_box div {
		display: table-cell;
		padding: 4px;
		vertical-align: middle;
		text-align: center;
	}
</style>
<div id="p_muid_box">
	<div>
		<?php if (isset($_SESSION['shop'])) { ?>
		<img alt="Current Shop: <?php e($_SESSION['shop']->name); ?>" title="Current Shop: <?php e($_SESSION['shop']->name); ?>" id="p_muid_thumbnail" src="<?php e($_->config->location.$_SESSION['shop']->thumbnail); ?>" />
		<?php } else { ?>
		<span id="p_muid_thumbnail" class="label label-primary">No Shop</span>
		<?php } ?>
	</div>
</div>