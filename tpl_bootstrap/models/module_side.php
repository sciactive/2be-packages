<?php
/**
 * Template for a module.
 *
 * @package Templates\bootstrap
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');
?>
<div class="module <?php e(implode(' ', $this->classes)); ?>">
	<?php if ($this->show_title && (!empty($this->title) || !empty($this->note))) { ?>
	<div class="module_title page-header">
		<?php if (!empty($this->title)) { ?>
		<h3><?php echo $this->title;
		if (!empty($this->note)) { ?>
			<small><?php echo $this->note; ?></small>
		<?php } ?></h3>
		<?php } ?>
	</div>
	<?php } ?>
	<div class="module_content">
		<?php echo $this->content; ?>
	</div>
</div>