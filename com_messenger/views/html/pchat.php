<?php
/**
 * A view to load pchat.
 *
 * @package Components\messenger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$_->icons->load();
$_->com_soundmanager->load();

$remote_server = !$_->config->com_messenger->use_proxy && substr($_->config->com_messenger->xmpp_bosh_url, 0, 1) !== '/';

?>
<script type="text/javascript">
<?php if ($remote_server) { ?>
pines.loadjs("<?php e($_->config->location); ?>components/com_messenger/includes/flxhr/flXHR.js");
<?php } ?>
pines.loadjs("<?php e($_->config->location); ?>components/com_messenger/includes/strophejs-latest/strophe.js");
<?php if ($remote_server) { ?>
pines.loadjs("<?php e($_->config->location); ?>components/com_messenger/includes/strophejs-latest/plugins/strophe.flxhr.js");
<?php } ?>
pines.loadjs("<?php e($_->config->location); ?>components/com_messenger/includes/strophejs-latest/plugins/strophe.roster.js");
pines.loadjs("<?php e($_->config->location); ?>components/com_messenger/includes/strophejs-latest/plugins/strophe.blocking.js");
pines.loadjs("<?php e($_->config->location); ?>components/com_messenger/includes/pchat/jquery.pchat.js");
pines.loadcss("<?php e($_->config->location); ?>components/com_messenger/includes/pchat/jquery.pchat.default.css");
</script>