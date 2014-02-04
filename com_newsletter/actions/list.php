<?php
/**
 * List all the newsletters.
 *
 * @package Components\newsletter
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_newsletter/listmail') && !gatekeeper('com_newsletter/send') )
	punt_user(null, pines_url('com_newsletter', 'list'));

$_->com_newsletter->list_mails();