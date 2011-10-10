<?php
/**
 * Print a public raffle form.
 *
 * @package Pines
 * @subpackage com_raffle
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$entity = com_raffle_raffle::factory((int) $_REQUEST['id']);
if (!isset($entity->guid) || !$entity->public)
	return 'error_404';

if ($entity->complete) {
	pines_notice('This raffle has been completed.');
	pines_redirect(pines_url());
	return;
}

$entity->print_public();

?>