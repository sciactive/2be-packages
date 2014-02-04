<?php
/**
 * A view to build an iframe.
 *
 * @package Components\iframe
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<iframe<?php

// Optional Attributes
if (isset($this->align))
	echo ' align="'.h((string) $this->align, ENT_COMPAT, '', false).'"';
if (isset($this->frameborder))
	echo ' frameborder="'.h((int) $this->frameborder, ENT_COMPAT, '', false).'"';
if (isset($this->height))
	echo ' height="'.h((string) $this->height, ENT_COMPAT, '', false).'"';
if (isset($this->longdesc))
	echo ' longdesc="'.h((string) $this->longdesc, ENT_COMPAT, '', false).'"';
if (isset($this->marginheight))
	echo ' marginheight="'.h((string) $this->marginheight, ENT_COMPAT, '', false).'"';
if (isset($this->marginwidth))
	echo ' marginwidth="'.h((string) $this->marginwidth, ENT_COMPAT, '', false).'"';
if (isset($this->name))
	echo ' name="'.h((string) $this->name, ENT_COMPAT, '', false).'"';
if (isset($this->scrolling))
	echo ' scrolling="'.h((string) $this->scrolling, ENT_COMPAT, '', false).'"';
if (isset($this->src))
	echo ' src="'.h((string) $this->src, ENT_COMPAT, '', false).'"';
if (isset($this->width))
	echo ' width="'.h((string) $this->width, ENT_COMPAT, '', false).'"';

// Standard Attributes
if (isset($this->class))
	echo ' class="'.h((string) $this->class, ENT_COMPAT, '', false).'"';
if (isset($this->id))
	echo ' id="'.h((string) $this->id, ENT_COMPAT, '', false).'"';
if (isset($this->style))
	echo ' style="'.h((string) $this->style, ENT_COMPAT, '', false).'"';
if (isset($this->title))
	echo ' title="'.h((string) $this->title, ENT_COMPAT, '', false).'"';

// Extra Attributes
if (isset($this->allowTransparency))
	echo ' allowTransparency="'.h((string) $this->allowTransparency, ENT_COMPAT, '', false).'"';

?>><?php echo $this->icontent; ?></iframe>