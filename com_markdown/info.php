<?php
/**
 * com_markdown's information.
 *
 * @package Components\markdown
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Markdown',
	'author' => 'SciActive (Component), Michel Fortin (Markdown Library)',
	'version' => '1.2.5-1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Markdown processor',
	'description' => "Provides a Markdown to HTML converter.\n\nFor markdown syntax, see http://daringfireball.net/projects/markdown/syntax\nFor extra features in this version, see http://michelf.ca/projects/php-markdown/extra",
	'depend' => array(
		'pines' => '<3',
	),
);