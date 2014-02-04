<?php
/**
 * Print meta tags.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

foreach ((array) $this->entity->meta_tags as $cur_meta_tag) { ?>

<meta name="<?php e($cur_meta_tag['name']); ?>" content="<?php e(format_content($cur_meta_tag['content'])); ?>" />
<?php }