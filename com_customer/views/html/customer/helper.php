<?php
/**
 * Customer entity helper.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$module = new module('com_entityhelper', 'default_helper');
$module->render = $this->render;
$module->entity = $this->entity;
echo $module->render();

if ($this->render == 'body' && gatekeeper('com_customer/listcustomers')) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Properties</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">GUID</td>
				<td><?php e($this->entity->guid); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Username</td>
				<td><a data-entity="<?php e($this->entity->guid); ?>" data-entity-context="user"><?php e($this->entity->username); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Real Name</td>
				<td><?php e($this->entity->name); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Enabled</td>
				<td><?php echo $this->entity->has_tag('enabled') ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php if (!empty($this->entity->email) && in_array('email', $_->config->com_customer->shown_fields_customer)) { ?>
			<tr>
				<td style="font-weight:bold;">Email</td>
				<td><a href="mailto:<?php e($this->entity->email); ?>"><?php e($this->entity->email); ?></a></td>
			</tr>
			<?php } if (isset($this->entity->company->guid) && in_array('company', $_->config->com_customer->shown_fields_customer)) { ?>
			<tr>
				<td style="font-weight:bold;">Company</td>
				<td><a data-entity="<?php e($this->entity->company->guid); ?>" data-entity-context="com_customer_company"><?php e($this->entity->company->name); ?></a></td>
			</tr>
			<?php if (!empty($this->entity->job_title)) { ?>
			<tr>
				<td style="font-weight:bold;">Job Title</td>
				<td><?php e($this->entity->job_title); ?></td>
			</tr>
			<?php } } if (in_array('phone', $_->config->com_customer->shown_fields_customer)) {
				if (!empty($this->entity->phone_cell)) { ?>
			<tr>
				<td style="font-weight:bold;">Cell Phone</td>
				<td><a href="tel:<?php e($this->entity->phone_cell); ?>"><?php e(format_phone($this->entity->phone_cell)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->phone_work)) { ?>
			<tr>
				<td style="font-weight:bold;">Work Phone</td>
				<td><a href="tel:<?php e($this->entity->phone_work); ?>"><?php e(format_phone($this->entity->phone_work)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->phone_home)) { ?>
			<tr>
				<td style="font-weight:bold;">Home Phone</td>
				<td><a href="tel:<?php e($this->entity->phone_home); ?>"><?php e(format_phone($this->entity->phone_home)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->fax)) { ?>
			<tr>
				<td style="font-weight:bold;">Fax</td>
				<td><a href="tel:<?php e($this->entity->fax); ?>"><?php e(format_phone($this->entity->fax)); ?></a></td>
			</tr>
			<?php } } ?>
			<tr>
				<td style="font-weight:bold;">Timezone</td>
				<td><?php e($this->entity->get_timezone()).(empty($this->entity->timezone) ? ' (Inherited)' : ' (Assigned)'); ?></td>
			</tr>
			<?php if (in_array('referrer', $_->config->com_customer->shown_fields_customer)) { ?>
			<tr>
				<td style="font-weight:bold;">Referrer</td>
				<td><?php foreach ($_->config->com_customer->referrer_values as $cur_value) {
						if ($this->entity->referrer == $cur_value)
							e($cur_value);
					} ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php if (in_array('address', $_->config->com_customer->shown_fields_customer)) { ?>
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
	<?php if ($this->entity->addresses) { ?>
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
<?php } if (in_array('points', $_->config->com_customer->shown_fields_customer)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Points</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">Current Points</td>
				<td style="text-align: right;"><?php e($this->entity->points); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Peak Points</td>
				<td style="text-align: right;"><?php e($this->entity->peak_points); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Total Points in All Time</td>
				<td style="text-align: right;"><?php e($this->entity->total_points); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php } if (in_array('membership', $_->config->com_customer->shown_fields_customer)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Membership</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">Member</td>
				<td><?php echo $this->entity->member ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php if ($this->entity->member) { ?>
			<tr>
				<td style="font-weight:bold;">Member Since</td>
				<td><?php e(format_date($this->entity->member_since, 'full_long')); ?></td>
			</tr>
			<?php } if ($this->entity->member_exp) { ?>
			<tr>
				<td style="font-weight:bold;">Membership Expiration</td>
				<td><?php echo $this->entity->member_exp ? h(format_date($this->entity->member_exp, 'date_long')) : ''; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } if ($this->entity->attributes && in_array('attributes', $_->config->com_customer->shown_fields_customer) && $this->entity->attributes) { ?>
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