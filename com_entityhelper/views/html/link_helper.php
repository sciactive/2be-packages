<?php
/**
 * A view to load the entity helper.
 *
 * @package Components\entityhelper
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style type="text/css">
a[data-entity] {
cursor: help;
}
</style>
<script type="text/javascript">
$_.entity_helper_url = <?php echo json_encode(pines_url('com_entityhelper', 'helper')); ?>;
$_(function(){
$("body").on("click", "a[data-entity]", function(){
var e = this;
$_.loadjs("<?php e($_->config->location); ?>components/com_entityhelper/includes/entityhelper.js");
$_(function(){$_.entity_helper(e);});
});
});
</script>