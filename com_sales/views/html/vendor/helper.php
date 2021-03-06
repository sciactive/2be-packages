<?php
/**
 * Vendor entity helper.
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

if ($this->render == 'body' && gatekeeper('com_sales/listvendors')) { ?>
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
				<td style="font-weight:bold;">Name</td>
				<td><?php e($this->entity->name); ?></td>
			</tr>
			<?php if (!empty($this->entity->email)) { ?>
			<tr>
				<td style="font-weight:bold;">Email</td>
				<td><a href="mailto:<?php e($this->entity->email); ?>"><?php e($this->entity->email); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->phone_work)) { ?>
			<tr>
				<td style="font-weight:bold;">Phone</td>
				<td><a href="tel:<?php e($this->entity->phone_work); ?>"><?php e(format_phone($this->entity->phone_work)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->fax)) { ?>
			<tr>
				<td style="font-weight:bold;">Fax</td>
				<td><a href="tel:<?php e($this->entity->fax); ?>"><?php e(format_phone($this->entity->fax)); ?></a></td>
			</tr>
			<?php } if (!empty($this->entity->account_number)) { ?>
			<tr>
				<td style="font-weight:bold;">Account Number</td>
				<td><?php e($this->entity->account_number); ?></td>
			</tr>
			<?php } if (isset($this->entity->logo)) { ?>
			<tr>
				<td style="font-weight:bold;">Company Logo</td>
				<td><img src="<?php e($this->entity->get_logo()); ?>" alt="Company Logo" /></td>
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
<?php } if (!empty($this->entity->client_username) || !empty($this->entity->client_password) || !empty($this->entity->client_rep_name) || !empty($this->entity->client_email) || !empty($this->entity->client_web_address)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Client Details</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">Username</td>
				<td><?php e($this->entity->client_username); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Password</td>
				<td><?php e($this->entity->client_password); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Rep Name</td>
				<td><?php e($this->entity->client_rep_name); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Email</td>
				<td><a href="mailto:<?php e($this->entity->client_email); ?>"><?php e($this->entity->client_email); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Web Address</td>
				<td><a href="<?php e($this->entity->client_web_address); ?>" target="_blank"><?php e($this->entity->client_web_address); ?></a></td>
			</tr>
		</tbody>
	</table>
</div>
<?php } if (!empty($this->entity->online_web_address) || !empty($this->entity->online_customer_id) || !empty($this->entity->online_username) || !empty($this->entity->online_password)) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Online Ordering</h3>
	<table class="table table-bordered" style="clear:both;">
		<tbody>
			<tr>
				<td style="font-weight:bold;">Web Address</td>
				<td><a href="<?php e($this->entity->online_web_address); ?>" target="_blank"><?php e($this->entity->online_web_address); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Customer ID</td>
				<td><?php e($this->entity->online_customer_id); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Username</td>
				<td><?php e($this->entity->online_username); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Password</td>
				<td><?php e($this->entity->online_password); ?></td>
			</tr>
		</tbody>
	</table>
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
<?php }
$products = (array) $_->nymph->getEntities(
		array('class' => com_sales_product),
		array('&',
			'tag' => array('com_sales', 'product'),
			'ref' => array('vendors', $this->entity)
		)
	);
if ($products) { ?>
<div style="clear:both;">
	<hr />
	<h3 style="margin:10px 0;">Products</h3>
	<table class="table table-bordered" style="clear:both;">
		<thead>
			<tr>
				<th>SKU</th>
				<th>Product</th>
				<th>Enabled</th>
				<th>Vendor Cost</th>
				<th>Link</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($products as $cur_product) { ?>
			<tr>
				<td><?php e($cur_product->sku); ?></td>
				<td><a data-entity="<?php e($cur_product->guid); ?>" data-entity-context="com_sales_product"><?php e($cur_product->name); ?></a></td>
				<td><?php echo $cur_product->enabled ? 'Yes' : 'No'; ?></td>
				<?php
				$entry = array();
				foreach ($cur_product->vendors as $cur_vendor) {
					if ($this->entity->is($cur_vendor['entity']))
						$entry = $cur_vendor;
				}
				?>
				<td>$<?php e($_->com_sales->round($entry['cost'], true)); ?></td>
				<td><a href="<?php e($entry['link']); ?>" target="_blank"><?php e($entry['link']); ?></a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php } }