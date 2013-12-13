<?php
/**
 * Provides a form for the user to edit a employee.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = htmlspecialchars($this->entity->name);
$pines->com_pgrid->load();
?>
<style type="text/css" >
	.p_muid_issue_actions button {
		padding: 0;
	}
	.p_muid_btn {
		display: inline-block;
		width: 16px;
		height: 16px;
	}
</style>
<script type="text/javascript">
	var p_muid_notice;

	pines(function(){
		<?php if (!empty($this->sales)) { ?>
		var cur_defaults = {
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_toolbar: true,
			pgrid_view_height: 'auto',
			pgrid_toolbar_contents: [
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post(<?php echo json_encode(pines_url('system', 'csv')); ?>, {
						filename: 'sale_history',
						content: rows
					});
				}}
			]
		};
		$("#p_muid_sales, #p_muid_returns").pgrid(cur_defaults);
		<?php } ?>
		$("#p_muid_history, #p_muid_issues, #p_muid_paystubs").pgrid({
			pgrid_sort_col: 1,
			pgrid_sort_ord: 'asc',
			pgrid_toolbar: false,
			pgrid_footer: false,
			pgrid_view_height: 'auto'
		});

		p_muid_notice = $.pnotify({
			text: "",
			hide: false,
			closer: false,
			sticker: false,
			history: false,
			animate_speed: 100,
			icon: "ui-icon ui-icon-comment",
			// Setting stack to false causes PNotify to ignore this notice when positioning.
			stack: false,
			after_init: function(pnotify){
				// Remove the notice if the user mouses over it.
				pnotify.mouseout(function(){
					pnotify.pnotify_remove();
				});
			},
			before_open: function(pnotify){
				// This prevents the notice from displaying when it's created.
				pnotify.pnotify({
					before_open: null
				});
				return false;
			}
		});
		$("tbody", "#p_muid_issues").mouseenter(function(){
			if (p_muid_notice.text)
				p_muid_notice.pnotify_display();
		}).mouseleave(function(){
			p_muid_notice.pnotify_remove();
		}).mousemove(function(e){
			p_muid_notice.css({"top": e.clientY+12, "left": e.clientX+12});
		});
		p_muid_notice.com_hrm_issue_update = function(comments){
			if (comments == "<ul><li></li></ul>") {
				p_muid_notice.pnotify_remove();
			} else {
				p_muid_notice.pnotify({text: comments});
				if (!p_muid_notice.is(":visible"))
					p_muid_notice.pnotify_display();
			}
		};
		<?php if (gatekeeper('com_hrm/resolveissue')) { ?>
		// Mark an employee issue as resolved, unresolved or remove it altogether.
		pines.com_hrm_process_issue = function(id, status){
			var comments;
			if (status == 'delete') {
				if (!confirm('Delete Employee Issue?'))
					return;
			} else {
				comments = prompt('Comments:');
				if (comments == null)
					return;
			}
			$.ajax({
				url: <?php echo json_encode(pines_url('com_hrm', 'issue/process')); ?>,
				type: "POST",
				dataType: "html",
				data: {"id": id, "status": status, "comments": comments},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to process this issue:\n"+pines.safe(XMLHttpRequest.status)+": "+pines.safe(textStatus));
				},
				success: function(data){
					if (data == 'Error') {
						pines.error("An error occured while trying to process this issue.");
					} else {
						location.reload(true);
					}
				}
			});
		};
		<?php } ?>
	});
</script>
<div class="pf-form">
	<div id="p_muid_div" class="accordion">
		<?php if (!empty($this->entity->employment_history)) { ?>
		<div class="accordion-group">
			<a class="accordion-heading" href="javascript:void(0);" data-parent="#p_muid_div" data-toggle="collapse" data-target=":focus + .collapse" tabindex="0">
				<big class="accordion-toggle">Employment History</big>
			</a>
			<div class="accordion-body collapse">
				<div class="accordion-inner clearfix">
					<table id="p_muid_history">
						<thead>
							<tr>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->entity->employment_history as $cur_history) { ?>
							<tr>
								<td><?php echo htmlspecialchars(format_date($cur_history[0], 'date_long')); ?></td>
								<td><?php echo htmlspecialchars($cur_history[1]); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } if (!empty($this->issues)) { ?>
		<div class="accordion-group">
			<a class="accordion-heading" href="javascript:void(0);" data-parent="#p_muid_div" data-toggle="collapse" data-target=":focus + .collapse" tabindex="0">
				<big class="accordion-toggle">Issues/Transgressions</big>
			</a>
			<div class="accordion-body collapse">
				<div class="accordion-inner clearfix">
					<table id="p_muid_issues">
						<thead>
							<tr>
								<th>Date</th>
								<th>Issue</th>
								<th>Quantity</th>
								<th>Penalty</th>
								<th>Filed by</th>
								<th>Status</th>
								<?php if (gatekeeper('com_hrm/resolveissue')) { ?>
								<th>Actions</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->issues as $cur_issue) { ?>
							<tr onmouseover="p_muid_notice.com_hrm_issue_update('&lt;ul&gt;&lt;li&gt;'+<?php echo htmlspecialchars(json_encode(implode(array_map('htmlspecialchars', $cur_issue->comments), '</li><li>')), ENT_QUOTES); ?>+'&lt;/li&gt;&lt;/ul&gt;');">
								<td><?php echo htmlspecialchars(format_date($cur_issue->date, 'date_short')); ?></td>
								<td><?php echo htmlspecialchars($cur_issue->issue_type->name); ?></td>
								<td>x<?php echo htmlspecialchars($cur_issue->quantity); ?></td>
								<td>$<?php echo round($cur_issue->issue_type->penalty*$cur_issue->quantity, 2); ?></td>
								<td><?php echo htmlspecialchars($cur_issue->user->name); ?></td>
								<td><?php echo htmlspecialchars($cur_issue->status); ?></td>
								<?php if (gatekeeper('com_hrm/resolveissue')) { ?>
								<td><div class="p_muid_issue_actions">
									<?php if ($cur_issue->status != 'resolved') { ?>
									<button class="btn btn-mini" type="button" onclick="pines.com_hrm_process_issue(<?php echo htmlspecialchars(json_encode("{$cur_issue->guid}")); ?>, 'resolved');" title="Resolve"><span class="p_muid_btn picon picon-flag-yellow"></span></button>
									<?php } else { ?>
									<button class="btn btn-mini" type="button" onclick="pines.com_hrm_process_issue(<?php echo htmlspecialchars(json_encode("{$cur_issue->guid}")); ?>, 'unresolved');" title="Reissue"><span class="p_muid_btn picon picon-flag-red"></span></button>
									<?php } ?>
									<button class="btn btn-mini" type="button" onclick="pines.com_hrm_process_issue(<?php echo htmlspecialchars(json_encode("{$cur_issue->guid}")); ?>, 'delete');" title="Remove"><span class="p_muid_btn picon picon-edit-delete"></span></button>
								</div></td>
								<?php } ?>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } if (!empty($this->sales)) { ?>
		<div class="accordion-group">
			<a class="accordion-heading" href="javascript:void(0);" data-parent="#p_muid_div" data-toggle="collapse" data-target=":focus + .collapse" tabindex="0">
				<big class="accordion-toggle">Sales History</big>
			</a>
			<div class="accordion-body collapse">
				<div class="accordion-inner clearfix">
					<table id="p_muid_sales">
						<thead>
							<tr>
								<th>ID</th>
								<th>Date</th>
								<th>Customer</th>
								<th>First Item</th>
								<th>Price</th>
								<th>Status</th>
								<th>Location</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($this->sales as $cur_sale) { ?>
							<tr title="<?php echo htmlspecialchars($cur_sale->guid); ?>">
								<td><a data-entity="<?php echo htmlspecialchars($cur_sale->guid); ?>" data-entity-context="com_sales_sale"><?php echo htmlspecialchars($cur_sale->id); ?></a></td>
								<td><?php echo htmlspecialchars(format_date($cur_sale->p_cdate)); ?></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_sale->customer->guid); ?>" data-entity-context="com_customer_customer"><?php echo htmlspecialchars($cur_sale->customer->name); ?></a></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_sale->products[0]['entity']->guid); ?>" data-entity-context="com_sales_product"><?php echo htmlspecialchars($cur_sale->products[0]['entity']->name); ?></a></td>
								<td>$<?php echo htmlspecialchars($cur_sale->total); ?></td>
								<td><?php echo htmlspecialchars(ucwords($cur_sale->status)); ?></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_sale->group->guid); ?>" data-entity-context="group"><?php echo htmlspecialchars($cur_sale->group->name); ?></a></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } if (!empty($this->returns)) { ?>
		<div class="accordion-group">
			<a class="accordion-heading" href="javascript:void(0);" data-parent="#p_muid_div" data-toggle="collapse" data-target=":focus + .collapse" tabindex="0">
				<big class="accordion-toggle">Return History</big>
			</a>
			<div class="accordion-body collapse">
				<div class="accordion-inner clearfix">
					<table id="p_muid_returns">
						<thead>
							<tr>
								<th>ID</th>
								<th>Sale ID</th>
								<th>Date</th>
								<th>Customer</th>
								<th>First Item</th>
								<th>Price</th>
								<th>Status</th>
								<th>Location</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($this->returns as $cur_return) { ?>
							<tr title="<?php echo htmlspecialchars($cur_return->guid); ?>">
								<td><a data-entity="<?php echo htmlspecialchars($cur_return->guid); ?>" data-entity-context="com_sales_return"><?php echo htmlspecialchars($cur_return->id); ?></a></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_return->sale->guid); ?>" data-entity-context="com_sales_sale"><?php echo htmlspecialchars($cur_return->sale->id); ?></a></td>
								<td><?php echo htmlspecialchars(format_date($cur_return->p_cdate)); ?></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_return->customer->guid); ?>" data-entity-context="com_customer_customer"><?php echo htmlspecialchars($cur_return->customer->name); ?></a></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_return->products[0]['entity']->guid); ?>" data-entity-context="com_sales_product"><?php echo htmlspecialchars($cur_return->products[0]['entity']->name); ?></a></td>
								<td>$<?php echo htmlspecialchars($cur_return->total); ?></td>
								<td><?php echo htmlspecialchars(ucwords($cur_return->status)); ?></td>
								<td><a data-entity="<?php echo htmlspecialchars($cur_return->group->guid); ?>" data-entity-context="group"><?php echo htmlspecialchars($cur_return->group->name); ?></a></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } if (!empty($this->paystubs)) { ?>
		<div class="accordion-group">
			<a class="accordion-heading" href="javascript:void(0);" data-parent="#p_muid_div" data-toggle="collapse" data-target=":focus + .collapse" tabindex="0">
				<big class="accordion-toggle">Paystubs</big>
			</a>
			<div class="accordion-body collapse">
				<div class="accordion-inner clearfix">
					<table id="p_muid_paystubs">
						<thead>
							<tr>
								<th>From</th>
								<th>To</th>
								<th>Amount</th>
								<th>Penalties</th>
								<th>Bonuses</th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ($this->paystubs as $cur_paystub) {
							foreach ($cur_paystub->payroll as $cur_payment) {
								if ($cur_payment['employee']->guid != $this->entity->guid)
									continue; ?>
							<tr title="<?php echo htmlspecialchars($cur_paystub->guid); ?>">
								<td><?php echo htmlspecialchars(format_date($cur_paystub->start)); ?></td>
								<td><?php echo htmlspecialchars(format_date($cur_paystub->end)); ?></td>
								<td>$<?php echo htmlspecialchars(number_format($cur_payment['total_pay'], 2, '.', '')); ?></td>
								<td>$<?php echo htmlspecialchars(number_format($cur_payment['penalties'], 2, '.', '')); ?></td>
								<td>$<?php echo htmlspecialchars(number_format($cur_payment['bonuses'], 2, '.', '')); ?></td>
							</tr>
						<?php }
						} ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } ?>
		<br class="pf-clearing" />
	</div>
</div>
<?php if (gatekeeper('com_hrm/listemployees')) { ?>
<input class="btn" type="button" onclick="pines.get(<?php echo htmlspecialchars(json_encode(pines_url('com_hrm', 'employee/list', array('employed' => isset($this->entity->terminated) ? 'false' : 'true')))); ?>);" value="&laquo; Employees" />
<?php }