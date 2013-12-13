<?php
/**
 * tpl_print class.
 *
 * @package Templates\print
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * tpl_print main class.
 *
 * A simple template which only outputs the content position. Good for printing
 * a page.
 *
 * @package Templates\print
 */
class tpl_print extends template {
	/**
	 * The template format.
	 * @var string $format
	 */
	public $format = 'html-desktop-5';
	/**
	 * The editor CSS location, relative to WonderPHP's directory.
	 * @var string $editor_css
	 */
	public $editor_css = 'templates/tpl_print/css/style.css';

	/**
	 * Format a menu in HTML.
	 *
	 * @param array $menu The menu.
	 * @return string The menu's HTML.
	 */
	public function menu($menu) {
		if (count($menu) == 1)
			return '';
		$return = '<ul class="dropdown dropdown-vertical">';
		foreach ($menu as $key => $value) {
			if (is_int($key)) continue;
			$return .= $this->sub_menu($value);
		}
		$return .= '</ul>';
		return $return;
	}

	/**
	 * Format a sub menu in HTML.
	 *
	 * @param array $menu The menu.
	 * @return string The menu's HTML.
	 */
	public function sub_menu($menu) {
		$return = '<li class="ui-state-default'.($menu[0]['current_page'] ? ' current_page' : '').($menu[0]['current_page_parent'] ? ' current_page_parent' : '').'"><a'.
			(count($menu) > 1 ? ' class="dir" href="' : ' href="').
			(isset($menu[0]['href']) ? htmlspecialchars($menu[0]['href']) : 'javascript:void(0);').
			(isset($menu[0]['onclick']) ? "\" onclick=\"{$menu[0]['onclick']}\">" : '">').
			htmlspecialchars($menu[0]['text']).'</a>';
		if (count($menu) > 1) {
			$return .= '<ul>';
			foreach ($menu as $key => $value) {
				if (is_int($key)) continue;
				$return .= $this->sub_menu($value);
			}
			$return .= '</ul>';
		}
		$return .= '</li>';
		return $return;
	}
}