<?php
/**
 * tpl_mobile class.
 *
 * @package Templates\mobile
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * tpl_mobile main class.
 *
 * jQuery UI styled template for mobile browsers.
 *
 * @package Templates\mobile
 */
class tpl_mobile extends template {
	/**
	 * The template format.
	 * @var string $format
	 */
	public $format = 'html-mobile-5';
	/**
	 * The editor CSS location, relative to WonderPHP's directory.
	 * @var string $editor_css
	 */
	public $editor_css = 'templates/tpl_mobile/css/editor.css';

	/**
	 * Format a menu in HTML.
	 * 
	 * @param array $menu The menu.
	 * @return string The menu's HTML.
	 */
	public function menu($menu) {
		global $_;
		if (count($menu) == 1)
			return '';
		$return = '<ul class="menu">';
		foreach ($menu as $key => &$value) {
			if ((int) $key === $key) continue;
			$return .= $this->sub_menu($value);
		}
		$return .= '</ul>';
		return $return;
	}

	/**
	 * Format a sub menu in HTML.
	 * 
	 * @param array &$menu The menu.
	 * @return string The menu's HTML.
	 */
	public function sub_menu(&$menu) {
		$count = count($menu);
		// TODO: Remove target attribute. It's not XHTML 1.0 Strict.
		$return = '<li><a class="btn" href="'.
			(isset($menu[0]['href']) ? h($menu[0]['href']) : 'javascript:void(0);').'"'.
			(isset($menu[0]['onclick']) ? " onclick=\"{$menu[0]['onclick']}\"" : '').
			(isset($menu[0]['target']) ? " target=\"{$menu[0]['target']}\"" : '').
			'>'.h($menu[0]['text']).'</a>'.
			($count > 1 ? '<a class="btn expander" href="javascript:void(0);"><i class="icon-chevron-down"></i></a>' : '');
		if ($count > 1) {
			$return .= '<ul>';
			foreach ($menu as $key => &$value) {
				if ((int) $key === $key) continue;
				$return .= $this->sub_menu($value);
			}
			$return .= '</ul>';
		}
		$return .= '</li>';
		return $return;
	}
}