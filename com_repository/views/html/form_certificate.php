<?php
/**
 * Provides a form to generate a certificate.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Generate a Certificate';
$this->note = 'All packages will be signed against this new certificate. Any current signatures will become invalid.';
?>
<form class="pf-form" method="post" action="<?php e(pines_url('com_repository', 'savecert')); ?>">
	<div class="pf-element">
		<label><span class="pf-label">Country Name</span>
			<span class="pf-note">2 Letter Code (Ex: US)</span>
			<input class="pf-field" type="text" name="countryName" size="24" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">State or Province Name</span>
			<span class="pf-note">Full Name (Ex: Some-State)</span>
			<input class="pf-field" type="text" name="stateOrProvinceName" size="24" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Locality</span>
			<span class="pf-note">Eg, City (Ex: City)</span>
			<input class="pf-field" type="text" name="localityName" size="24" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Organization Name</span>
			<span class="pf-note">Eg, Company (Ex: Internet Widgits Pty Ltd)</span>
			<input class="pf-field" type="text" name="organizationName" size="24" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Organizational Unit Name</span>
			<span class="pf-note">Eg, Section (Ex: Dev Repository)</span>
			<input class="pf-field" type="text" name="organizationalUnitName" size="24" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Common Name</span>
			<span class="pf-note">Eg, The Repository (Ex: http://pines.example.com/repo/)</span>
			<input class="pf-field" type="text" name="commonName" size="24" value="<?php e($_->config->full_location); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Email Address</span>
			<input class="pf-field" type="email" name="emailAddress" size="24" /></label>
	</div>
	<div class="pf-element pf-heading">
		<h3>Security Options</h3>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Digest Method</span>
			<span class="pf-note">Which digest method to use.</span>
			<select class="pf-field" name="digest_alg">
				<?php foreach (openssl_get_md_methods() as $cur_method) { ?>
				<option value="<?php e($cur_method); ?>"<?php echo ($cur_method == 'sha1') ? ' selected="selected"' : ''; ?>><?php e($cur_method); ?></option>
				<?php } ?>
			</select></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Private Key Bits</span>
			<input class="pf-field" type="text" name="private_key_bits" size="24" value="1024" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Valid Days</span>
			<input class="pf-field" type="text" name="days" size="24" value="365" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Private Key Password</span>
			<span class="pf-note">Leaving this blank makes it easier to sign packages, but provides significantly less security.</span>
			<input class="pf-field" type="text" name="password" size="24" /></label>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
	</div>
</form>