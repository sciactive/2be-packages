<?php
/**
 * List renditions.
 *
 * @package Components\mailer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_mailer/listrenditions') )
	punt_user(null, pines_url('com_mailer', 'rendition/list'));

$_->com_mailer->list_renditions();