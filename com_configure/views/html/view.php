<?php
/**
 * Display a list of configuration settings.
 *
 * @package Components\configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = h("Viewing Configuration for {$this->entity->name}");
if ($this->entity->per_user) {
	if ($this->entity->user->is_com_configure_condition)
		$this->note = 'For conditional configuration <a data-entity="'.h($this->entity->user->guid).'" data-entity-context="com_configure_condition">'.h($this->entity->user->name).'</a>.';
	elseif ($this->entity->type == 'user')
		$this->note = "For {$this->entity->type} <a data-entity=\"".h($this->entity->user->guid)."\" data-entity-context=\"user\">".h("{$this->entity->user->name} [{$this->entity->user->username}]").'</a>.';
	elseif ($this->entity->type == 'group')
		$this->note = "For {$this->entity->type} <a data-entity=\"".h($this->entity->user->guid)."\" data-entity-context=\"group\">".h("{$this->entity->user->name} [{$this->entity->user->groupname}]").'</a>.';
}
?>
<div class="hero-unit">
	<h1><?php e("{$this->entity->info->name} {$this->entity->info->version}"); ?></h1>
</div>
<form class="pf-form" action="" method="post">
	<?php foreach ($this->entity->get_full_config_array() as $cur_var) { ?>
	<div class="pf-element pf-full-width">
		<span class="pf-label"><?php e($cur_var['cname']); ?></span>
		<span class="pf-note"><?php echo str_replace("\n", '<br />', h($cur_var['description'])); ?></span>
		<div class="pf-group">
			<div class="pf-field">
				<?php if (is_array($cur_var['value'])) {
					echo '<ul>';
					foreach ($cur_var['value'] as $cur_value) {
						echo '<li>'.h(print_r($cur_value, true)).'</li>';
					}
					echo '</ul>';
				} else {
					if (is_bool($cur_var['value']))
						$cur_var['value'] = ($cur_var['value']) ? 'Yes' : 'No';
					e(print_r($cur_var['value'], true));
				} ?>
			</div>
		</div>
	</div>
	<?php } ?>
</form>