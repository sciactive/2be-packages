<?php
/**
 * Lists threads and provides functions to manipulate them.
 *
 * @package Components\notes
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Threads';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_notes/thread/list']);
?>
<script type="text/javascript">
	$_(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_notes/editthread')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_notes', 'thread/edit', array('id' => '__title__'))); ?>},
				<?php } ?>
				//{type: 'button', text: 'E-Mail', extra_class: 'picon picon-mail-message-new', multi_select: true, url: 'mailto:__col_2__', delimiter: ','},
				{type: 'separator'},
				<?php if (gatekeeper('com_notes/deletethread')) { ?>
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_notes', 'thread/delete', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'separator'},
				<?php } ?>
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'threads',
						content: rows
					});
				}}
			],
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_notes/thread/list", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		$("#p_muid_grid").pgrid(cur_options);
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>GUID</th>
			<th>Last Modified</th>
			<th>Created</th>
			<th>Attached Entity</th>
			<th>Hidden</th>
			<th>Privacy</th>
			<th>Notes</th>
			<th>Original Poster</th>
			<th>Original Note</th>
			<th>Latest Poster</th>
			<th>Latest Note</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->threads as $thread) {
		// Try to guess the context (class name) of the attached entity.
		$context = str_replace('hook_override_', '', get_class($thread->entities[0]));
		if ($context == 'Entity' && substr($thread->entities[0]->tags[0], 0, 4) === 'com_') {
			$guess = $thread->entities[0]->tags[0].'_'.$thread->entities[0]->tags[1];
			if (class_exists($guess))
				$context = $guess;
		} ?>
		<tr title="<?php e($thread->guid); ?>">
			<td><a data-entity="<?php e($thread->guid); ?>" data-entity-context="com_notes_thread"><?php e($thread->guid); ?></a></td>
			<td><?php e(format_date($thread->mdate)); ?></td>
			<td><?php e(format_date($thread->cdate)); ?></td>
			<td><?php echo isset($thread->entities[0]->guid) ? '<a data-entity="'.h($thread->entities[0]->guid).'" data-entity-context="'.h($context).'">'.h($thread->entities[0]->info('name')).'</a>' : ''; ?></td>
			<td><?php echo ($thread->hidden ? 'Yes' : 'No'); ?></td>
			<td><?php echo ($thread->ac->other ? 'everyone' : ($thread->ac->group ? 'my-group' : 'only-me')); ?></td>
			<td><?php e(count($thread->notes)); ?></td>
			<td><a data-entity="<?php e($thread->user->guid); ?>" data-entity-context="user"><?php e($thread->user->info('name')); ?></a></td>
			<td><?php $first_note = reset($thread->notes); e(strlen($first_note['text']) > 100 ? substr($first_note['text'], 0, 100).'...' : $first_note['text']); ?></td>
			<td><?php $last_note = end($thread->notes); ?><a data-entity="<?php e($last_note['user']->guid); ?>" data-entity-context="user"><?php e($last_note['user']->info('name')); ?></a></td>
			<td><?php e(strlen($last_note['text']) > 100 ? substr($last_note['text'], 0, 100).'...' : $last_note['text']); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>