<?php
/**
 * A view to build a style.
 *
 * @package Components\istyle
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style<?php

// Required Attributes
if (isset($this->type))
	echo ' type="'.h((string) $this->type, ENT_COMPAT, '', false).'"';
else
	echo ' type="text/css"';

// Optional Attributes
if (isset($this->media))
	echo ' media="'.h((int) $this->media, ENT_COMPAT, '', false).'"';

// Standard Attributes
if (isset($this->dir))
	echo ' dir="'.h((string) $this->dir, ENT_COMPAT, '', false).'"';
if (isset($this->lang))
	echo ' lang="'.h((string) $this->lang, ENT_COMPAT, '', false).'"';
if (isset($this->title))
	echo ' title="'.h((string) $this->title, ENT_COMPAT, '', false).'"';
if (isset($this->xml_lang))
	echo ' xml:lang="'.h((string) $this->xml_lang, ENT_COMPAT, '', false).'"';
?>><?php echo ($this->preserve_tags === 'true') ? $this->icontent : strip_tags($this->icontent); ?></style>