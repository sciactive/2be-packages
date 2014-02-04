<?php
/**
 * Provide an image preview of a PDF page.
 *
 * @package Components\pdf
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!gatekeeper())
	punt_user(null, pines_url('com_pdf', 'image', $_GET));

$_->page->override = true;

$file = 'media/pdf/'.clean_filename($_REQUEST['file']);
$pdfpage = intval($_REQUEST['page']) - 1;

/* Determine PDF's size */
/**
 * Require the TCPDF class.
 */
require_once('components/com_pdf/includes/tcpdf/tcpdf.php');
/**
 * Require the FPDI class.
 */
require_once('components/com_pdf/includes/fpdi/fpdi.php');

$pdf = new FPDI();
$pagecount = $pdf->setSourceFile($file);
/* End Determine PDF's size */

if ($pdfpage >= 0 && $pdfpage < $pagecount) {
	$tplidx = $pdf->importPage($pdfpage + 1);
	$pagesize = $pdf->getTemplatesize($tplidx);
	$im = new Imagick("{$file}[$pdfpage]");

	$im->setImageFormat('png');

	$output = $im->getimageblob();
	header('Content-Type: image/png');
	$_->page->override_doc($output);
} else {
	header('Content-Type: text/plain');
	$_->page->override_doc('Invalid file or page.');
}