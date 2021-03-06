<?php
/**
 * Main page of the 2be CMS template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Templates\pinescms
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
header('Content-Type: text/html');

if (substr($_->config->tpl_pinescms->variant, -4) === 'left')
	$sidebar = 'left';
elseif (substr($_->config->tpl_pinescms->variant, -5) === 'right')
	$sidebar = 'right';
elseif (substr($_->config->tpl_pinescms->variant, -6) === 'noside')
	$sidebar = 'none';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php e($_->page->get_title()); ?></title>
	<meta name="HandheldFriendly" content="true" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?php e($_->config->location); ?>favicon.ico" />
	<link href='http://fonts.googleapis.com/css?family=Crimson+Text' rel='stylesheet' type='text/css'>
	<link href="<?php e($_->config->location); ?>templates/tpl_pinescms/css/dropdown/dropdown.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/tpl_pinescms/css/dropdown/dropdown.vertical.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/tpl_pinescms/css/dropdown/default.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/tpl_pinescms/css/dropdown/default.pines.css" media="all" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php e($_->config->rela_location); ?>system/includes/js.php"></script>
	<script type="text/javascript">$_(function(){if($.pnotify){
		PNotify.prototype.options.opacity = .9;
		PNotify.prototype.options.delay = 15000;
		$_.pnotify_notice_defaults.nonblock.nonblock = false;
		$_.pnotify_alert_defaults.nonblock.nonblock = false;
	}});</script>
	<?php echo $_->page->render_modules('head', 'module_head'); ?>
	<link href="<?php e($_->config->location); ?>templates/tpl_pinescms/css/style.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body class="scheme-<?php e($_->config->tpl_pinescms->color_scheme); ?>">
	<div id="top"><?php
		echo $_->page->render_modules('top');
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
	<div id="nav" class="navbar navbar-default navbar-fixed-top <?php echo ($_->config->tpl_pinescms->navigation_fixed) ? '': 'nav_not_fixed' ;?>">
		<div class="container">
			<?php if ($_->config->tpl_pinescms->use_nav_logo) { ?>
			<a href="<?php e(pines_url()); ?>"><img id="nav-logo" class="<?php e($_->config->tpl_pinescms->navigation_orientation) ?>" src="<?php e($_->config->tpl_pinescms->nav_logo_image); ?>" alt="<?php e($_->config->page_title); ?>"/></a>
			<?php } ?>
			<div class="navbar-right">
				<?php echo $_->page->render_modules('main_menu', 'module_head'); ?>
			</div>
		</div>
	</div>
	<div id="shadow_container" class="container <?php echo ($_->config->tpl_pinescms->navigation_fixed) ? 'fixed_nav': '' ;?>">
		<div id="shadow_box">
			<?php if ($_->config->tpl_pinescms->display_header) { ?>
			<div id="pines_header" class="clearfix">
				<a id="logo" href="<?php e(pines_url()); ?>">
					<?php if ($_->config->tpl_pinescms->use_header_image) { ?>
					<img src="<?php e($_->config->tpl_pinescms->header_image); ?>" alt="<?php e($_->config->page_title); ?>" />
					<?php } else { ?>
					<span><?php e($_->config->page_title); ?></span>
					<?php } ?>
				</a>
				<div id="header_search"><?php echo $_->page->render_modules('search', 'module_head'); ?></div>
				<div id="header"><?php echo $_->page->render_modules('header'); ?></div>
				<div id="header-right"><?php echo $_->page->render_modules('header_right'); ?></div>
			</div>
			<?php } ?>
			<div id="pines_pre_content"><?php echo $_->page->render_modules('pre_content'); ?></div>
			<div id="breadcrumbs"><?php echo $_->page->render_modules('breadcrumbs', 'module_simple'); ?></div>
			<div id="pines_content" class="container">
				<div class="modules">
					<?php if ($sidebar) { if ($sidebar == 'left') { ?>
					<div class="row">
						<div id="sidebar" class="col-sm-3">
							<?php echo $_->page->render_modules('left'); echo $_->page->render_modules('right'); ?>
						</div>
						<div id="main_content" class="col-sm-9">
							<?php echo $_->page->render_modules('content'); ?>
						</div>
					</div>
					<?php } elseif ($sidebar == 'right') { ?>
					<div class="row">
						<div id="main_content" class="col-sm-9">
							<?php echo $_->page->render_modules('content'); ?>
						</div>
						<div id="sidebar" class="col-sm-3">
							<?php echo $_->page->render_modules('left'); echo $_->page->render_modules('right'); ?>
						</div>
					</div>
					<?php } elseif ($sidebar == 'none') { ?>
					<div class="row">
						<div id="main_content" class="col-sm-12">
							<?php echo $_->page->render_modules('content'); ?>
						</div>
					</div>
					<?php } } else {
						echo $_->page->render_modules('content');
					} ?>
				</div>
			</div>
			<div id="pines_post_content"><?php echo $_->page->render_modules('post_content'); ?></div>
			<div id="pines_footer_shadow"></div>
			<div id="pines_footer"><?php echo $_->page->render_modules('footer'); ?></div>
			<div id="pines_copyright">
				<?php if ($_->config->tpl_pinescms->show_recycled_bits) { ?>
				<div id="recycled_bits"></div>
				<?php } ?>
				<p><?php e($_->config->copyright_notice, ENT_COMPAT, '', false); ?></p>
			</div>
		</div>
	</div>
	<div id="copyright-line-container" class="container">
		<div id="copyright-line-edges"><div id="copyright-line">&nbsp;</div></div>
	</div>
	<div id="bottom"><?php echo $_->page->render_modules('bottom'); ?></div>
</body>
</html>