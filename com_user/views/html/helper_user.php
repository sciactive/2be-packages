<?php
/**
 * User entity helper.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$module = new module('com_entityhelper', 'default_helper');
$module->render = $this->render;
$module->entity = $this->entity;
echo $module->render();

if ($this->render == 'body' && gatekeeper('com_user/listusers')) { ?>
<div style="clear:both;">
	<hr />
	<div class="thumbnail pull-right" style="margin-bottom: .2em;">
		<img style="vertical-align: bottom;" src="<?php e($this->entity->info('avatar')); ?>" alt="Avatar" title="Avatar by Gravatar" />
	</div>
	<h3 style="margin:10px 0;">Properties</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">GUID</td>
				<td><?php e($this->entity->guid); ?></td>
			</tr>
			<?php if (!$_->config->com_user->email_usernames) { ?>
			<tr>
				<td style="font-weight:bold;">Username</td>
				<td><?php e($this->entity->username); ?></td>
			</tr>
			<?php } if (in_array('name', $_->config->com_user->user_fields)) { ?>
			<tr>
				<td style="font-weight:bold;">Real Name</td>
				<td><?php e($this->entity->name); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Enabled</td>
				<td><?php echo $this->entity->hasTag('enabled') ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php if ($_->config->com_user->email_usernames || (!empty($this->entity->email) && in_array('email', $_->config->com_user->user_fields))) { ?>
			<tr>
				<td style="font-weight:bold;">Email</td>
				<td><a href="mailto:<?php e($this->entity->email); ?>"><?php e($this->entity->email); ?></a><?php echo isset($this->entity->secret) ? ' (Unverified)' : ''; ?></td>
			</tr>
			<?php } if (!empty($this->entity->phone) && in_array('phone', $_->config->com_user->user_fields)) { ?>
			<tr>
				<td style="font-weight:bold;">Phone</td>
				<td><a href="tel:<?php e($this->entity->phone); ?>"><?php e(format_phone($this->entity->phone)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->fax) && in_array('fax', $_->config->com_user->user_fields)) { ?>
			<tr>
				<td style="font-weight:bold;">Fax</td>
				<td><a href="tel:<?php e($this->entity->fax); ?>"><?php e(format_phone($this->entity->fax)); ?></a></td>
			</tr>
			<?php } if (in_array('timezone', $_->config->com_user->user_fields)) { ?>
			<tr>
				<td style="font-weight:bold;">Timezone</td>
				<td><?php e($this->entity->get_timezone()).(empty($this->entity->timezone) ? ' (Inherited)' : ' (Assigned)'); ?></td>
			</tr>
			<?php } if ($this->entity->group->guid) { ?>
			<tr>
				<td style="font-weight:bold;">Primary Group</td>
				<td><a data-entity="<?php e($this->entity->group->guid); ?>" data-entity-context="group"><?php e($this->entity->group->info('name')); ?></a></td>
			</tr>
			<?php } if ($this->entity->groups) { ?>
			<tr>
				<td style="font-weight:bold;">Groups</td>
				<td>
					<ul>
						<?php
						$names = array();
						foreach ((array) $this->entity->groups as $cur_group) {
							if (!isset($cur_group->guid))
								continue;
							$names[] = '<li><a data-entity="'.h($cur_group->guid).'" data-entity-context="group">'.h($cur_group->info('name')).'</a></li>';
						}
						echo implode("\n", $names);
						?>
					</ul>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td style="font-weight:bold;">Inherit Abilities</td>
				<td><?php echo $this->entity->inherit_abilities ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php if (!empty($this->entity->referral_code) && $_->config->com_user->referral_codes) { ?>
			<tr>
				<td style="font-weight:bold;">Referral Code</td>
				<td><?php e($this->entity->referral_code); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php if (in_array('address', $_->config->com_user->user_fields)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Address</h3>
	<address>
		<?php if ($this->entity->address_type == 'us') {
			e($this->entity->address_1).'<br />';
			if (!empty($this->entity->address_2))
				e($this->entity->address_2).'<br />';
			e($this->entity->city).', ';
			e($this->entity->state).' ';
			e($this->entity->zip);
		} else {
			echo '<pre>'.h($this->entity->address_international).'</pre>';
		} ?>
	</address>
	<?php if (in_array('additional_addresses', $_->config->com_user->user_fields) && $this->entity->addresses) { ?>
	<h3 style="margin:10px 0;">Additional Addresses</h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr>
				<th>Type</th>
				<th>Address 1</th>
				<th>Address 2</th>
				<th>City</th>
				<th>State</th>
				<th>Zip</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->entity->addresses as $cur_address) { ?>
			<tr>
				<td><?php e($cur_address['type']); ?></td>
				<td><?php e($cur_address['address_1']); ?></td>
				<td><?php e($cur_address['address_2']); ?></td>
				<td><?php e($cur_address['city']); ?></td>
				<td><?php e($cur_address['state']); ?></td>
				<td><?php e($cur_address['zip']); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php } ?>
</div>
<?php } if (in_array('attributes', $_->config->com_user->user_fields) && $this->entity->attributes) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Attributes</h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr><th>Name</th><th>Value</th></tr>
		</thead>
		<tbody>
			<?php foreach ($this->entity->attributes as $cur_attribute) { ?>
			<tr><td><?php e($cur_attribute['name']); ?></td><td><?php e($cur_attribute['value']); ?></td></tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } }