<?php
/**
 * Entity Helper for Testimonials
 *
 * @package Components\testimonials
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

// Make footer URLs for lists & get status
// Check if it is a review
if ($this->entity->hasTag('review')) {
	$action = 'testimonial/list_reviews';
} else {
	$action = 'testimonial/list';
}
// get the status
if ($this->entity->hasTag('pending'))
	$status = 'pending';
if ($this->entity->hasTag('approved'))
	$status = 'approved';
if ($this->entity->hasTag('denied'))
	$status = 'denied';

if ($this->render == 'body' && gatekeeper('com_testimonials/listtestimonials')) {
$module = new module('com_entityhelper', 'default_helper');
$module->render = $this->render;
$module->entity = $this->entity;
echo $module->render();
?>

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
				<td style="font-weight:bold;">Testimonial ID</td>
				<td><?php e($this->entity->id); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Customer</td>
				<td><a data-entity="<?php e($this->entity->customer->guid); ?>" data-entity-context="com_customer_customer"><?php e($this->entity->customer->name); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Testimonial Status</td>
				<td><?php e(ucwords($status)); ?></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Created By</td>
				<td><a data-entity="<?php e($this->entity->user->guid); ?>" data-entity-context="user"><?php e($this->entity->user->name); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Location</td>
				<td><a data-entity="<?php e($this->entity->group->guid); ?>" data-entity-context="group"><?php e($this->entity->group->name); ?></a></td>
			</tr>
			<tr>
				<td style="font-weight:bold;">Date</td>
				<td><?php e(format_date($this->entity->cdate)); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<?php } elseif ($this->render == 'footer') { ?>
	<a href="<?php e(pines_url('com_testimonials', $action, array('show' => 'id:'.$this->entity->id, 'type' => $status))); ?>" class="btn btn-default">View in List</a>
	<?php if (gatekeeper('com_testimonials/edittestimonials')) { ?>
	<a href="<?php e(pines_url('com_testimonials', 'testimonial/edit', array('id' => $this->entity->guid))); ?>" class="btn btn-default">Edit</a>
<?php } }