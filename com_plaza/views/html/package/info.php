<?php
/**
 * Displays package info.
 *
 * @package Components\plaza
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Package Info for '.h($this->package['package']);
?>
<div id="p_muid_info">
	<style type="text/css">
		#p_muid_info {
			padding: 1em 2em;
		}
		#p_muid_info .version {
			display: block;
			float: right;
			clear: right;
		}
		#p_muid_info .short_description {
			font-size: 1.1em;
		}
		#p_muid_info .icon {
			float: left;
			margin-right: .5em;
		}
		#p_muid_fancybox {
			padding: .4em;
			height: 120px;
			overflow: auto;
		}
		#p_muid_fancybox .screen_small {
			width: 100px;
			height: auto;
			max-height: 100px;
			vertical-align: top;
		}
	</style>
	<div class="pf-form">
		<div class="pf-element pf-heading">
			<?php if (!empty($this->package['icon'])) { ?>
			<img src="<?php e(pines_url('com_plaza', 'package/media', array('local' => 'false', 'name' => $this->package['package'], 'publisher' => $this->package['publisher'], 'media' => $this->package['icon']))); ?>" alt="Icon" class="icon" style="width: 32px; height: 32px;" />
			<?php } ?>
			<h3><span class="name"><?php e($this->package['name']); ?></span><span class="package" style="float: right;"><?php e($this->package['package']); ?></span></h3>
			<p style="clear: right;">
				<span>By <span class="author"><?php e($this->package['author']); ?></span></span>
				<span class="version">Version <span class="text"><?php e($this->package['version']); ?></span></span>
			</p>
		</div>
		<div class="pf-element pf-full-width short_description"><?php e($this->package['short_description']); ?></div>
		<?php if ($this->package['services']) { ?>
		<div class="pf-element services">
			<span class="pf-label">Provides Services</span>
			<span class="pf-field"><?php e(implode(', ', $this->package['services'])); ?></span>
		</div>
		<?php } ?>
		<div class="pf-element license">
			<span class="pf-label">License</span>
			<?php if (preg_match('/^https?:\/\//', $this->package['license'])) { ?>
			<span class="pf-field"><a href="<?php e($this->package['license']); ?>" target="_blank"><?php e($this->package['license']); ?></a></span>
			<?php } else { ?>
			<span class="pf-field"><?php e($this->package['license']); ?></span>
			<?php } ?>
		</div>
		<div class="pf-element license">
			<span class="pf-label">Website</span>
			<?php if (preg_match('/^https?:\/\//', $this->package['website'])) { ?>
			<span class="pf-field"><a href="<?php e($this->package['website']); ?>" target="_blank"><?php e($this->package['website']); ?></a></span>
			<?php } else { ?>
			<span class="pf-field"><?php e($this->package['website']); ?></span>
			<?php } ?>
		</div>
		<div class="pf-element description"><?php echo str_replace("\n", '<br />', h($this->package['description'])); ?></div>
		<?php if ($this->package['screens']) { if (isset($_->com_fancybox)) { ?>
		<div class="pf-element pf-full-width screenshots">
			<div id="p_muid_fancybox" class="ui-widget-content ui-corner-all">
				<?php foreach ($this->package['screens'] as $cur_screen) { ?>
				<a rel="p_muid_ss" title="<?php e($cur_screen['alt']); ?>" href="<?php e($_->com_plaza->package_get_media($this->package, $cur_screen['file'], true)); ?>">
					<img class="screen_small" alt="<?php e($cur_screen['alt']); ?>" src="<?php e($_->com_plaza->package_get_media($this->package, $cur_screen['file'], true)); ?>" />
				</a>
				<?php } ?>
			</div>
		</div>
		<script type="text/javascript">
			pines(function(){
				$("#p_muid_fancybox > a").fancybox({titleShow: true, titlePosition: "inside"});
			});
		</script>
		<?php } else { ?>
		<div class="pf-element screenshots">
			<span class="pf-label">Screenshots</span>
			<span class="pf-note">Install com_fancybox for a fancier screenshot experience.</span>
			<?php foreach ($this->package['screens'] as $cur_screen) { ?>
			<div class="pf-group">
				<div class="pf-field"><a href="<?php e(pines_url('com_plaza', 'package/media', array('local' => 'false', 'name' => $this->package['package'], 'publisher' => $this->package['publisher'], 'media' => $cur_screen['file']))); ?>" target="_blank"><?php e($cur_screen['alt']); ?></a></div>
			</div>
			<?php } ?>
		</div>
		<?php } } if ($this->package['depend']) { ?>
		<div class="pf-element">
			<a href="javascript:void(0);" onclick="$(this).nextAll('div').slideToggle();">See What This Package Depends On</a>
			<br />
			<div class="depend" style="display: none; padding-left: 10px;">
				<?php foreach ($this->package['depend'] as $key => $value) { ?>
				<span class="pf-label"><?php e($key); ?></span><div class="pf-group"><div class="pf-field"><?php e($value); ?></div></div>
				<?php } ?>
			</div>
		</div>
		<?php } if ($this->package['conflict']) { ?>
		<div class="pf-element">
			<a href="javascript:void(0);" onclick="$(this).nextAll('div').slideToggle();">See What This Package Conflicts With</a>
			<br />
			<div class="conflict" style="display: none; padding-left: 10px;">
				<?php foreach ($this->package['conflict'] as $key => $value) { ?>
				<span class="pf-label"><?php e($key); ?></span><div class="pf-group"><div class="pf-field"><?php e($value); ?></div></div>
				<?php } ?>
			</div>
		</div>
		<?php } if ($this->package['recommend']) { ?>
		<div class="pf-element">
			<a href="javascript:void(0);" onclick="$(this).nextAll('div').slideToggle();">See What This Package Recommends</a>
			<br />
			<div class="recommend" style="display: none; padding-left: 10px;">
				<?php foreach ($this->package['recommend'] as $key => $value) { ?>
				<span class="pf-label"><?php e($key); ?></span><div class="pf-group"><div class="pf-field"><?php e($value); ?></div></div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
	<br />
</div>