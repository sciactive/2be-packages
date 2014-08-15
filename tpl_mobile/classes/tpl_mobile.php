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
/* @var $_ core */
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
	 * The editor CSS location, relative to 2be's directory.
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
		$id = uniqid('menu_');
		$return = '<div class="panel-group" id="'.$id.'">';
		foreach ($menu as $key => &$value) {
			if ((int) $key === $key) continue;
			$return .= $this->sub_menu($value, $id);
		}
		$return .= '</div>';
		return $return;
	}

	/**
	 * Format a sub menu in HTML.
	 *
	 * @param array &$menu The menu.
	 * @return string The menu's HTML.
	 */
	public function sub_menu(&$menu, $id) {
		$count = count($menu);
		$return = '<div class="panel '.(($menu[0]['current_page'] || $menu[0]['current_page_parent']) ? 'panel-primary' : 'panel-default').'">'.
			'<a class="panel-heading" href="'.
			(isset($menu[0]['href']) ? h($menu[0]['href']) : 'javascript:void(0);').'"'.
			($count > 1 ? ' data-parent="#'.$id.'" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0"' : '').
			(isset($menu[0]['onclick']) ? " onclick=\"{$menu[0]['onclick']}\"" : '').
			(isset($menu[0]['target']) ? " target=\"{$menu[0]['target']}\"" : '').
			'>'.
			($count > 1 ? '<span class="menu-icon"><span></span><span></span><span></span></span>' : '').
			'<big class="panel-title"><span class="panel-title">'.h($menu[0]['text']).'</span></big>'.
			'</a>';
		if ($count > 1) {
			$new_id = uniqid('menu_');
			$return .= '<div class="panel-collapse collapse"><div class="panel-body clearfix"><div class="panel-group" id="'.$new_id.'">';
			foreach ($menu as $key => &$value) {
				if ((int) $key === $key) continue;
				$return .= $this->sub_menu($value, $new_id);
			}
			$return .= '</div></div></div>';
		}
		return $return.'</div>';
	}
}