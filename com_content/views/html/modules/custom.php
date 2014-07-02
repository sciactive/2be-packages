<?php
/**
 * Prints custom content.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_content->wrap_content)
	echo '<div style="position: relative;">';
if (is_callable($_->editor, 'parse_input')) {
	echo format_content($_->editor->parse_input($this->icontent));
} else {
	echo format_content($this->icontent);
}
if ($_->config->com_content->wrap_content)
	echo '</div>';