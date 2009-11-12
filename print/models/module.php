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
	<?php if ($this->show_title && !empty($this->title)) { ?>
	<div class="module_title"><?php echo $this->title; ?></div>
	<?php } ?>
	<?php echo $this->content; ?>
</div>