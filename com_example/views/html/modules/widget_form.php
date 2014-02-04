<?php
/**
 * Provides a form for the user to choose a foobar.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$foobars = $_->entity_manager->get_entities(array('class' => com_example_foobar), array('&', 'tag' => array('com_example', 'foobar')));
$_->entity_manager->sort($foobars, 'name');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Foobar</span>
			<select class="pf-field" name="id">
				<?php foreach ($foobars as $cur_foobar) { ?>
				<option value="<?php e($cur_foobar->guid); ?>"<?php echo $this->id == "$cur_foobar->guid" ? ' selected="selected"' : ''; ?>><?php e($cur_foobar->name); ?></option>
				<?php } ?>
			</select></label>
	</div>
</div>