<?php
/**
 * Show a page.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	$entity = com_content_page::factory((int) $_REQUEST['id']);
} else {
	$entity = $_->nymph->getEntity(
			array('class' => com_content_page),
			array('&',
				'tag' => array('com_content', 'page'),
				'strict' => array(
					array('alias', $_REQUEST['a']),
					array('enabled', true)
				)
			)
		);
}

if (!isset($entity->guid) || !$entity->ready())
	throw new HttpClientException(null, 404);

// Set the default variant for pages.
if ($_->config->com_content->page_variant && $_->com_content->is_variant_valid($_->config->com_content->page_variant)) {
	$cur_template = $_->current_template;
	$_->config->$cur_template->variant = $_->config->com_content->page_variant;
}

// Check for and set the variant for the current template.
if (isset($entity->variants[$_->current_template]) && $_->com_content->is_variant_valid($entity->variants[$_->current_template])) {
	$cur_template = $_->current_template;
	$_->config->$cur_template->variant = $entity->variants[$_->current_template];
}

// Page title.
if ($entity->title_use_name || !isset($entity->title))
	$title = format_content($entity->name);
else
	$title = format_content($entity->title);
switch ($entity->get_option('title_position')) {
	case 'prepend':
		$_->page->title_pre("$title - ");
		break;
	case 'append':
		$_->page->title(" - $title");
		break;
	case 'replace':
		$_->page->title_set($title);
		break;
}

// Meta tags.
if ($entity->meta_tags) {
	$module = new module('com_content', 'meta_tags', 'head');
	$module->entity = $entity;
}

if ($entity->get_option('show_breadcrumbs')) {
	$module = new module('com_content', 'breadcrumb', 'breadcrumbs');
	$module->entity = $entity;
}

$entity->print_page();