<?php
/**
 * Prints a fortune.
 *
 * @package Components\fortune
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Fortune';
?>
<div>
	<?php e($this->fortune); ?>
</div>