<?php
/**
 * Main page of the Shop template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Templates\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
// Experimental AJAX code.
if ($_->config->tpl_shop->ajax && ($_REQUEST['tpl_shop_ajax'] == 1 && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
	$return = array(
		'notices' => $_->page->get_notice(),
		'errors' => $_->page->get_error(),
		'main_menu' => $_->page->render_modules('main_menu', 'module_head'),
		'pos_head' => $_->page->render_modules('head', 'module_head'),
		'pos_top' => $_->page->render_modules('top', 'module_header'),
		'pos_header' => $_->page->render_modules('header', 'module_header').'&nbsp;',
		'pos_header_right' => $_->page->render_modules('header_right', 'module_header_right'),
		'pos_pre_content' => $_->page->render_modules('pre_content', 'module_header'),
		'pos_breadcrumbs' => $_->page->render_modules('breadcrumbs'),
		'pos_content_top_left' => $_->page->render_modules('content_top_left'),
		'pos_content_top_right' => $_->page->render_modules('content_top_right'),
		'pos_content' => $_->page->render_modules('content', 'module_content'),
		'pos_content_bottom_left' => $_->page->render_modules('content_bottom_left'),
		'pos_content_bottom_right' => $_->page->render_modules('content_bottom_right'),
		'pos_post_content' => $_->page->render_modules('post_content', 'module_header'),
		'pos_left' => $_->page->render_modules('left', 'module_side'),
		'pos_right' => $_->page->render_modules('right', 'module_side'),
		'pos_footer' => $_->page->render_modules('footer', 'module_header'),
		'pos_bottom' => $_->page->render_modules('bottom', 'module_header')
	);
	echo json_encode($return);
	return;
}
header('Content-Type: text/html');
$_->com_bootstrap->load_js_css();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php e($_->page->get_title()); ?></title>
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?php e($_->config->location); ?>favicon.ico" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script type="text/javascript" src="<?php e($_->config->rela_location); ?>system/includes/js.php"></script>
	<script type="text/javascript">$_(function(){if ($.pnotify) {
		PNotify.prototype.options.opacity = .9;
		PNotify.prototype.options.delay = 15000;
	}});</script>
	<?php if ($_->config->tpl_shop->ajax) { ?>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/ajax.js"></script>
	<?php } ?>
	<!--[if lt IE 8]>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/jquery/jquery.dropdown.js"></script>
	<![endif]-->
	<?php echo $_->page->render_modules('head', 'module_head'); ?>
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/pines.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/print.css" media="print" rel="stylesheet" type="text/css" />
</head>
<body class="<?php echo in_array('printfix', $_->config->tpl_shop->fancy_style) ? ' printfix' : ''; echo in_array('printheader', $_->config->tpl_shop->fancy_style) ? ' printheader' : ''; echo in_array('nosidegutters', $_->config->tpl_shop->fancy_style) ? ' nosidegutters' : '';?>">
	<div id="top"><?php
		echo $_->page->render_modules('top', 'module_header');
		$error = $_->page->get_error();
		$notice = $_->page->get_notice();
		if ( $error || $notice ) { ?>
		<script type="text/javascript">
			$_(function(){
				<?php
				if ( $error ) { foreach ($error as $cur_item) {
					echo '$_.error('.json_encode(h($cur_item)).", \"Error\");\n";
				} }
				if ( $notice ) { foreach ($notice as $cur_item) {
					echo '$_.notice('.json_encode(h($cur_item)).", \"Notice\");\n";
				} }
				?>
			});
		</script>
		<?php
		}
	?></div>
	<nav id="nav" class="navbar navbar-static-top <?php echo $_->config->tpl_shop->alt_navbar ? 'navbar-inverse' : 'navbar-default'; ?>" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php e($_->config->full_location); ?>">
					<?php if ($_->config->tpl_shop->use_header_image) { ?>
					<img src="<?php e($_->config->tpl_shop->header_image); ?>" alt="<?php e($_->config->page_title); ?>" />
					<?php } else { ?>
						<span>
						<?php switch ($_->config->tpl_shop->brand_type) {
							case "System Name":
								e($_->config->system_name);
								break;
							case "Page Title":
								e($_->config->page_title);
								break;
							case "Custom":
								e($_->config->tpl_shop->brand_name);
								break;
							} ?>
						</span>
					<?php } ?>
				</a>
			</div>

			<div class="collapse navbar-collapse" id="navbar-collapse">
				<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
						<input style="width: 250px;" type="text" class="form-control" placeholder="Search shops and products">
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
				<div class="navbar-right" id="shop_thumbnail">
					<?php echo $_->page->render_modules('shop_thumbnail', 'module_head'); ?>
				</div>
				<?php echo $_->page->render_modules('main_menu', 'module_head'); ?>
			</div>
		</div>
	</nav>
	<div id="header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-12 positions">
					<div id="header_position"><?php echo $_->page->render_modules('header', 'module_header'); ?></div>
					<div id="header_right"><?php echo $_->page->render_modules('header_right', 'module_header_right'); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div id="page" class="container-fluid">
		<div class="row">
			<div id="breadcrumbs" class="col-sm-12"><?php echo $_->page->render_modules('breadcrumbs', 'module_header'); ?></div>
		</div>
		<div class="row">
			<div id="pre_content" class="col-sm-12"><?php echo $_->page->render_modules('pre_content', 'module_header'); ?></div>
		</div>
		<div id="column_container">
			<div class="row">
				<?php if (in_array($_->config->tpl_shop->variant, array('threecol', 'twocol-sideleft'))) { ?>
				<div id="left" class="col-sm-3">
					<?php echo $_->page->render_modules('left', 'module_side'); ?>
					<?php if ($_->config->tpl_shop->variant == 'twocol-sideleft') { echo $_->page->render_modules('right', 'module_side'); } ?>&nbsp;
				</div>
				<?php } ?>
				<div class="<?php echo $_->config->tpl_shop->variant == 'full-page' ? 'col-sm-12' : ($_->config->tpl_shop->variant == 'threecol' ? 'col-sm-6' : 'col-sm-9'); ?>">
					<div id="content_container">
						<div class="row">
							<div id="content_top_left" class="col-sm-6"><?php echo $_->page->render_modules('content_top_left'); ?></div>
							<div id="content_top_right" class="col-sm-6"><?php echo $_->page->render_modules('content_top_right'); ?></div>
						</div>
						<div id="content"><?php echo $_->page->render_modules('content', 'module_content'); ?></div>
						<div class="row">
							<div id="content_bottom_left" class="col-sm-6"><?php echo $_->page->render_modules('content_bottom_left'); ?></div>
							<div id="content_bottom_right" class="col-sm-6"><?php echo $_->page->render_modules('content_bottom_right'); ?></div>
						</div>
					</div>
				</div>
				<?php if (in_array($_->config->tpl_shop->variant, array('threecol', 'twocol-sideright'))) { ?>
				<div id="right" class="col-sm-3">
					<?php if ($_->config->tpl_shop->variant == 'twocol-sideright') { echo $_->page->render_modules('left', 'module_side'); } ?>
					<?php echo $_->page->render_modules('right', 'module_side'); ?>&nbsp;
				</div>
				<?php } ?>
			</div>
		</div>
		<?php if ($_->config->tpl_shop->variant == 'full-page') { ?>
		<div class="row">
			<div id="sidebar" class="col-sm-12">
				<?php echo $_->page->render_modules('left', 'module_header'); ?>
				<?php echo $_->page->render_modules('right', 'module_header'); ?>
			</div>
		</div>
		<?php } ?>
		<div class="row">
			<div id="post_content" class="col-sm-12"><?php echo $_->page->render_modules('post_content', 'module_header'); ?></div>
		</div>
	</div>
	<div id="footer" class="clearfix">
		<div class="container">
			<div class="row">
				<div class="col-sm-12 positions">
					<div id="footer_position"><?php echo $_->page->render_modules('footer', 'module_header'); ?></div>
					<p id="copyright"><?php e($_->config->copyright_notice, ENT_COMPAT, '', false); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom"><?php echo $_->page->render_modules('bottom', 'module_header'); ?></div>
</body>
</html>