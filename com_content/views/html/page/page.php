<?php
/**
 * Displays page content.
 *
 * @package Pines
 * @subpackage com_content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if (!isset($this->entity))
	$this->entity = com_content_page::factory((int) $this->id);

// Custom head code.
if ($this->entity->enable_custom_head && $pines->config->com_content->custom_head) {
	$head = new module('system', 'null', 'head');
	$head->content($this->entity->custom_head);
}

if ($this->entity->get_option('show_title'))
	$this->title = '<span class="page_title">'.htmlspecialchars($this->entity->name).'</span>';

if ($this->entity->get_option('show_author_info'))
	$this->note = '<span class="page_info"><span class="page_posted_by_text">Posted by </span><span class="page_author">'.htmlspecialchars($this->entity->user->name).'</span><span class="page_posted_on_text"> on </span><span class="page_date">'.htmlspecialchars(format_date($this->entity->p_cdate, 'date_short')).'</span></span>';

if ($this->entity->get_option('show_intro')) {
	if ($pines->config->com_content->wrap_pages)
		echo '<div style="position: relative;">';
	echo format_content($this->entity->intro);
	if ($pines->config->com_content->wrap_pages)
		echo '</div>';
}

if ($pines->config->com_content->wrap_pages)
	echo '<div style="position: relative;">';
echo format_content($this->entity->content);
if ($pines->config->com_content->wrap_pages)
	echo '</div>';

?>