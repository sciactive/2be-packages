<?php
/**
 * Display a form to swap inventory on an ESP.
 *
 * @package Components\esp
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<form class="pf-form" id="p_muid_form" action="">
	<div class="pf-element pf-heading">
		<h3><?php e($this->entity->item['entity']->name); ?></h3>
	</div>
	<div class="pf-element">
		<span class="pf-label">Item Serial</span>
		<span class="pf-field"><?php e($this->entity->item['serial']); ?></span>
	</div>
	<div class="pf-element pf-heading">
		<h3>New Item</h3>
	</div>
	<div class="pf-element">
		<span class="pf-label">Item Serial</span>
		<input class="pf-field form-control" type="text" name="new_serial" value="" />
	</div>
</form>