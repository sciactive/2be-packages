<?php
/**
 * Shows a pay summary for an individual in the given time period.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Kirk Johnson <kirk@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style type="text/css">
	#p_muid_pay_report .right_text {
		text-align: right;
	}
	#p_muid_pay_report .item_list {
		width: 100%;
		margin: 0;
		text-align: left;
		border-bottom: 1px solid black;
		border-collapse: collapse;
	}
	#p_muid_pay_report .item_list th {
		border-bottom: 1px solid black;
		padding: 2px;
	}
	#p_muid_pay_report .item_list tr td p {
		margin: 0;
	}
</style>
<div id="p_muid_pay_report" class="pf-form">
	<div class="pf-element pf-full-width" style="text-align: center;">
		<h3>Payroll Report</h3>
	</div>
	<div class="pf-element" style="float: right; clear: right;">
		<div><img style="margin: 0;" src="<?php echo is_callable(array($this->employee->group, 'get_logo')) ? h($this->employee->group->get_logo(true)) : ''; ?>" alt="<?php e($pines->config->system_name); ?>" /></div>
		<div><?php
		if ($this->employee->pay_type != 'salary') {
			echo "<strong>Pay Per Hour: $".h(number_format($this->pay_per_hour, 2, '.', ''))."</strong>";
		}
		?></div>
	</div>
	<div class="pf-element" style="padding-bottom: .4em;">
		<strong class="pf-label">Pay Date:</strong>
		<span class="pf-field"><?php e(format_date(time(), 'date_sort')); ?></span>
	</div>
	<div class="pf-element" style="padding-bottom: .4em;">
		<strong class="pf-label">Pay Period:</strong>
		<span class="pf-field"><?php e(format_date($this->start_date, 'date_sort')).'&nbsp&nbsp - &nbsp&nbsp'.h(format_date($this->end_date, 'date_sort')); ?></span>
	</div>
	<div class="pf-element" style="padding-bottom: .4em;">
		<strong class="pf-label">Employee:</strong>
		<span class="pf-field"><?php e($this->employee->name);?></span>
	</div>
	<div class="pf-element" style="padding-bottom: .4em;">
		<strong class="pf-label">Store:</strong>
		<span class="pf-field"><?php e($this->employee->group->name);?></span>
	</div>
	<div class="pf-element">
		<strong class="pf-label">Supervisor:</strong>
		<span class="pf-field"></span>
	</div>
	<?php if ($this->employee->pay_type == 'commission_draw' || $this->hourreport) { ?>
	<div class="pf-element pf-full-width">
		<h3 style="text-align: center;">Sales</h3>
		<table class="item_list">
			<thead>
				<tr>
					<th>Invoice #</th>
					<th>Name</th>
					<th>Sale Date</th>
					<th class="right_text">Sale Amount$</th>
					<th class="right_text"></th>
					<th class="right_text">Commission</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_sales = 0;
				$commission_total=0;
				foreach ($this->sales as $cur_sale) {
					if($cur_sale->product-commission != null)
						$commission = $cur_sale->product->commission;
					else
						$commission = $cur_sale->subtotal * 0.06;
					$total_sales += $cur_sale->subtotal;
					$commission_total += round($commission, 2);
					?>
				<tr>
					<td><?php e($cur_sale->id); ?></td>
					<td><?php e($cur_sale->customer->name); ?></td>
					<td><?php e(format_date($cur_sale->p_cdate)); ?></td>
					<td class="right_text">$<?php e(number_format($cur_sale->subtotal, 2, '.', '')); ?></td>
					<td></td>
					<td class="right_text">$<?php e(number_format($commission, 2, '.', '')); ?></td>
				</tr>
				<?php } ?>
				<?php
					// If this is an hour report don't report the total of
					// commission because they aren't being paid for the
					// commissions on this paycheck.
					if (!$this->hourreport) { ?>
				<tr>
					<td><strong>Total</strong></td>
					<td></td>
					<td></td>
					<td class="right_text"><strong>$<?php e(number_format($total_sales, 2, '.', '')); ?></strong></td>
					<td class="right_text"></td>
					<td class="right_text"><strong>$<?php e(number_format($commission_total, 2, '.', '')); ?></strong></td>
				</tr>
				<?php } if ($this->hourreport) { ?>
				<tr>
					<td><strong>total (Commissions not included on this check):</strong></td>
					<td></td>
					<td></td>
					<td class="right_text"></td>
					<td></td>
					<td class="right_text">$<?php e(number_format($commission_total, 2, '.', '')); ?></td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
	<?php } ?>
	<div class="pf-element pf-full-width">
		<h3 style="text-align: center;">Hourly</h3>
		<table class="item_list">
			<thead>
				<tr>
					<th class="right_text">Type</th>
					<th class="right_text">Hours</th>
					<th class="right_text">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($this->employee->pay_type != 'salary') {
					$pay = ($this->overtime * $this->employee->pay_rate * 1.5) + ($this->reg_hours * $this->employee->pay_rate);
					?>
				<tr>
					<td class="right_text">Regular</td>
					<td class="right_text"><?php e(number_format($this->reg_hours, 2, '.', '')); ?></td>
					<td class="right_text">$<?php e(number_format(($this->reg_hours * $this->employee->pay_rate), 2, '.', '')); ?></td>
				</tr>
				<tr>
					<td class="right_text">Overtime</td>
					<td class="right_text"><?php e(number_format($this->overtime, 2, '.', '')); ?></td>
					<td class="right_text">$<?php e(number_format(($this->overtime * $this->employee->pay_rate * 1.5), 2, '.', '')); ?></td>
				</tr>
				<tr>
					<td><strong>Total Hourly</strong></td>
					<td class="right_text"><?php e(number_format(($this->overtime + $this->reg_hours), 2, '.', ''));?></td>
					<td class="right_text">$<?php e(number_format($pay, 2, '.', ''));?></td>
				</tr>
				<?php } else { ?>
				<tr>
					<td>Salary</td>
					<td class="right_text"></td>
					<td class="right_text">$<?php e(number_format($this->salary, 2, '.', '')); ?></td>
				</tr>
				<tr>
					<td ><strong>Total</strong></td>
					<td class="right_text"></td>
					<td class="right_text">$<?php e(number_format($this->salary, 2, '.', '')); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php if (!$this->hourreport) { ?>
	<div class="pf-element pf-full-width">
		<h3 style="text-align: center;">Bonuses</h3>
		<table class="item_list">
			<thead>
				<tr>
					<th>Type</th>
					<th>Reason</th>
					<th class="right_text">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($this->bonuses as $cur_bonus) { ?>
				<tr>
					<td><?php e($cur_bonus->name);?></td>
					<td><?php e($cur_bonus->comments);?></td>
					<td class="right_text">$<?php e(number_format($cur_bonus->amount, 2, '.', '')); ?></td>
				</tr>
				<?php } ?>
				<tr>
					<td><strong>Total</strong></td>
					<td><strong></strong></td>
					<td class="right_text">$<?php e(number_format($this->bonus_total, 2, '.', '')); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php } ?>
	<div class="pf-element pf-full-width">
		<h3 style="text-align: center;">Reimbursement/Drawback/Deduction</h3>
		<table class="item_list">
			<thead>
				<tr>
					<th>Type</th>
					<th>Reason</th>
					<th class="right_text">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->adjustments as $cur_adjustments) { ?>
				<tr>
					<td><?php e($cur_adjustments->name);?></td>
					<td><?php e($cur_adjustments->comments);?></td>
					<td class="right_text">$<?php e(number_format($cur_adjustments->amount, 2, '.', '')); ?></td>
				</tr>
				<?php } if (!$this->hourreport) { ?>
				<tr>
					<td>Already Paid Adjustment</td>
					<td>Deducted amount for time already paid</td>
					<td class="right_text">$<?php e(number_format($this->adjust, 2, '.', '')); ?></td>
				</tr>
				<tr>
					<td><strong>Total</strong></td>
					<td></td>
					<td class="right_text">$<?php e(number_format(($this->adjustment_total + $this->adjust), 2, '.', '')); ?></td>
				</tr>
				<?php } else { ?>
				<tr>
					<td><strong>Total</strong></td>
					<td></td>
					<td class="right_text">$<?php e(number_format($this->adjustment_total, 2, '.', '')); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="pf-element pf-full-width">
		<div style="float: right; clear: right;">
			<?php
			if (!$this->hourreport) {
				if ($this->employee->pay_type == 'commission_draw') {
					if ($this->commission > $pay)
						echo '<div><strong>COMMISSION</strong></div>';
					else
						echo '<div><strong>DRAW</strong></div>';
				} elseif ($this->employee->pay_type == 'hourly') {
					echo '<div><strong>HOURLY</strong></div';
				} else
					echo '<div><strong>SALARY</strong></div>';
			} else {
				echo '<div><strong>Total</strong></div>';
			}
			?>
			<hr style="clear: both;" />
			<div class="right_text">
				<span>Gross Pay: <?php e($this->total_pay);?></span>
			</div>
		</div>
	</div>
</div>