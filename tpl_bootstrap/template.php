<?php
/**
 * Main page of the Bootstrap template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Templates\bootstrap
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');
// Experimental AJAX code.
if ($_->config->tpl_bootstrap->ajax && ($_REQUEST['tpl_bootstrap_ajax'] == 1 && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
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
$width = ($_->config->template->width == 'fluid') ? '-fluid' : '';

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
	<script type="text/javascript">pines(function(){if ($.pnotify) {
		$.pnotify.defaults.opacity = .9;
		$.pnotify.defaults.delay = 15000;
	}});</script>
	<?php if ($_->config->tpl_bootstrap->ajax) { ?>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/ajax.js"></script>
	<?php } ?>
	<?php if ($_->template->verify_color($_->config->tpl_bootstrap->lighter_color) && $_->template->verify_color($_->config->tpl_bootstrap->darker_color) && $_->template->verify_color($_->config->tpl_bootstrap->border_color)) { ?>
	<!--[if lt IE 8]>
	<style type="text/css">
	.navbar-inner {
		filter: progid:DXImageTransform.Microsoft.gradient(enabled = false) !important;
	}
	</style>
	<![endif]-->
	<?php } ?>
	<!--[if lt IE 8]>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/jquery/jquery.dropdown.js"></script>
	<![endif]-->
	<?php if (!empty($_->config->tpl_bootstrap->custom_ie_code)) {
		echo $_->config->tpl_bootstrap->custom_ie_code;
	}  echo $_->page->render_modules('head', 'module_head'); ?>
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/pines.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/print.css" media="print" rel="stylesheet" type="text/css" />
	<?php if (!empty($_->config->tpl_bootstrap->font_folder)) { ?>
	<link href="<?php e($_->config->tpl_bootstrap->font_folder); ?>stylesheet.css" media="all" rel="stylesheet" type="text/css" />
	<?php } 
	if (!empty($_->config->tpl_bootstrap->link_css)) {
		$link_css = explode(',', $_->config->tpl_bootstrap->link_css);
		foreach ($link_css as $cur_link) {
			echo '<link type="text/css" rel="stylesheet" href="'.h($_->config->location).$cur_link.'" />';
		}
	}
	?>
	<?php  
	// The following creates strings of classes to be added into certain elements based on configuration options.
	// Body Configuration 
	if (!empty($_->config->tpl_bootstrap->body_fontface)) {
		// body font face has been set
		if (empty($body))
			$body = "body-font";
		else
			$body .= " body-font";
	}

	if (!empty($_->config->tpl_bootstrap->body_css)) {
		// body custom css has been set
		if (empty($body))
			$body = "body-custom";
		else
			$body .= " body-custom";
	}
	// Navigation Bar Configuration
	if ($_->config->tpl_bootstrap->mobile_menu == "adjusted") { 
		// The mobile menu option is chosen
		if (empty($nav))
			$nav = "adjusted";
		else
			$nav .= " adjusted";
	} 
	if ($_->template->verify_color($_->config->tpl_bootstrap->lighter_color) && $_->template->verify_color($_->config->tpl_bootstrap->darker_color) && $_->template->verify_color($_->config->tpl_bootstrap->border_color)) {
		// all the required fields for changing the nav bar colors have been filled out
		if (empty($nav))
			$nav = "bar-colors";
		else
			$nav .= " bar-colors";
	}
	if ($_->template->verify_color($_->config->tpl_bootstrap->caret_color) && $_->template->verify_color($_->config->tpl_bootstrap->caret_hover_color)) {
		// caret color has been set
		if (empty($nav))
			$nav = "caret-color";
		else
			$nav .= " caret-color";
	}
	if ($_->template->verify_color($_->config->tpl_bootstrap->brand_color) && $_->template->verify_color($_->config->tpl_bootstrap->brand_hover_color)) {
		// brand color has been set
		if (empty($nav))
			$nav = "brand-color";
		else
			$nav .= " brand-color";
	}
	if ($_->template->verify_color($_->config->tpl_bootstrap->font_color) && $_->template->verify_color($_->config->tpl_bootstrap->font_hover_color)) {
		// font color has been set
		if (empty($nav))
			$nav = "font-color";
		else
			$nav .= " font-color";
	}
	if (!empty($_->config->tpl_bootstrap->brand_fontface)) {
		// brand font face has been set
		if (empty($nav))
			$nav = "brand-font";
		else
			$nav .= " brand-font";
	}
	if ($_->config->tpl_bootstrap->navbar_menu_height > 0) {
		// Menu Height has been set
		if (empty($nav))
			$nav = "menu-height";
		else
			$nav .= " menu-height";
	}
	if (!empty($_->config->tpl_bootstrap->menu_fontface)) {
		// Menu Font face has been set
		if (empty($nav))
			$nav = "menu-font";
		else
			$nav .= " menu-font";
	}
	if (!empty($_->config->tpl_bootstrap->menu_css)) {
		// Custom Menu CSS 
		if (empty($nav))
			$nav = "menu-custom";
		else
			$nav .= " menu-custom";
	}
	if (!empty($_->config->tpl_bootstrap->brand_css)) {
		// Custom Brand CSS 
		if (empty($nav))
			$nav = "brand-custom";
		else
			$nav .= " brand-custom";
	}
	if (!empty($_->config->tpl_bootstrap->nav_list_css)) {
		// Custom Navbar CSS 
		if (empty($nav))
			$nav = "nav-list-custom";
		else
			$nav .= " nav-list-custom";
	}
	if (!empty($_->config->tpl_bootstrap->nav_bar_css)) {
		// Custom Navbar CSS 
		if (empty($nav))
			$nav = "nav-bar-custom";
		else
			$nav .= " nav-bar-custom";
	}

	// Footer Configuration Options
	if ($_->template->verify_color($_->config->tpl_bootstrap->footer_background) && $_->template->verify_color($_->config->tpl_bootstrap->footer_border)) {
		// footer background and footer border colors
		if (empty($footer))
			$footer = "bg-color";
		else
			$footer .= " bg-color";
	}
	if ($_->template->verify_color($_->config->tpl_bootstrap->footer_font_color)) {
		// footer font color
		if (empty($footer))
			$footer = "font-color";
		else
			$footer .= " font-color";
	}
	if (!empty($_->config->tpl_bootstrap->footer_css)) {
		// footer fixed
		if (empty($footer))
			$footer = "footer-custom";
		else
			$footer .= " footer-custom";
	}
	if ($_->config->tpl_bootstrap->footer_type == "fixed") {
		// footer fixed
		if (empty($footer))
			$footer = "footer-fixed";
		else
			$footer .= " footer-fixed";
	}// The page variable below still affects the footer so I put it in here.
	if ($_->config->tpl_bootstrap->footer_type == "fixed") {
		// footer fixed
		if (empty($page))
			$page = "footer-fixed";
		else
			$page .= " footer-fixed";
	} ?>
</head>
<body class="<?php echo $body; echo in_array('printfix', $_->config->tpl_bootstrap->fancy_style) ? ' printfix' : ''; echo in_array('printheader', $_->config->tpl_bootstrap->fancy_style) ? ' printheader' : ''; echo in_array('nosidegutters', $_->config->tpl_bootstrap->fancy_style) ? ' nosidegutters' : '';?>">
	<div id="top"><?php
		echo $_->page->render_modules('top', 'module_header');
		$error = $_->page->get_error();
		$notice = $_->page->get_notice();
		if ( $error || $notice ) { ?>
		<script type="text/javascript">
			pines(function(){
				<?php
				if ( $error ) { foreach ($error as $cur_item) {
					echo 'pines.error('.json_encode(h($cur_item)).", \"Error\");\n";
				} }
				if ( $notice ) { foreach ($notice as $cur_item) {
					echo 'pines.notice('.json_encode(h($cur_item)).", \"Notice\");\n";
				} }
				?>
			});
		</script>
		<?php
		}
	?></div>
	<div id="nav-configure" class="<?php echo $nav; ?>">
		<div id="nav" class="navbar clearfix <?php echo $_->config->tpl_bootstrap->navbar_fixed ? 'navbar-fixed-top' : ''; ?> <?php echo $_->config->tpl_bootstrap->alt_navbar ? 'navbar-inverse' : ''; ?>">
			<div class="navbar-inner">
				<div class="container<?php echo $width; ?>">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="<?php e($_->config->full_location); ?>">
						<?php if ($_->config->tpl_bootstrap->use_header_image) { ?>
						<img src="<?php e($_->config->tpl_bootstrap->header_image); ?>" alt="<?php e($_->config->page_title); ?>" />
						<?php } else { ?>
							<span>
						<?php	switch ($_->config->tpl_bootstrap->brand_type) {
								case "System Name":
									e($_->config->system_name);
									break;
								case "Page Title":
									e($_->config->page_title);
									break;
								case "Custom":
									e($_->config->tpl_bootstrap->brand_name);
									break;
								} ?>
							</span>	
					<?php } ?>
					</a>
					<div class="nav-collapse">
						<?php echo $_->page->render_modules('main_menu', 'module_head'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="header">
		<div class="container<?php echo $width; ?>">
			<div class="row-fluid">
				<div class="span12 positions">
					<div id="header_position"><?php echo $_->page->render_modules('header', 'module_header'); ?></div>
					<div id="header_right"><?php echo $_->page->render_modules('header_right', 'module_header_right'); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div id="page" class="container<?php echo $width; ?> <?php echo $page; ?>">
		<div class="row-fluid">
			<div id="breadcrumbs" class="span12"><?php echo $_->page->render_modules('breadcrumbs', 'module_header'); ?></div>
		</div>
		<div class="row-fluid">
			<div id="pre_content" class="span12"><?php echo $_->page->render_modules('pre_content', 'module_header'); ?></div>
		</div>
		<div id="column_container">
			<div class="row-fluid">
				<?php if (in_array($_->config->tpl_bootstrap->variant, array('threecol', 'twocol-sideleft'))) { ?>
				<div id="left" class="span3">
					<?php echo $_->page->render_modules('left', 'module_side'); ?>
					<?php if ($_->config->tpl_bootstrap->variant == 'twocol-sideleft') { echo $_->page->render_modules('right', 'module_side'); } ?>&nbsp;
				</div>
				<?php } ?>
				<div class="<?php echo $_->config->tpl_bootstrap->variant == 'full-page' ? 'span12' : ($_->config->tpl_bootstrap->variant == 'threecol' ? 'span6' : 'span9'); ?>">
					<div id="content_container">
						<div class="row-fluid">
							<div id="content_top_left" class="span6"><?php echo $_->page->render_modules('content_top_left'); ?></div>
							<div id="content_top_right" class="span6"><?php echo $_->page->render_modules('content_top_right'); ?></div>
						</div>
						<div id="content"><?php echo $_->page->render_modules('content', 'module_content'); ?></div>
						<div class="row-fluid">
							<div id="content_bottom_left" class="span6"><?php echo $_->page->render_modules('content_bottom_left'); ?></div>
							<div id="content_bottom_right" class="span6"><?php echo $_->page->render_modules('content_bottom_right'); ?></div>
						</div>
					</div>
				</div>
				<?php if (in_array($_->config->tpl_bootstrap->variant, array('threecol', 'twocol-sideright'))) { ?>
				<div id="right" class="span3">
					<?php if ($_->config->tpl_bootstrap->variant == 'twocol-sideright') { echo $_->page->render_modules('left', 'module_side'); } ?>
					<?php echo $_->page->render_modules('right', 'module_side'); ?>&nbsp;
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="row-fluid">
			<div id="post_content" class="span12"><?php echo $_->page->render_modules('post_content', 'module_header'); ?></div>
		</div>
	</div>
	<div id="footer" class="clearfix <?php echo $footer;?>">
		<div class="container<?php echo $width; ?>">
			<div class="row-fluid">
				<div class="span12 positions">
					<div id="footer_position"><?php echo $_->page->render_modules('footer', 'module_header'); ?></div>
					<p id="copyright"><?php e($_->config->copyright_notice, ENT_COMPAT, '', false); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom"><?php echo $_->page->render_modules('bottom', 'module_header'); ?></div>
</body>
</html>