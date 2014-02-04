<?php
/**
 * Provides a form for the user to submit custom content.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$_->editor->load();
?>
<div class="pf-form">
	<div class="pf-element pf-full-width">
		<label>
			<span class="pf-label">Content</span><br />
			<textarea rows="8" cols="35" class="peditor" style="width: 740px; height: 500px;" name="icontent"><?php e($this->icontent); ?></textarea>
		</label>
	</div>
</div>