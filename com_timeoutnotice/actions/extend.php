<?php
/**
 * Extend the user's session.
 *
 * @package Components\timeoutnotice
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

$pines->page->override = true;
header('Content-Type: application/json');
$pines->page->override_doc(json_encode(gatekeeper()));