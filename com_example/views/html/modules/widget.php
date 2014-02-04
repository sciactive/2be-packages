<?php
/**
 * A foobar widget.
 *
 * @package Components\example
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

if (isset($this->id)) {
	$this->entity = com_example_foobar::factory((int) $this->id);
	$this->title = h($this->entity->name);
} else {
	$this->title = 'Foobar Widget';
}
?>
<div>
	<?php
	if (!isset($this->id))
		echo 'This widget has not been configured yet.';
	elseif (!isset($this->entity))
		echo 'Couldn\'t access requested foobar.';
	else
		echo $this->entity->description;
	?>
</div>