<?php
/**
 * Provides a form to fill in manager credentials.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<?php if (!gatekeeper('com_sales/manager')) { ?>
<form class="pf-form" method="post" action="" autocomplete="off">
	<div class="pf-element pf-heading">
		<h3>Manager Login</h3>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Username</span>
			<input class="pf-field form-control" type="text" name="username" size="24" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Password</span>
			<input class="pf-field form-control" type="password" name="password" size="24" /></label>
	</div>
</form>
<?php }