<?php
/**
 * Provides a switcher to go to the desktop or mobile site.
 *
 * @package Components\uasniffer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if (!isset($this->mobile_text))
	$this->mobile_text = 'Mobile Version';
if (!isset($this->desktop_text))
	$this->desktop_text = 'Desktop Version';
if (!isset($this->center))
	$this->center = true;

$is_mobile = $_->depend->check('browser', 'mobile-real');

$switched = ($_COOKIE['com_uasniffer_switch'] == 'true');

?>
<div id="p_muid_links" style="<?php echo ($this->center ? 'text-align: center; ' : ''); ?>margin: 1em 0;">
	<script type="text/javascript">
		$_(function(){
			var values = function(){
				var cookies = document.cookie.split(';'), c, date = new Date();
				date.setTime(date.getTime()-1);
				for (var i = 0; i < cookies.length; i++) {
					c = cookies[i];
					while (c.charAt(0)==' ')
						c = c.substring(1,c.length);
					if (c.indexOf("com_uasniffer_switch=") == 0)
						return ['', date.toGMTString()];
				}
				date.setTime(date.getTime()+1209600000);
				return ['true', date.toGMTString()];
			}
			$("#p_muid_links").on("click", "a", function(){
				var cookie = values();
				document.cookie = 'com_uasniffer_switch='+cookie[0]+'; expires='+cookie[1]+'; path=<?php e($_->config->rela_location); ?>';
				location.reload(true);
			});
		});
	</script>
	<?php if ($this->show_both || !($is_mobile xor $switched)) { ?>
	<a href="javascript:void(0);"><?php e($this->mobile_text); ?></a>
	<?php } if ($this->show_both) { ?>
	|
	<?php } if ($this->show_both || ($is_mobile xor $switched)) { ?>
	<a href="javascript:void(0);"><?php e($this->desktop_text); ?></a>
	<?php } ?>
</div>