<?php
/**
 * Provides a form for the user to edit a company.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New Company' : 'Editing ['.h($this->entity->name).']';
$this->note = 'Provide company profile details in this form.';
?>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_customer', 'company/save')); ?>">
	<?php if (isset($this->entity->guid)) { ?>
	<div class="date_info" style="float: right; text-align: right;">
		<?php if (isset($this->entity->user)) { ?>
		<div>User: <span class="date"><?php e("{$this->entity->user->name} [{$this->entity->user->username}]"); ?></span></div>
		<div>Group: <span class="date"><?php e("{$this->entity->group->name} [{$this->entity->group->groupname}]"); ?></span></div>
		<?php } ?>
		<div>Created: <span class="date"><?php e(format_date($this->entity->p_cdate, 'full_short')); ?></span></div>
		<div>Modified: <span class="date"><?php e(format_date($this->entity->p_mdate, 'full_short')); ?></span></div>
	</div>
	<?php } ?>
	<div class="pf-element">
		<label><span class="pf-label">Company Name</span>
			<input class="pf-field" type="text" name="name" size="24" value="<?php e($this->entity->name); ?>" /></label>
	</div>
	<div class="pf-element">
		<script type="text/javascript">
			$_(function(){
				var address_us = $("#p_muid_address_us");
				var address_international = $("#p_muid_address_international");
				$("#p_muid_form [name=address_type]").change(function(){
					var address_type = $(this);
					if (address_type.is(":checked") && address_type.val() == "us") {
						address_us.show();
						address_international.hide();
					} else if (address_type.is(":checked") && address_type.val() == "international") {
						address_international.show();
						address_us.hide();
					}
				}).change();
			});
		</script>
		<span class="pf-label">Address Type</span>
		<label><input class="pf-field" type="radio" name="address_type" value="us"<?php echo ($this->entity->address_type == 'us') ? ' checked="checked"' : ''; ?> /> US</label>
		<label><input class="pf-field" type="radio" name="address_type" value="international"<?php echo $this->entity->address_type == 'international' ? ' checked="checked"' : ''; ?> /> International</label>
	</div>
	<div id="p_muid_address_us" style="display: none;">
		<div class="pf-element">
			<label><span class="pf-label">Address 1</span>
				<input class="pf-field" type="text" name="address_1" size="24" value="<?php e($this->entity->address_1); ?>" /></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Address 2</span>
				<input class="pf-field" type="text" name="address_2" size="24" value="<?php e($this->entity->address_2); ?>" /></label>
		</div>
		<div class="pf-element">
			<span class="pf-label">City, State</span>
			<input class="pf-field" type="text" name="city" size="15" value="<?php e($this->entity->city); ?>" />
			<select class="pf-field" name="state">
				<option value="">None</option>
				<?php foreach (array(
						'AL' => 'Alabama',
						'AK' => 'Alaska',
						'AZ' => 'Arizona',
						'AR' => 'Arkansas',
						'CA' => 'California',
						'CO' => 'Colorado',
						'CT' => 'Connecticut',
						'DE' => 'Delaware',
						'DC' => 'DC',
						'FL' => 'Florida',
						'GA' => 'Georgia',
						'HI' => 'Hawaii',
						'ID' => 'Idaho',
						'IL' => 'Illinois',
						'IN' => 'Indiana',
						'IA' => 'Iowa',
						'KS' => 'Kansas',
						'KY' => 'Kentucky',
						'LA' => 'Louisiana',
						'ME' => 'Maine',
						'MD' => 'Maryland',
						'MA' => 'Massachusetts',
						'MI' => 'Michigan',
						'MN' => 'Minnesota',
						'MS' => 'Mississippi',
						'MO' => 'Missouri',
						'MT' => 'Montana',
						'NE' => 'Nebraska',
						'NV' => 'Nevada',
						'NH' => 'New Hampshire',
						'NJ' => 'New Jersey',
						'NM' => 'New Mexico',
						'NY' => 'New York',
						'NC' => 'North Carolina',
						'ND' => 'North Dakota',
						'OH' => 'Ohio',
						'OK' => 'Oklahoma',
						'OR' => 'Oregon',
						'PA' => 'Pennsylvania',
						'RI' => 'Rhode Island',
						'SC' => 'South Carolina',
						'SD' => 'South Dakota',
						'TN' => 'Tennessee',
						'TX' => 'Texas',
						'UT' => 'Utah',
						'VT' => 'Vermont',
						'VA' => 'Virginia',
						'WA' => 'Washington',
						'WV' => 'West Virginia',
						'WI' => 'Wisconsin',
						'WY' => 'Wyoming',
						'AA' => 'Armed Forces (AA)',
						'AE' => 'Armed Forces (AE)',
						'AP' => 'Armed Forces (AP)'
					) as $key => $cur_state) { ?>
				<option value="<?php echo $key; ?>"<?php echo $this->entity->state == $key ? ' selected="selected"' : ''; ?>><?php echo $cur_state; ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Zip</span>
				<input class="pf-field" type="text" name="zip" size="24" value="<?php e($this->entity->zip); ?>" /></label>
		</div>
	</div>
	<div id="p_muid_address_international" style="display: none;">
		<div class="pf-element pf-full-width">
		<label><span class="pf-label">Address</span>
			<span class="pf-group pf-full-width">
				<span class="pf-field" style="display: block;">
					<textarea style="width: 100%;" rows="3" cols="35" name="address_international"><?php e($this->entity->address_international); ?></textarea>
				</span>
			</span></label>
		</div>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Email</span>
			<input class="pf-field" type="email" name="email" size="24" value="<?php e($this->entity->email); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Phone Number</span>
			<input class="pf-field" type="tel" name="phone" size="24" value="<?php e(format_phone($this->entity->phone)); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Fax</span>
			<input class="pf-field" type="tel" name="fax" size="24" value="<?php e(format_phone($this->entity->fax)); ?>" onkeyup="this.value=this.value.replace(/\D*0?1?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d)?\D*(\d*)\D*/, '($1$2$3) $4$5$6-$7$8$9$10 x$11').replace(/\D*$/, '');" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Website</span>
			<span class="pf-note">ex: http://www.website.com</span>
			<input class="pf-field" type="url" name="website" size="24" value="<?php e($this->entity->website); ?>" /></label>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_customer', 'company/list'))); ?>);" value="Cancel" />
	</div>
</form>