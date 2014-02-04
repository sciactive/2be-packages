<?php
/**
 * Provides a form for the user to choose a page.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$pages = $_->entity_manager->get_entities(array('class' => com_content_page), array('&', 'tag' => array('com_content', 'page')));
$_->entity_manager->sort($pages, 'name');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Page</span>
			<select class="pf-field" name="id">
				<?php foreach ($pages as $cur_page) { ?>
				<option value="<?php e($cur_page->guid); ?>"<?php echo $this->id == "$cur_page->guid" ? ' selected="selected"' : ''; ?>><?php e($cur_page->name); ?></option>
				<?php } ?>
			</select></label>
	</div>
</div>