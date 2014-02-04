<?php
/**
 * Provides a form for the user to choose a category.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$categories = $_->entity_manager->get_entities(array('class' => com_content_category), array('&', 'tag' => array('com_content', 'category')));
$_->entity_manager->sort($categories, 'name');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Category</span>
			<select class="pf-field" name="id">
				<?php foreach ($categories as $cur_category) { ?>
				<option value="<?php e($cur_category->guid); ?>"<?php echo $this->id == "$cur_category->guid" ? ' selected="selected"' : ''; ?>><?php e($cur_category->name); ?></option>
				<?php } ?>
			</select></label>
	</div>
</div>