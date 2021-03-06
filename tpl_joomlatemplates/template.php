<?php
/**
 * Main page of the Joomla template adapter.
 *
 * @package Templates\joomlatemplates
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
header('Content-Type: text/html');

// Get the current Joomla! template.
$jtemplate = $_->config->tpl_joomlatemplates->template;

// Get the directory for the template.
$jtdir = "templates/tpl_joomlatemplates/templates/$jtemplate/";
if (!file_exists($jtdir))
	die('Required Joomla! template is missing!');

/**
 * Joomla! template adapter. This class will act as the Joomla! template class.
 */
include('templates/tpl_joomlatemplates/classes/jtemplate_adapter.php');

$jtclass = new jtemplate_adapter($jtemplate, $jtdir);
$jtclass->render();