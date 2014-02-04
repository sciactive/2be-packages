<?php
/**
 * View thank you page.
 *
 * @package Components\contact
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<div>
<?php 
	if (!empty($_->config->com_contact->thankyou_page)) {
		$page = (int) $_->config->com_contact->thankyou_page;
		pines_redirect(pines_url('com_content', 'page', array('id' => $page)));
	} else {
		$this->title = h($_->config->com_contact->thankyou_title);
		e($_->config->com_contact->thankyou_message);
	}
?>
</div>