<?php
/**
 * Shows the results from a file uploader test.
 *
 * @package Components\elfinderupload
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'elFinder Uploader Results';
?>
<div class="pf-form">
	<div class="pf-element pf-heading">
		<h4>File: <?php e($this->file); ?></h4>
	</div>
	<div class="pf-element">
		<span class="pf-label">Check Passed</span>
		<span class="pf-note">If the check does not pass, the user is probably trying to hack the system.</span>
		<span class="pf-field"><?php echo ($_->uploader->check($this->file)) ? 'Yes' : 'No'; ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Real Path</span>
		<span class="pf-note">This path can be used in code to manipulate the file.</span>
		<span class="pf-field"><?php $real = $_->uploader->real($this->file); e($real); ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Relative URL</span>
		<span class="pf-note">This path can be used for browser access to the file.</span>
		<span class="pf-field"><?php $url = $_->uploader->url($real); e($url); ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Full URL</span>
		<span class="pf-note">This path can be used for access to the file in email, another server, etc.</span>
		<span class="pf-field"><?php $furl = $_->uploader->url($real, true); e($furl); ?></span>
	</div>
	<div class="pf-element pf-heading">
		<h4>Temp File: <?php e($this->tmpfile); ?></h4>
	</div>
	<div class="pf-element">
		<span class="pf-label">Check Passed</span>
		<span class="pf-note">The check for temp files is a little different.</span>
		<span class="pf-field"><?php echo ($_->uploader->temp($this->tmpfile)) ? 'Yes' : 'No'; ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Real Path</span>
		<span class="pf-note">This path can be used in code to manipulate the file.</span>
		<span class="pf-field"><?php $real = $_->uploader->temp($this->tmpfile); e($real); ?></span>
	</div>
	<div class="pf-element pf-heading">
		<h4>Folder: <?php e($this->folder); ?></h4>
	</div>
	<div class="pf-element">
		<span class="pf-label">Check Passed</span>
		<span class="pf-field"><?php echo ($_->uploader->check($this->folder)) ? 'Yes' : 'No'; ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Real Path</span>
		<span class="pf-field"><?php $real = $_->uploader->real($this->folder); e($real); ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Relative URL</span>
		<span class="pf-field"><?php $url = $_->uploader->url($real); e($url); ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Full URL</span>
		<span class="pf-field"><?php $furl = $_->uploader->url($real, true); e($furl); ?></span>
	</div>
	<fieldset class="pf-group">
		<legend>Multi-File Uploading Result</legend>
		<?php foreach ((array) $this->files as $file) { ?>
		<div class="pf-element pf-heading">
			<h4>File: <?php e($file); ?></h4>
		</div>
		<div class="pf-element">
			<span class="pf-label">Check Passed</span>
			<span class="pf-field"><?php echo ($_->uploader->check($file)) ? 'Yes' : 'No'; ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Real Path</span>
			<span class="pf-field"><?php $real = $_->uploader->real($file); e($real); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Relative URL</span>
			<span class="pf-field"><?php $url = $_->uploader->url($real); e($url); ?></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Full URL</span>
			<span class="pf-field"><?php $furl = $_->uploader->url($real, true); e($furl); ?></span>
		</div>
		<?php } ?>
	</fieldset>
</div>