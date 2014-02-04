<?php
/**
 * Displays user defined "about" information.
 *
 * @package Components\about
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = h("About {$_->config->system_name}");
?>
<p><?php e($_->config->com_about->description, ENT_COMPAT, '', false); ?></p>