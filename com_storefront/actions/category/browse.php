<?php
/**
 * Browse a category's products.
 *
 * @package Components\storefront
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	$entity = com_sales_category::factory((int) $_REQUEST['id']);
} else {
	$entity = $_->entity_manager->get_entity(
			array('class' => com_sales_category),
			array('&',
				'tag' => array('com_sales', 'category'),
				'data' => array(
					array('enabled', true),
					array('alias', $_REQUEST['a'])
				)
			)
		);
}

if (!isset($entity->guid) || !$entity->enabled)
	throw new HttpClientException(null, 404);

// Page title.
if ($entity->title_use_name || !isset($entity->title))
	$title = format_content($entity->name);
else
	$title = format_content($entity->title);
switch ($entity->title_position) {
	case 'prepend':
	default:
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
	$module = new module('com_storefront', 'meta_tags', 'head');
	$module->entity = $entity;
}

if ($entity->show_breadcrumbs) {
	$module = new module('com_storefront', 'breadcrumb', 'breadcrumbs');
	$module->entity = $entity;
}

// TODO: Finish the spec view.
//$module = new module('com_storefront', 'category/specs', 'left');
//$module->entity = $entity;

$module = new module('com_storefront', 'category/browse', 'content');
$module->entity = $entity;
foreach ((array) $entity->show_pages as $cur_page) {
	if (!isset($cur_page->guid))
		continue;
	$page_module = $cur_page->print_page();
	if (!$page_module)
		continue;
	$page_module->detach();
	$module->show_page_modules[] = $page_module;
	// Check for and set the variant for the current template.
	if (isset($cur_page->variants[$_->current_template]) && $_->com_content->is_variant_valid($cur_page->variants[$_->current_template])) {
		$cur_template = $_->current_template;
		$_->config->$cur_template->variant = $cur_page->variants[$_->current_template];
	}
}
$module->page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
$module->products_per_page = $_->config->com_storefront->products_per_page;
switch ($_REQUEST['sort']) {
	case 'name':
	default:
		$module->sort = 'name';
		$module->sort_var = 'name';
		$module->sort_reverse = false;
		break;
	case 'name_r':
		$module->sort = 'name_r';
		$module->sort_var = 'name';
		$module->sort_reverse = true;
		break;
	case 'unit_price':
		$module->sort = 'unit_price';
		$module->sort_var = 'unit_price';
		$module->sort_reverse = false;
		break;
	case 'unit_price_r':
		$module->sort = 'unit_price_r';
		$module->sort_var = 'unit_price';
		$module->sort_reverse = true;
		break;
	case 'p_cdate':
		$module->sort = 'p_cdate';
		$module->sort_var = 'p_cdate';
		$module->sort_reverse = false;
		break;
	case 'p_cdate_r':
		$module->sort = 'p_cdate_r';
		$module->sort_var = 'p_cdate';
		$module->sort_reverse = true;
		break;
}