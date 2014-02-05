<?php
/**
 * Main page of the Mobile WonderPHP template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Templates\mobile
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
header('Content-Type: text/html');

$menu = $_->page->render_modules('main_menu', 'module_head');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php e($_->page->get_title()); ?></title>
	<meta name="HandheldFriendly" content="true" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?php e($_->config->location); ?>favicon.ico" />
	<script type="text/javascript" src="<?php e($_->config->rela_location); ?>system/includes/js.php"></script>
	<script type="text/javascript" src="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/js/template.js"></script>
	<?php echo $_->page->render_modules('head', 'module_head'); ?>
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/pines.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="navbar" class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<?php if (!empty($menu)) { ?>
			<button id="menu_link" type="button" class="btn btn-navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<?php } if ($_->config->tpl_mobile->use_header_image) { ?>
			<a class="brand brand-image" href="<?php e(pines_url()); ?>">
				<img src="<?php e($_->config->tpl_mobile->header_image); ?>" alt="<?php e($_->config->page_title); ?>" />
			</a>
			<?php } else { ?>
			<a class="brand" href="<?php e(pines_url()); ?>">
				<?php e($_->config->page_title); ?>
			</a>
			<?php } ?>
		</div>
	</div>
</div>
<div id="wrapper">
<div id="page">
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
	<?php $header_content = $_->page->render_modules('header', 'module_header').$_->page->render_modules('header_right', 'module_header');
	if ($header_content) { ?>
	<div id="header" class="well">
		<?php echo $header_content; ?>
	</div>
	<?php } ?>
	<div id="pre_content"><?php echo $_->page->render_modules('pre_content', 'module_header'); ?></div>
	<div id="breadcrumbs"><?php echo $_->page->render_modules('breadcrumbs', 'module_header'); ?></div>
	<div id="content_top_left"><?php echo $_->page->render_modules('content_top_left'); ?></div>
	<div id="content_top_right"><?php echo $_->page->render_modules('content_top_right'); ?></div>
	<div id="content"><?php echo $_->page->render_modules('content'); ?></div>
	<div id="content_bottom_left"><?php echo $_->page->render_modules('content_bottom_left'); ?></div>
	<div id="content_bottom_right"><?php echo $_->page->render_modules('content_bottom_right'); ?></div>
	<?php if (in_array($_->config->tpl_mobile->variant, array('full', 'left'))) { ?>
	<div id="left"><?php echo $_->page->render_modules('left'); ?></div>
	<?php } if (in_array($_->config->tpl_mobile->variant, array('full', 'right'))) { ?>
	<div id="right"><?php echo $_->page->render_modules('right'); ?></div>
	<?php } ?>
	<div id="post_content"><?php echo $_->page->render_modules('post_content', 'module_header'); ?></div>
	<div id="footer" class="well">
		<div class="modules"><?php echo $_->page->render_modules('footer', 'module_header'); ?></div>
		<p id="copyright"><?php e($_->config->copyright_notice, ENT_COMPAT, '', false); ?></p>
	</div>
	<div id="bottom"><?php echo $_->page->render_modules('bottom', 'module_header'); ?></div>
</div>
<?php if (!empty($menu)) { ?>
<div id="menu">
	<?php echo $menu; ?>
</div>
<?php } ?>
</div>
</body>
</html>