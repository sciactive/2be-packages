<?php
/**
 * View a shop's owner.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$user = $this->entity->user;

if (!isset($user))
	return;

?>
<h4>Shop Owner</h4>
<div id="p_muid_owner" class="shop_owner">
	<?php if (gatekeeper()) { ?>
	<a href="#" class="btn btn-default" style="float: right; clear: right;">Message</a>
	<?php } ?>
	<div class="media">
		<a class="pull-left" data-entity="<?php e($user->guid); ?>" data-entity-context="user">
			<img class="media-object" src="<?php e($user->info('avatar')); ?>" alt="Avatar" title="Avatar by Gravatar" />
		</a>
		<div class="media-body">
			<h4 class="media-heading"><a data-entity="<?php e($user->guid); ?>" data-entity-context="user"><?php e($user->name); ?></a></h4>
			<?php if (!$_->config->com_user->email_usernames) { ?>
			Username: <?php e($user->username); ?><br />
			<?php } ?>
			Member since: <?php e(format_date($user->p_cdate, 'date_med')); ?><br />
			<?php if (
					$this->entity->display_email &&
					(
						$_->config->com_user->email_usernames ||
						(!empty($user->email) && in_array('email', $_->config->com_user->user_fields))
					)
				) { ?>
			Email: <a href="mailto:<?php e($user->email); ?>"><?php e($user->email); ?></a><?php echo isset($user->secret) ? ' (Unverified)' : ''; ?><br />
			<?php } /* if (!empty($user->phone) && in_array('phone', $_->config->com_user->user_fields)) { ?>
			Phone: <a href="tel:<?php e($user->phone); ?>"><?php e(format_phone($user->phone)); ?></a><br />
			<?php } if (!empty($user->fax) && in_array('fax', $_->config->com_user->user_fields)) { ?>
			Fax: <a href="tel:<?php e($user->fax); ?>"><?php e(format_phone($user->fax)); ?></a><br />
			<?php } */ ?>
		</div>
	</div>
</div>