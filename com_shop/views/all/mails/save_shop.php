<?php
/**
 * An shop mail that's sent when a shop is saved.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = '#to_first_name#, a shop was saved!';
?>
Hi #to_name#,<br />
<br />
I'm letting you know that #name# just saved a shop:<br />
<br />
<span style="margin-left: 2em;">#shop_name#</span><br />
<br />
Regards,<br />
#system_name#