<?php
/**
 * A view to build a Nivo Slider module.
 *
 * @package Components\nivoslider
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_nivoslider->check_files && !$_->uploader->check($this->file)) {
	echo 'Unsafe file detected.';
	return;
}
$_->com_nivoslider->load();

$images = trim(file_get_contents($_->uploader->real($this->file)));
$dir = ($this->absolute_path == 'true') ? $_->config->location : rtrim(dirname($this->file), '/') . '/';
$images = explode("\n", $images);

$options = (object) array();

if (isset($this->effect))
	$options->effect = (string) $this->effect;
if (isset($this->slices))
	$options->slices = (int) $this->slices;
if (isset($this->animSpeed))
	$options->animSpeed = (int) $this->animSpeed;
if (isset($this->pauseTime))
	$options->pauseTime = (int) $this->pauseTime;
if (isset($this->startSlide))
	$options->startSlide = (int) $this->startSlide;
if (isset($this->directionNav))
	$options->directionNav = ($this->directionNav == 'true');
if (isset($this->directionNavHide))
	$options->directionNavHide = ($this->directionNavHide == 'true');
if (isset($this->controlNav))
	$options->controlNav = ($this->controlNav == 'true');
if (isset($this->controlNavThumbs))
	$options->controlNavThumbs = ($this->controlNavThumbs == 'true');
if (isset($this->controlNavThumbsFromRel))
	$options->controlNavThumbsFromRel = ($this->controlNavThumbsFromRel == 'true');
if (isset($this->controlNavThumbsSearch))
	$options->controlNavThumbsSearch = (string) $this->controlNavThumbsSearch;
if (isset($this->controlNavThumbsReplace))
	$options->controlNavThumbsReplace = (string) $this->controlNavThumbsReplace;
if (isset($this->keyboardNav))
	$options->keyboardNav = ($this->keyboardNav == 'true');
if (isset($this->pauseOnHover))
	$options->pauseOnHover = ($this->pauseOnHover == 'true');
if (isset($this->manualAdvance))
	$options->manualAdvance = ($this->manualAdvance == 'true');
if (isset($this->captionOpacity))
	$options->captionOpacity = (float) $this->captionOpacity;

if (empty($this->theme))
	$this->theme = 'none';

?>
<div class="slider-wrapper theme-<?php e($this->theme); ?>">
	<div class="ribbon"></div>
	<script type="text/javascript">
		pines.loadcss("<?php e($_->config->location); ?>components/com_nivoslider/includes/themes/<?php e(clean_filename($this->theme)); ?>/<?php e(clean_filename($this->theme)); ?>.css");
		pines(function(){
			$('#p_muid_slider').nivoSlider(<?php echo json_encode($options); ?>);
		});
	</script>
	<div id="p_muid_slider" class="nivoSlider <?php e($this->class); ?>" style="<?php e((isset($this->width) ? "width: $this->width; " : '').(isset($this->height) ? "height: $this->height;" : '')); ?>">
		<?php
		$captions = array();
		foreach ($images as $cur_image) {
			if (empty($cur_image))
				continue;
			$image_html = '';
			$parts = explode('|', $cur_image, 4);
			$image_html .= '<img alt="" src="'.h($dir.$parts[0]);
			if (!empty($parts[1])) {
				if (!$_->config->com_nivoslider->allow_html_captions || strpos($parts[1], '<') === false)
					$image_html .= '" title="'.h($parts[1]);
				else {
					$unique = uniqid('p_muid_');
					$captions[$unique] = $parts[1];
					$image_html .= '" title="#'.h($unique);
				}
			}
			$image_html .= '" />';
			if (!empty($parts[2]) || !empty($parts[3]))
				$image_html = '<a'.(empty($parts[2]) ? '' : ' href="'.h($parts[2]).'"').(empty($parts[3]) ? '' : ' onclick="'.h($parts[3]).'"').'>'.$image_html.'</a>';
			echo $image_html;
		}
		?>
	</div>
	<?php if ($captions) { foreach ($captions as $key => $cur_caption) { ?>
	<div id="<?php e($key); ?>" class="nivo-html-caption">
		<?php echo $cur_caption; ?>
	</div>
	<?php } } ?>
</div>