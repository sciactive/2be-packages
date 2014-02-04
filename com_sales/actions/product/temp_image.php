<?php
/**
 * Provide an interface to temporarily stored product images.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/editproduct') && !gatekeeper('com_sales/newproduct') )
	punt_user(null, pines_url('com_sales', 'product/edit', array('image' => $_REQUEST['image'], 'type' => $_REQUEST['type'])));

$_->page->override = true;

if ($_REQUEST['source'] == 'temp')
	$file = $_->uploader->temp($_REQUEST['image']);
else
	$file = $_REQUEST['image'];
if (!$file || !file_exists($file))
	throw new HttpClientException(null, 404);

$image = new Imagick($file);
if (!$image)
	throw new HttpServerException(null, 500);

$options = array();
if ($_REQUEST['options']) {
	$tmp_options = json_decode($_REQUEST['options'], true);
	if ($tmp_options['tmb_method'] == 'crop')
		$options['tmb_method'] = 'crop';
	elseif ($tmp_options['tmb_method'] == 'fit')
		$options['tmb_method'] = 'fit';
	if (is_numeric($tmp_options['h']))
		$options['h'] = $tmp_options['h'];
	if (is_numeric($tmp_options['w']))
		$options['w'] = $tmp_options['w'];
	if (is_numeric($tmp_options['x']))
		$options['x'] = $tmp_options['x'];
	if (is_numeric($tmp_options['y']))
		$options['y'] = $tmp_options['y'];
}

$_->com_sales->process_product_image($image, $_REQUEST['type'], $options);

header('Content-Type: image/png');
header('Content-Disposition: inline; filename="product-image.png"');
$_->page->override_doc("$image");