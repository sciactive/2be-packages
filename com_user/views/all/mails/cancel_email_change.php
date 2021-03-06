<?php
/**
 * An email sent to cancel a user's email address change.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = '#to_first_name#, Your email address has been changed on #system_name#.';
?>
Hi #to_name#,<br />
<br />
We've received a request to change your email address at <a href="#site_link#" target="_blank">#site_link#</a>
to #new_email#. If you didn't request this change, you can cancel it by clicking
this link:<br />
<br />
<a href="#cancel_link#" target="_blank">#cancel_link#</a><br />
<br />
If you did make this request, you can complete the change by clicking the link
emailed to #new_email#.<br />
<br />
Regards,<br />
#system_name#