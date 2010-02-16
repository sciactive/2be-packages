<?php
/**
 * Template for a module.
 *
 * @package Pines
 * @subpackage tpl_print
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<div class="module<?php echo $this->class_suffix; ?>">
	<?php if ($this->show_title && (!empty($this->title) || !empty($this->note))) { ?>
	<div class="module_title ui-widget-header ui-corner-all">
		<?php if (!empty($this->title)) { ?>
			<h2><?php echo htmlentities($this->title); ?></h2>
		<?php } ?>
		<?php if (!empty($this->note)) { ?>
			<p><?php echo $this->note; ?></p>
		<?php } ?>
	</div>
	<?php } ?>
	<div class="module_content">
		<?php echo $this->content; ?>
	</div>
</div>