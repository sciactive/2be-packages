<?php
/**
 * Prints breadcrumbs example.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<ul class="breadcrumb">
	<li><a href="<?php e(pines_url()); ?>" class="breadcrumb_item">Home</a> <span class="divider">&gt;</span></li>
	<li><span class="breadcrumb_item">Example Breadcrumbs</span> <span class="divider">&gt;</span></li>
	<li class="active"><span class="breadcrumb_item"><?php e($this->position); ?></span></li>
</ul>