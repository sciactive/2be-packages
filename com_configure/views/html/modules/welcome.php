<?php
/**
 * Welcome to 2be widget.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Welcome to '.h($_->info->name);
?>
<div class="page-header">
	<h2>Welcome to <?php e($_->info->name); ?> <small>version <?php e($_->info->version); ?></small></h2>
</div>
<p>Congratulations on successfully installing <?php e($_->info->name); ?>
	on your system. <a href="#p_muid_migrating" data-toggle="modal">Are you migrating from another installation?</a></p>
<div class="modal fade" id="p_muid_migrating">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Migrating an Installation</h4>
			</div>
			<div class="modal-body">
				<p>Migrating a 2be installation involves just a few quick steps:</p>
				<div id="p_muid_migrate_steps" class="panel-group">
					<div class="panel panel-default">
						<a class="panel-heading ui-helper-clearfix" href="javascript:void(0);" data-parent="#p_muid_migrate_steps" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
							<big class="panel-title">
								Reinstalling Components and Templates
							</big>
						</a>
						<div class="panel-collapse collapse in">
							<div class="panel-body clearfix">
								You need to reinstall all the components and templates
								you had installed on the previous installation. You can
								do that in the <a href="<?php e(pines_url('com_plaza', 'package/repository')); ?>">2be Plaza</a>.
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<a class="panel-heading ui-helper-clearfix" href="javascript:void(0);" data-parent="#p_muid_migrate_steps" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
							<big class="panel-title">
								Copying Configuration Files
							</big>
						</a>
						<div class="panel-collapse collapse">
							<div class="panel-body clearfix">
								Next you need to copy all your configuration files to
								the new installation. If you are on a Unix or Linux
								system, you can do that while in the installation
								directory with this command:
								<pre>find -L . ./components/ ./templates/ -maxdepth 2 -name "config.php" | xargs tar -czf configfiles.tar.gz</pre>
								Then move the <code>configfiles.tar.gz</code> file to
								this new installation's directory and run this command:
								<pre>tar -xzhf configfiles.tar.gz && rm configfiles.tar.gz</pre>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<a class="panel-heading ui-helper-clearfix" href="javascript:void(0);" data-parent="#p_muid_migrate_steps" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
							<big class="panel-title">
								Copying Media Files
							</big>
						</a>
						<div class="panel-collapse collapse">
							<div class="panel-body clearfix">
								Since media files are all stored in the <code>media</code>
								folder (unless you changed the "Upload Location"
								option), you can simply copy the contents of that folder
								from the old installation.
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<a class="panel-heading ui-helper-clearfix" href="javascript:void(0);" data-parent="#p_muid_migrate_steps" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
							<big class="panel-title">
								Migrating Entity Data
							</big>
						</a>
						<div class="panel-collapse collapse">
							<div class="panel-body clearfix">
								Data in 2be is stored in objects called entities,
								which can be migrated using the Entity Tools component,
								com_entitytools. If you don't already have it installed
								on the old installation, install it. Now go to System ->
								Entity Tools -> Export to export your entities. Then on
								this system, go to <a href="<?php e(pines_url('com_entitytools', 'import')); ?>">Import</a>
								and select the file that the export returned.
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<a class="panel-heading ui-helper-clearfix" href="javascript:void(0);" data-parent="#p_muid_migrate_steps" data-toggle="collapse" data-target=":focus + .panel-collapse" tabindex="0">
							<big class="panel-title">
								Anything Else?
							</big>
						</a>
						<div class="panel-collapse collapse">
							<div class="panel-body clearfix">
								That covers all the standard files and data that
								components use, however there may be other data that
								needs to be migrated. Check the old database for any
								other tables that may need to be migrated. Check the old
								installation folder for other files that may need to be
								copied.
								<br /><br />
								After you're sure there's no more data to copy, you're
								done!
								<div class="picon-32 picon-face-smile" style="background-position: center top; background-repeat: no-repeat; height: 32px; margin-top: 1em;">&nbsp;</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="javascript:void(0);" class="btn btn-primary" data-dismiss="modal">Close</a>
				<a href="http://2be.io/content/page/a-support/" target="_blank" class="btn btn-default">Ask for Help</a>
			</div>
		</div>
	</div>
</div>
<div>
	To help you get started with 2be, here are some important areas in the
	<a href="<?php e(pines_url('com_configure', 'list')); ?>">configuration</a>:
	<h4 style="text-align: center;">Settings and Preferences</h4>
	<dl style="margin-top: 0;">
		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'system'))); ?>">2be Config</a></dt>
		<dd>Main system config includes things like names, default templates and component, timezone, etc.</dd>

		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'com_user'))); ?>">User Manager</a></dt>
		<dd>The user manager provides user and group abilities. You can tune it to work just how you'd like here.</dd>

		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'com_content'))); ?>">CMS</a></dt>
		<dd>The CMS, or Content Management System is what builds the pages in your website. You can set defaults and options here.</dd>

		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'com_timeoutnotice'))); ?>">Timeout Notice</a></dt>
		<dd>The timeout notice will log users out after they've been idle for a while. You can set up its features here.</dd>

		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'com_logger'))); ?>">Logger</a></dt>
		<dd>The logger keeps a log of important things that happen on your website. You can set up how and where it logs information here.</dd>
	</dl>
	<h4 style="text-align: center;">Appearance</h4>
	<dl style="margin-top: 0;">
		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'system'))); ?>">2be Config</a></dt>
		<dd>The main system config lets you choose default templates, which change the whole site's appearance.</dd>

		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'com_bootstrap'))); ?>">Bootstrap</a> and <a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'com_jquery'))); ?>">jQuery</a></dt>
		<dd>Bootstrap provides theming for most of the form inputs, buttons, and other various elements. jQuery UI provides theming for many of the widgets, like the data grids, and the tpl_pines template. Try different combinations of Bootstrap and jQuery UI themes.</dd>

		<dt><a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'tpl_pinescms'))); ?>">Pines CMS Template</a> and <a href="<?php e(pines_url('com_configure', 'edit', array('component' => 'tpl_pines'))); ?>">Pines Template</a></dt>
		<dd>These are the default templates for 2be. You can configure a lot of options for each of them, including changing their layout.</dd>
	</dl>
</div>