<?php
/**
 * Provides a form for the user to setup their database.
 *
 * @package Components\mysql
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Database Setup';
?>
<form class="pf-form" method="post" action="<?php e(pines_url('com_mysql', 'setup')); ?>" autocomplete="off">
	<div class="pf-element">
		You have successfully installed the <?php e($_->info->com_mysql->name); ?> in <?php e("{$_->info->name} {$_->info->version}"); ?>. Now you need to configure a database to use.
		The user and database can be created for you by filling out the "Automatic Setup" section below.
	</div>
	<div class="pf-element pf-heading">
		<h3>Database Information</h3>
		<p>Please provide the information <?php e($_->info->name); ?> will use to connect to your database below.</p>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Host</span>
			<span class="pf-note">The hostname or address of your MySQL server.</span>
			<input class="pf-field form-control" type="text" name="host" size="24" value="<?php e($this->host); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">User</span>
			<span class="pf-note">The username to use to connect to your MySQL server.</span>
			<input class="pf-field form-control" type="text" name="user" size="24" value="<?php e($this->user); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Password</span>
			<span class="pf-note">The password to use to connect to your MySQL server.</span>
			<input class="pf-field form-control" type="text" name="password" size="24" value="<?php e($this->password); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Database</span>
			<span class="pf-note">The name of the database to use.</span>
			<input class="pf-field form-control" type="text" name="database" size="24" value="<?php e($this->database); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Table Name Prefix</span>
			<span class="pf-note">The prefix to use when creating new tables.</span>
			<span class="pf-note">You can use this feature to have multiple installations use the same database.</span>
			<input class="pf-field form-control" type="text" name="prefix" size="24" value="<?php e($this->prefix); ?>" /></label>
	</div>
	<div class="pf-element pf-heading">
		<h3>Automatic Setup</h3>
		<p>The user and/or database can be created for you by providing a privileged user's credentials below. Leave blank to skip this option.</p>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Setup User</span>
			<span class="pf-note">The username to use to create the database on your MySQL server.</span>
			<input class="pf-field form-control" type="text" name="setup_user" size="24" value="<?php e($this->setup_user); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Password</span>
			<span class="pf-note">The password to use to create the database on your MySQL server.</span>
			<input class="pf-field form-control" type="password" name="setup_password" size="24" value="<?php e($this->setup_password); ?>" /></label>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
	</div>
</form>