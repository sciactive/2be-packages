<?php
/**
 * Provides a form for the user to choose a page.
 *
 * @package Pines
 * @subpackage com_content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$pages = $pines->entity_manager->get_entities(array('class' => com_content_page), array('&', 'tag' => array('com_content', 'page')));
$pines->entity_manager->sort($pages, 'name');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Page</span>
			<select class="pf-field ui-widget-content ui-corner-all" name="id">
				<?php foreach ($pages as $cur_page) { ?>
				<option value="<?php echo $cur_page->guid; ?>"><?php echo htmlentities($cur_page->name); ?></option>
				<?php } ?>
			</select></label>
	</div>
</div>