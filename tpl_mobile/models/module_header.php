<?php
/**
 * Template for a module.
 *
 * @package Templates\mobile
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
?>
<div class="module <?php e(implode(' ', $this->classes)); ?>">
	<?php if ($this->show_title && (!empty($this->title) || !empty($this->note))) { ?>
	<div class="module_title">
		<?php if (!empty($this->title)) { ?>
			<h2><?php echo $this->title; ?></h2>
		<?php } if (!empty($this->note)) { ?>
			<p><?php echo $this->note; ?></p>
		<?php } ?>
	</div>
	<?php } ?>
	<div class="module_content ui-helper-reset ui-helper-clearfix">
		<?php echo $this->content; ?>
	</div>
</div>