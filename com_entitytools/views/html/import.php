<?php
/**
 * Confirms that the user really wants to run a benchmark.
 *
 * @package Components\entitytools
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Entity Manager Import';
?>
<script type="text/javascript">
	$_(function(){
		$("#p_muid_form").on("submit", function(){
			if ($("[name=reset_entities]", "#p_muid_form").is(":checked"))
				if (!confirm("Choosing to reset entities will PERMANENTLY DELETE all entities. Are you sure?"))
					return false;
		});
	});
</script>
<form id="p_muid_form" enctype="multipart/form-data" class="pf-form" method="post" action="<?php e(pines_url('com_entitytools', 'import')); ?>">
	<div class="pf-element pf-heading">
		<p>
			Use this feature to import entities from a file made by a 2be
			Entity Manager.
		</p>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Upload File</span>
			<input class="pf-field form-control" type="file" name="entity_import" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Reset Entities</span>
			<input class="pf-field" type="checkbox" name="reset_entities" value="ON" /> Log me out and delete all entities before the import.</label>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
	</div>
</form>