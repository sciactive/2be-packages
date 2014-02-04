<?php
/**
 * Shipper entity helper.
 *
 * @package Components\sales
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

if ($this->render == 'body' && gatekeeper('com_sales/listshippers')) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Properties</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;" colspan="2">GUID</td>
				<td><?php e($this->entity->guid); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;" colspan="2">Name</td>
				<td><?php e($this->entity->name); ?></td>
			</tr>
			<?php if (!empty($this->entity->email)) { ?>
			<tr>
				<td style="font-weight:bold;" colspan="2">Email</td>
				<td><a href="mailto:<?php e($this->entity->email); ?>"><?php e($this->entity->email); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->phone_work)) { ?>
			<tr>
				<td style="font-weight:bold;" colspan="2">Phone</td>
				<td><a href="tel:<?php e($this->entity->phone_work); ?>"><?php e(format_phone($this->entity->phone_work)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->fax)) { ?>
			<tr>
				<td style="font-weight:bold;" colspan="2">Fax</td>
				<td><a href="tel:<?php e($this->entity->fax); ?>"><?php e(format_phone($this->entity->fax)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->account_number)) { ?>
			<tr>
				<td style="font-weight:bold;" colspan="2">Account Number</td>
				<td><?php e($this->entity->account_number); ?></td>
			</tr>
			<?php } ?>
			<tr >
				<td style="font-weight:bold; vertical-align: middle;" rowspan="4">Tracking</td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Supported</td>
				<td><?php echo $this->entity->can_track() ? 'Yes' : 'No'; ?></td>
			</tr>
			<?php if (isset($this->entity->tracking) && ($this->entity->tracking != 'custom' || ($this->entity->tracking == 'custom' && !empty($this->entity->tracking_url)))) { ?>
			<tr>
				<td style="font-weight:bold;">Setup</td>
				<td><?php e($this->entity->tracking); ?></td>
			</tr>
			<?php } if (!empty($this->entity->tracking_url)) { ?>
			<tr>
				<td style="font-weight:bold;">URL</td>
				<td><?php e($this->entity->tracking_url); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php if (!empty($this->entity->address_1) || !empty($this->entity->address_international)) { ?>
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
</div>
<?php } if (!empty($this->entity->terms)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Terms</h3>
	<pre><?php e($this->entity->terms); ?></pre>
</div>
<?php } if (!empty($this->entity->comments)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Comments</h3>
	<pre><?php e($this->entity->comments); ?></pre>
</div>
<?php } }