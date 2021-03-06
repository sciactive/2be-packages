<?php
/**
 * Provides a form for shipping info.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Shipping Address';
?>
<script type="text/javascript">
	$_(function(){
		// Address type toggle.
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

		// Fill in address with profile.
		$("#p_muid_use_profile").click(function(){
			$("[name=name]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->name)); ?>).change();
			$("[name=address_type][value=<?php echo (!isset($this->user_address->address_type) || $this->user_address->address_type == 'us') ? 'us' : 'international'; ?>]", "#p_muid_form").attr("checked", true).change();
			$("[name=address_1]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->address_1)); ?>).change();
			$("[name=address_2]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->address_2)); ?>).change();
			$("[name=city]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->city)); ?>).change();
			$("[name=state]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->state)); ?>).change();
			$("[name=zip]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->zip)); ?>).change();
			$("[name=address_international]", "#p_muid_form").val(<?php echo json_encode(h($this->user_address->address_international)); ?>).change();
		});
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_shop', 'checkout/shippingsave')); ?>">
	<div class="pf-element">
		<span class="pf-label">Autofill</span>
		<small><button type="button" class="pf-field btn btn-default" id="p_muid_use_profile">Use My Account Info</button></small>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Name</span>
			<input class="pf-field form-control" type="text" name="name" size="24" value="<?php e(isset($this->address->name) ? $this->address->name : $this->user_address->name); ?>" /></label>
	</div>
	<div class="pf-element">
		<span class="pf-label">Address Type</span>
		<label><input class="pf-field" type="radio" name="address_type" value="us"<?php echo (!isset($this->address->address_type) || $this->address->address_type == 'us') ? ' checked="checked"' : ''; ?> /> US</label>
		<label><input class="pf-field" type="radio" name="address_type" value="international"<?php echo $this->address->address_type == 'international' ? ' checked="checked"' : ''; ?> /> International</label>
	</div>
	<div id="p_muid_address_us" style="display: none;">
		<div class="pf-element">
			<label><span class="pf-label">Address 1</span>
				<input class="pf-field form-control" type="text" name="address_1" size="24" value="<?php e($this->address->address_1); ?>" /></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Address 2</span>
				<input class="pf-field form-control" type="text" name="address_2" size="24" value="<?php e($this->address->address_2); ?>" /></label>
		</div>
		<div class="pf-element">
			<span class="pf-label">City, State</span>
			<input class="pf-field form-control" type="text" name="city" size="15" value="<?php e($this->address->city); ?>" />
			<select class="pf-field form-control" name="state">
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
					) as $key => $cur_state) {
				?><option value="<?php echo $key; ?>"<?php echo $this->address->state == $key ? ' selected="selected"' : ''; ?>><?php echo $cur_state; ?></option><?php
				} ?>
			</select>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Zip</span>
				<input class="pf-field form-control" type="text" name="zip" size="24" value="<?php e($this->address->zip); ?>" /></label>
		</div>
	</div>
	<div id="p_muid_address_international" style="display: none;">
		<div class="pf-element pf-full-width">
			<label><span class="pf-label">Address</span>
				<span class="pf-field pf-full-width">
					<span class="pf-field" style="display: block;">
						<textarea style="width: 100%;" rows="3" cols="35" name="address_international"><?php e($this->address->address_international); ?></textarea>
					</span>
				</span></label>
		</div>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-primary" id="p_muid_submit" type="submit" value="Continue" />
	</div>
</form>