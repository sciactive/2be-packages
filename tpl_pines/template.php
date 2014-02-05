<?php
/**
 * Main page of the WonderPHP template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Templates\pines
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
// Experimental AJAX code.
if ($_->config->tpl_pines->ajax && ($_REQUEST['tpl_pines_ajax'] == 1 && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
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
		'pos_content_top_left' => $_->page->render_modules('content_top_left', 'module_header'),
		'pos_content_top_right' => $_->page->render_modules('content_top_right', 'module_header'),
		'pos_content' => $_->page->render_modules('content', 'module_content'),
		'pos_content_bottom_left' => $_->page->render_modules('content_bottom_left', 'module_header'),
		'pos_content_bottom_right' => $_->page->render_modules('content_bottom_right', 'module_header'),
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
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php e($_->page->get_title()); ?></title>
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?php e($_->config->location); ?>favicon.ico" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/dropdown/dropdown.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/dropdown/dropdown.vertical.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/dropdown/themes/jqueryui/jqueryui.css" media="all" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="<?php e($_->config->rela_location); ?>system/includes/js.php"></script>
	<?php if ($_->config->tpl_pines->menu_delay) { ?>
	<script type="text/javascript">$_.tpl_pines_menu_delay = true;</script>
	<?php } ?>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/template.js"></script>
	<?php if ($_->config->tpl_pines->ajax) { ?>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/ajax.js"></script>
	<?php } ?>

	<!--[if lt IE 7]>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/jquery/jquery.dropdown.js"></script>
	<![endif]-->

	<?php echo $_->page->render_modules('head', 'module_head'); ?>
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/pines.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/print.css" media="print" rel="stylesheet" type="text/css" />
	<style type="text/css">.page-width {width: auto;<?php echo $_->config->template->width === 0 ? '' : ' max-width: '.(int) $_->config->template->width.'px;'; ?>}</style>
</head>
<body class="ui-widget ui-widget-content<?php echo in_array('shadows', $_->config->tpl_pines->fancy_style) ? ' shadows' : ''; echo in_array('printfix', $_->config->tpl_pines->fancy_style) ? ' printfix' : ''; echo in_array('printheader', $_->config->tpl_pines->fancy_style) ? ' printheader' : ''; echo in_array('nosidegutters', $_->config->tpl_pines->fancy_style) ? ' nosidegutters' : ''; ?>">
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
	<div id="header" class="ui-widget-header">
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<div class="span4">
					<div id="page_title">
						<a href="<?php e($_->config->full_location); ?>">
							<?php if ($_->config->tpl_pines->use_header_image) { ?>
							<img src="<?php e($_->config->tpl_pines->header_image); ?>" alt="<?php e($_->config->page_title); ?>" />
							<?php } else { ?>
							<span><?php e($_->config->page_title); ?></span>
							<?php } ?>
						</a>
					</div>
				</div>
				<div id="header_position" class="span4"><?php echo $_->page->render_modules('header', 'module_header'); ?>&nbsp;</div>
				<div id="header_right" class="span4"><?php echo $_->page->render_modules('header_right', 'module_header_right'); ?></div>
			</div>
		</div>
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<div class="span12">
					<div id="main_menu"<?php echo $_->config->tpl_pines->center_menu ? ' class="centered"' : ''; ?>><?php echo $_->page->render_modules('main_menu', 'module_head'); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid page-width centered">
		<div class="row-fluid">
			<div id="pre_content" class="span12"><?php echo $_->page->render_modules('pre_content', 'module_header'); ?></div>
		</div>
	</div>
	<div id="column_container">
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<?php if (in_array($_->config->tpl_pines->variant, array('threecol', 'twocol-sideleft'))) { ?>
				<div id="left" class="span2">
					<?php echo $_->page->render_modules('left', 'module_side'); ?>
					<?php if ($_->config->tpl_pines->variant == 'twocol-sideleft') { echo $_->page->render_modules('right', 'module_side'); } ?>&nbsp;
				</div>
				<?php } ?>
				<div class="<?php echo $_->config->tpl_pines->variant == 'full-page' ? 'span12' : ($_->config->tpl_pines->variant == 'threecol' ? 'span8' : 'span10'); ?>">
					<div id="content_container">
						<div id="breadcrumbs"><?php echo $_->page->render_modules('breadcrumbs', 'module_header'); ?></div>
						<div class="row-fluid">
							<div id="content_top_left" class="span6"><?php echo $_->page->render_modules('content_top_left', 'module_header'); ?></div>
							<div id="content_top_right" class="span6"><?php echo $_->page->render_modules('content_top_right', 'module_header'); ?></div>
						</div>
						<div id="content"><?php echo $_->page->render_modules('content', 'module_content'); ?></div>
						<div class="row-fluid">
							<div id="content_bottom_left" class="span6"><?php echo $_->page->render_modules('content_bottom_left', 'module_header'); ?></div>
							<div id="content_bottom_right" class="span6"><?php echo $_->page->render_modules('content_bottom_right', 'module_header'); ?></div>
						</div>
					</div>
				</div>
				<?php if (in_array($_->config->tpl_pines->variant, array('threecol', 'twocol-sideright'))) { ?>
				<div id="right" class="span2">
					<?php if ($_->config->tpl_pines->variant == 'twocol-sideright') { echo $_->page->render_modules('left', 'module_side'); } ?>
					<?php echo $_->page->render_modules('right', 'module_side'); ?>&nbsp;
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="container-fluid page-width centered">
		<div class="row-fluid">
			<div id="post_content" class="span12"><?php echo $_->page->render_modules('post_content', 'module_header'); ?></div>
		</div>
	</div>
	<div id="footer" class="ui-widget-header">
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<div class="span12">
					<div id="footer_position"><?php echo $_->page->render_modules('footer', 'module_header'); ?></div>
					<p id="copyright"><?php e($_->config->copyright_notice, ENT_COMPAT, '', false); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom"><?php echo $_->page->render_modules('bottom', 'module_header'); ?></div>
</body>
</html>