<?php
/**
 * Main page of the Print template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Templates\print
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php e($_->page->get_title()); ?></title>
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?php e($_->config->location); ?>favicon.ico" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/style.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/dropdown/dropdown.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/dropdown/dropdown.vertical.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php e($_->config->location); ?>templates/<?php e($_->current_template); ?>/css/dropdown/themes/jqueryui/jqueryui.css" media="all" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php e($_->config->rela_location); ?>system/includes/js.php"></script>
	<?php echo $_->page->render_modules('head', 'module_head'); ?>
</head>
<body>
	<?php if ( count($_->page->get_error()) ) { ?>
	<div class="notice ui-state-error ui-corner-all ui-helper-clearfix">
		<?php
		$error = $_->page->get_error();
		foreach ($error as $cur_item)
			echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: 0.3em;\"></span><span>".h($cur_item)."</span></p>\n";
		?>
	</div>
	<?php } if ( count($_->page->get_notice()) ) { ?>
	<div class="notice ui-state-highlight ui-corner-all ui-helper-clearfix">
		<?php
		$notice = $_->page->get_notice();
		foreach ($notice as $cur_item)
			echo "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: 0.3em;\"></span><span>".h($cur_item)."</span></p>\n";
		?>
	</div>
	<?php }
	echo $_->page->render_modules('content'); ?>
</body>
</html>