<?php
/**
 * A view to load PForm.
 *
 * @package Components\pform
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
pines.loadcss("<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform.css' : 'pform.min.css'; ?>");
<?php if ($pines->depend->check('component', 'com_bootstrap')) { ?>
pines.loadcss("<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform-bootstrap.css' : 'pform-bootstrap.min.css'; ?>");
<?php } ?></script>
<noscript>
<link href="<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform.css' : 'pform.min.css'; ?>" type="text/css" media="all" rel="stylesheet" />
<?php if ($pines->depend->check('component', 'com_bootstrap')) { ?>
<link href="<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform-bootstrap.css' : 'pform-bootstrap.min.css'; ?>" type="text/css" media="all" rel="stylesheet" />
<?php } ?>
</noscript>
<!--[if lt IE 8]>
<script type="text/javascript">
pines.loadcss("<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform-ie-lt-8.css' : 'pform-ie-lt-8.min.css'; ?>");</script>
<noscript>
<link href="<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform-ie-lt-8.css' : 'pform-ie-lt-8.min.css'; ?>" type="text/css" media="all" rel="stylesheet" />
</noscript>
<![endif]-->
<!--[if lt IE 6]>
<script type="text/javascript">
pines.loadcss("<?php e($pines->config->location); ?>components/com_pform/includes/<?php echo $pines->config->debug_mode ? 'pform-ie-lt-6.css' : 'pform-ie-lt-6.min.css'; ?>");</script>
<![endif]-->