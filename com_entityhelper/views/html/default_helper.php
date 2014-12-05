<?php
/**
 * A default, rather simple, entity helper.
 *
 * @package Components\entityhelper
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
if ($this->render == 'body') {
	$type = $this->entity->info('type');
	$icon = $this->entity->info('icon');
	$image = $this->entity->info('image');
	$types = $this->entity->info('types');
	$url_list = $this->entity->info('url_list');
	if (!preg_match('/^[A-Z]{2,}/', $type))
		$type = ucwords($type);
	if (!preg_match('/^[A-Z]{2,}/', $types))
		$types = ucwords($types);
?>
<h4 style="float: left;">
	<?php if ($icon) { ?>
	<i style="float: left; height: 16px; width: 16px;" class="<?php e($icon); ?>"></i>&nbsp;
	<?php }
	e($type); ?>
</h4>
<?php if ($url_list) { ?>
<div style="float: right;">
	<a href="<?php e($url_list); ?>">List <?php e($types); ?></a>
</div>
<?php } ?>
<div style="clear: both; padding-top: 1em;" class="clearfix">
	<div class="alert alert-info" style="float: left; font-size:.9em;">
		Created: <?php echo format_date($this->entity->cdate, 'full_med'); ?>.<br />
		Modified: <?php echo format_date($this->entity->mdate, 'full_med'); ?>.
	</div>
	<?php if ($this->entity->user->guid) { ?>
	<div style="float: right; clear: right; font-size:.9em;">
		Owned by <a data-entity="<?php e($this->entity->user->guid); ?>" data-entity-context="user"><?php e($this->entity->user->info('name')); ?></a>
	</div>
	<?php } if ($this->entity->group->guid) { ?>
	<div style="float: right; clear: right; font-size:.9em;">
		Belongs to group <a data-entity="<?php e($this->entity->group->guid); ?>" data-entity-context="group"><?php e($this->entity->group->info('name')); ?></a>
	</div>
	<?php } ?>
</div>
<?php if ($image) { ?>
<div style="clear: both; padding-top: 1em; text-align: center;">
	<span class="thumbnail" style="display: inline-block; max-width: 90%;">
		<img src="<?php e($image); ?>" alt="" style="max-width: 100%;">
	</span>
</div>
<?php } } elseif ($this->render == 'footer') {
	$url_view = $this->entity->info('url_view');
	$url_edit = $this->entity->info('url_edit');
	if ($url_view) { ?>
<a href="<?php e($url_view); ?>" class="btn btn-default">View</a>
<?php } if ($url_edit) { ?>
<a href="<?php e($url_edit); ?>" class="btn btn-default">Edit</a>
<?php } if (!$url_view && !$url_edit) { ?>
<a href="javascript:void(0);" class="btn btn-default" data-dismiss="modal">Close</a>
<?php } }