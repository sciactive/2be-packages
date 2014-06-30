<?php
/**
 * tpl_shop class.
 *
 * @package Templates\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * tpl_shop main class.
 *
 * A nice looking, fluid width template based on Bootstrap.
 *
 * @package Templates\shop
 */
class tpl_shop extends template {
	/**
	 * The template format.
	 * @var string $format
	 */
	public $format = 'html-desktop-5';
	/**
	 * The editor CSS location, relative to 2be's directory.
	 * @var string $editor_css
	 */
	public $editor_css = 'templates/tpl_shop/css/editor.css';

	/**
	 * Format a menu in HTML.
	 *
	 * @param array $menu The menu.
	 * @return string The menu's HTML.
	 */
	public function menu($menu) {
		if (count($menu) == 1)
			return '';
		switch ($menu[0]['position']) {
			case 'left':
			case 'right':
			case 'content':
			case 'user1':
			case 'user2':
			case 'user3':
			case 'user4':
				$return = '<ul class="dropdown dropdown-vertical">';
				break;
			case 'main_menu':
			default:
				$return = '<ul class="nav navbar-nav navbar-right">';
				break;
		}
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
	 * @param bool $top_level Whether this menu is a top-level menu.
	 * @return string The menu's HTML.
	 */
	public function sub_menu(&$menu, $top_level = true) {
		$count = count($menu);
		$return = '<li class="'.(($count > 1) ? ($top_level ? 'dropdown' : 'dropdown-submenu') : '').($menu[0]['current_page'] || $menu[0]['current_page_parent'] ? ' active' : '').'"><a href="'.
			(isset($menu[0]['href']) ? h($menu[0]['href']) : 'javascript:void(0);').'"'.
			(isset($menu[0]['onclick']) ? " onclick=\"{$menu[0]['onclick']}\"" : '').
			(isset($menu[0]['target']) ? " target=\"{$menu[0]['target']}\"" : '').
			(($count > 1) ? ' data-toggle="dropdown" class="dropdown-toggle"' : '')
			.'>'.h($menu[0]['text']).
			(($count > 1 && $top_level) ? '<b class="caret"></b>' : '').'</a>';
		if ($count > 1) {
			$return .= '<ul class="dropdown-menu">';
			foreach ($menu as $key => &$value) {
				if ((int) $key === $key) continue;
				$return .= $this->sub_menu($value, false);
			}
			$return .= '</ul>';
		}
		$return .= '</li>';
		return $return;
	}

	public function url($component = null, $action = null, $params = array(), $full_location = false) {
		// Was needed for redirection, but not anymore. ?? Or is it??
		if ($_REQUEST['tpl_shop_ajax'] == 1)
			$params['tpl_shop_ajax'] = 1;
		return parent::url($component, $action, $params, $full_location);
	}
}
