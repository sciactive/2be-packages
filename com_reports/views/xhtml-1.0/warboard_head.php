<?php
/**
 * Refreshes the company warboard.
 *
 * @package Pines
 * @subpackage com_reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<meta http-equiv="refresh" content="180;url=<?php echo htmlspecialchars(pines_url('com_reports', 'warboard')); ?>" />