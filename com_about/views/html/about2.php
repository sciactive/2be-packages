<?php
/**
 * Displays 2be's own "about" information.
 *
 * @package Components\about
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = h("About {$_->info->name}");
$this->note = h("Version {$_->info->version}");
?>
<p>
<?php e($_->info->name); ?> is a PHP application
framework from
<a href="http://sciactive.com/" target="_blank">SciActive</a>,
designed to be extensible and easy to use. It allows rapid development, highly
customizable implementation, easy maintenance, and unmatched flexibility.
</p>
<p>
New features can be added by downloading new components, and the look and feel
can be customized by downloading new templates.
<?php e($_->info->name); ?> uses a package manager
called 2be Plaza, which automatically installs any dependencies a component
needs. <?php e($_->info->name); ?> is designed to allow
maximum flexibility for the developer, and provide more than enough tools and
libraries to make development of even very complex systems easy.
<?php e($_->info->name); ?> supports different databases
by using a database abstraction system called an Entity Manager. Choosing the
right database is as simple as installing a new component.
</p>
<div class="btn-group pull-right">
	<a class="btn btn-primary" href="http://2be.io/" target="_blank">2be.io</a>
	<a class="btn btn-default" href="https://github.com/sciactive" target="_blank">2be on GitHub</a>
	<a class="btn btn-default" href="https://twitter.com/2be_io" target="_blank">2be on Twitter</a>
	<a class="btn btn-default" href="http://sciactive.com/" target="_blank">SciActive</a>
</div>
<br style="clear: both; height: 1px;" />