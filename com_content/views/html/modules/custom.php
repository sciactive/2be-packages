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
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if ($_->config->com_content->wrap_content)
	echo '<div style="position: relative;">';
echo format_content($this->icontent);
if ($_->config->com_content->wrap_content)
	echo '</div>';