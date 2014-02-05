<?php
/**
 * Lists packages and provides functions to manipulate them.
 *
 * @package Components\packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Packages';
$_->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = (object) json_decode($_SESSION['user']->pgrid_saved_states['com_packager/package/list']);
?>
<script type="text/javascript">
	$_(function(){
		var state_xhr;
		var cur_state = <?php echo (isset($this->pgrid_state) ? json_encode($this->pgrid_state) : '{}');?>;
		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				<?php if (gatekeeper('com_packager/newpackage')) { ?>
				{type: 'button', text: 'New', extra_class: 'picon picon-document-new', selection_optional: true, url: <?php echo json_encode(pines_url('com_packager', 'package/edit')); ?>},
				{type: 'button', text: 'Package(s) Wizard', extra_class: 'picon picon-tools-wizard', selection_optional: true, url: <?php echo json_encode(pines_url('com_packager', 'package/wizard')); ?>},
				<?php } if (gatekeeper('com_packager/editpackage')) { ?>
				{type: 'button', text: 'Edit', extra_class: 'picon picon-document-edit', double_click: true, url: <?php echo json_encode(pines_url('com_packager', 'package/edit', array('id' => '__title__'))); ?>},
				<?php } if (gatekeeper('com_packager/makepackage')) { ?>
				{type: 'button', text: 'Make Package(s)', extra_class: 'picon picon-package-x-generic', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_packager', 'package/make', array('id' => '__title__'))); ?>, delimiter: ','},
				<?php } ?>
				//{type: 'button', text: 'E-Mail', extra_class: 'picon picon-mail-message-new', multi_select: true, url: 'mailto:__col_2__', delimiter: ','},
				{type: 'separator'},
				<?php if (gatekeeper('com_packager/deletepackage')) { ?>
				{type: 'button', text: 'Delete', extra_class: 'picon picon-edit-delete', confirm: true, multi_select: true, url: <?php echo json_encode(pines_url('com_packager', 'package/delete', array('id' => '__title__'))); ?>, delimiter: ','},
				{type: 'separator'},
				<?php } ?>
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
				state_xhr = $.post(<?php echo json_encode(pines_url('com_pgrid', 'save_state')); ?>, {view: "com_packager/package/list", state: cur_state});
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
			<th>Type</th>
			<th>Name</th>
			<th>Author</th>
			<th>Component</th>
			<th>Version</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($this->packages as $package) { ?>
		<tr title="<?php e($package->guid); ?>">
			<td><a data-entity="<?php e($package->guid); ?>" data-entity-context="com_packager_package"><?php e($package->name); ?></a></td>
			<td><?php switch($package->type) {
				case 'component':
					echo 'Component';
					break;
				case 'template':
					echo 'Template';
					break;
				case 'system':
					echo 'System';
					break;
				case 'meta':
					echo 'Meta';
					break;
			} ?></td>
			<td><?php switch($package->type) {
				case 'component':
				case 'template':
					$component = $package->component;
					e($_->info->$component->name);
					break;
				case 'system':
					e($_->info->name);
					break;
				case 'meta':
					e($package->meta['name']);
					break;
			} ?></td>
			<td><?php switch($package->type) {
				case 'component':
				case 'template':
					$component = $package->component;
					e($_->info->$component->author);
					break;
				case 'system':
					e($_->info->author);
					break;
				case 'meta':
					e($package->meta['author']);
					break;
			} ?></td>
			<td><?php switch($package->type) {
				case 'component':
				case 'template':
					e($package->component);
					break;
				case 'system':
					echo 'system';
					break;
				case 'meta':
					echo 'N/A';
					break;
			} ?></td>
			<td><?php switch($package->type) {
				case 'component':
				case 'template':
					$component = $package->component;
					e($_->info->$component->version);
					break;
				case 'system':
					e($_->info->version);
					break;
				case 'meta':
					e($package->meta['version']);
					break;
			} ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>