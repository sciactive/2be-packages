<?php
/**
 * A view to load jQuery UI.
 *
 * @package Pines
 * @subpackage com_jquery
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<link href="<?php echo $pines->config->rela_location; ?>components/com_jquery/includes/jquery-ui/<?php echo $pines->config->com_jquery->theme; ?>/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo $pines->config->rela_location; ?>components/com_jquery/includes/jquery-ui.min.js"></script>