<?php
/**
 * Display a view to show customer history
 *
 * @package Components\loan
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'View Customer History';
$_->icons->load();

$loan_ids = $this->loan_ids;

?>
<style type="text/css">
	#p_muid_form .panel {
		margin-bottom:10px;
	}
	#p_muid_form .panel-heading {
		opacity: .8;
	}
	#p_muid_form .panel-heading:hover {
		color:#fff;
	}
	.p_muid_loading {
		text-align:center;
		min-height: 300px;
		padding-top: 150px;
		font-weight: bold;
		font-size: 20px;
	}
	.ui-dialog.ui-widget {
		z-index: 1037 !important;
	}
</style>
<script type="text/javascript">
	$_(function(){
		setTimeout(function(){
			var form = $('#p_muid_form');
			
			$(".p_muid_interaction_table").pgrid({
				pgrid_toolbar: false,
				pgrid_footer: true,
				pgrid_view_height: 'auto',
				pgrid_sort_col: 2,
				pgrid_sort_ord: 'desc'
			});
			
			if (form.find('.p_muid_interaction_table').length < 2) {
				form.find('.p_muid_loading').hide();
				var grid = form.find('.p_muid_grid');
				grid.fadeIn();
			}
		}, 1000);
		if ($('.history_status').html().length > 0) {
			$('<hr style="margin-bottom:12px;"/>').insertAfter('.history_status');
		}
	});
</script>
<div id="p_muid_form">
	<div class="history_status"></div>
	<?php 
	if (count($loan_ids) > 1) {
		$c = 0;
		?><div class="panel-group" id="p_muid_accordion_parent">
		<?php foreach ($loan_ids as $loan_id) {
		$loan = com_loan_loan::factory((int) $loan_id);
		$customer = $loan->customer;

		if (!isset($customer->guid)) { ?>
			<div class="alert-error panel-heading" style="margin-bottom: 10px; border: 1px solid #fff;"><big class="panel-title"><i class="fa fa-exclamation-circle"></i> Error Finding Customer.</big></div>
			<?php continue;
		} 
		
		$interactions = $_->nymph->getEntities(
				array('class' => com_customer_interaction),
				array('&',
					'ref' => array('customer', $customer->guid),
					'tag' => array('com_customer', 'interaction')
				)
			);
		
		?>
				<div class="panel panel-default">
					<a class="panel-heading" data-parent="#p_muid_accordion_parent" data-toggle="collapse" href="#p_muid_collapse<?php echo $c;?>">
						<big class="panel-title label-info" style="color:#fff;"><?php e($customer->name); ?></big>
					</a>
					<div id="p_muid_collapse<?php echo $c;?>" class="panel-collapse collapse">
						<div class="panel-body clearfix">
							<?php if (empty($interactions)) {
								echo '<i class="fa fa-info-circle"></i> This customer does not have any customer history.';
							} else { ?>
							<table class="p_muid_interaction_table">
								<thead>
									<tr>
										<th>ID</th>
										<th>Created</th>
										<th>Appointment</th>
										<th>Employee</th>
										<th>Interaction</th>
										<th>Status</th>
										<th>Comments</th>
									</tr>
								</thead>
								<tbody>
						<?php foreach ($interactions as $cur_interaction) { ?>
									<tr title="<?php e($cur_interaction->guid); ?>">
										<td><a data-entity="<?php e($cur_interaction->guid); ?>" data-entity-context="com_customer_interaction"><?php e($cur_interaction->guid); ?></a></td>
										<td><?php e(format_date($cur_interaction->cdate, 'date_sort')); ?></td>
										<td><?php e(format_date($cur_interaction->action_date, 'date_sort')); ?></td>
										<td><a data-entity="<?php e($cur_interaction->employee->guid); ?>" data-entity-context="user"><?php e($cur_interaction->employee->name); ?></a></td>
										<td><?php e($cur_interaction->type); ?></td>
										<td><?php echo ucwords($cur_interaction->status); ?></td>
										<td><?php e($cur_interaction->comments); ?></td>
									</tr>
						<?php } ?>
								</tbody>
							</table>
							<?php } ?>
						</div>
					</div>
				</div>
			
	<?php $c++; 
			} ?>
			</div>
	<?php } else {
		$loan = com_loan_loan::factory((int) $loan_ids);
		$customer = $loan->customer;
		if (!isset($customer->guid)) { ?>
			<div class="alert-error panel-heading" style="margin-bottom: 10px; border: 1px solid #fff;"><big class="panel-title"><i class="fa fa-exclamation-circle"></i> Error Finding Customer.</big></div>
			<?php
		} else { 
			$interactions = $_->nymph->getEntities(
				array('class' => com_customer_interaction),
				array('&',
					'ref' => array('customer', $customer->guid),
					'tag' => array('com_customer', 'interaction')
				)
			);
			?>
			<div class="panel-group">
				<div class="panel panel-default">
					<a class="panel-heading" data-toggle="collapse" href="javascript:void(0);">
						<big class="panel-title label-info" style="color:#fff;"><?php e($customer->name); ?></big>
					</a>
					<div class="panel-collapse collapse in">
						<div class="panel-body clearfix">
							<?php if (empty($interactions)) {
								echo '<i class="fa fa-info-circle"></i> This customer does not have any customer history.';
							} else { ?>
							<div class="p_muid_loading"><i class="fa fa-spinner fa-spin"></i> Loading Customer History...</div>
							<div class="p_muid_grid <?php echo (empty($interactions)) ? '' : 'hide'; ?>">
								<table class="p_muid_interaction_table">
									<thead>
										<tr>
											<th>ID</th>
											<th>Created</th>
											<th>Appointment</th>
											<th>Employee</th>
											<th>Interaction</th>
											<th>Status</th>
											<th>Comments</th>
										</tr>
									</thead>
									<tbody>
							<?php foreach ($interactions as $cur_interaction) { ?>
										<tr title="<?php e($cur_interaction->guid); ?>">
											<td><a data-entity="<?php e($cur_interaction->guid); ?>" data-entity-context="com_customer_interaction"><?php e($cur_interaction->guid); ?></a></td>
											<td><?php e(format_date($cur_interaction->cdate, 'date_sort')); ?></td>
											<td><?php e(format_date($cur_interaction->action_date, 'date_sort')); ?></td>
											<td><a data-entity="<?php e($cur_interaction->employee->guid); ?>" data-entity-context="user"><?php e($cur_interaction->employee->name); ?></a></td>
											<td><?php e($cur_interaction->type); ?></td>
											<td><?php echo ucwords($cur_interaction->status); ?></td>
											<td><?php e($cur_interaction->comments); ?></td>
										</tr>
							<?php } ?>
									</tbody>
								</table>
							</div>
						<?php } ?>
						</div>
					</div>
					
				</div>
			</div>
		<?php }
	} ?>
</div>