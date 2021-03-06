<?php
/**
 * Template for a module that needs some styling.
 *
 * @package Templates\pinescms
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');
?>
<div class="module <?php e(implode(' ', $this->classes)); ?>"><?php echo $this->content; ?></div>