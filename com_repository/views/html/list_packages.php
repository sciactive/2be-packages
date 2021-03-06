<?php
/**
 * Lists packages and provides functions to manipulate them.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Packages';
if (isset($this->user))
	$this->title .= ' from Publisher '.h($this->user->username);
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_repository/list_packages']);

?>
<script type="text/javascript">
	$_(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_repository/makeallindices')) { ?>
				{type: 'button', text: 'Refresh Repository', extra_class: 'picon picon-view-refresh', selection_optional: true, url: <?php echo json_encode(pines_url('com_repository', 'makeindices', array('all' => 'true'))); ?>},
				<?php } if (gatekeeper('com_repository/makeindices')) { ?>
				{type: 'button', text: 'Refresh My Index', extra_class: 'picon picon-view-refresh', selection_optional: true, url: <?php echo json_encode(pines_url('com_repository', 'makeindices')); ?>},
				<?php } if (gatekeeper('com_repository/newpackage')) { ?>
				{type: 'button', text: 'Upload Package', extra_class: 'picon picon-document-new', selection_optional: true, url: <?php echo json_encode(pines_url('com_repository', 'uploadpackage')); ?>},
				<?php } if (gatekeeper('com_repository/deletepackage') || gatekeeper('com_repository/deleteallpackage')) { ?>
				{type: 'separator'},
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, url: <?php echo json_encode(pines_url('com_repository', 'deletepackage', array('pub' => '__col_2__', 'p' => '__title__', 'v' => '__col_5__'))); ?>, delimiter: ','},
				<?php } ?>
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					$_.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'packages',
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
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_repository/list_packages", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		$("#p_muid_grid").pgrid(cur_options);
	});
</script>
<table id="p_muid_grid">
	<thead>
		<tr>
			<th>Package</th>
			<th>Publisher</th>
			<th>Name</th>
			<th>Author</th>
			<th>Version</th>
			<th>Type</th>
			<th>MD5 Hash</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->index as $package) { ?>
		<tr title="<?php e($package['package']); ?>">
			<td><?php e($package['package']); ?></td>
			<td><?php e($package['publisher']); ?></td>
			<td><?php e($package['name']); ?></td>
			<td><?php e($package['author']); ?></td>
			<td><?php e($package['version']); ?></td>
			<td><?php switch($package['type']) {
				case 'component':
					echo 'Component Package';
					break;
				case 'template':
					echo 'Template Package';
					break;
				case 'system':
					echo 'System Package';
					break;
				case 'meta':
					echo 'Meta Package';
					break;
			} ?></td>
			<td><?php e($package['md5']); ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>