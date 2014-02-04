<?php
/**
 * Print front page meta tags.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

foreach ((array) $_->config->com_content->front_page_meta_tags as $cur_meta_tag) {
	list ($name, $content) = explode(':', $cur_meta_tag, 2);
	if (empty($name) || empty($content))
		continue;
	?>

<meta name="<?php e($name); ?>" content="<?php e(format_content($content)); ?>" />
<?php }