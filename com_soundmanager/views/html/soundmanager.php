<?php
/**
 * A view to load SoundManager 2.
 *
 * @package Components\soundmanager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	window.SM2_DEFER = true;
	pines.loadjs("<?php e($_->config->location); ?>components/com_soundmanager/includes/soundmanager/script/<?php echo $_->config->debug_mode ? 'soundmanager2.js' : 'soundmanager2-nodebug-jsmin.js'; ?>");
	pines.load(function(){
		window.soundManager = new SoundManager();
		soundManager.url = "<?php e($_->config->rela_location); ?>components/com_soundmanager/includes/soundmanager/swf/";
		soundManager.flashVersion = 9;
		soundManager.useFlashBlock = false;
		soundManager.beginDelayedInit();
	});
</script>