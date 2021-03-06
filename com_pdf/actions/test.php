<?php
/**
 * Provide a test form for the display editors.
 *
 * @package Components\pdf
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('system/all') )
	punt_user(null, pines_url('com_pdf', 'test'));

$module = new module('com_pdf', 'test', 'content');

// This is an example display entity.
$displays = com_pdf_displays::factory();
$displays->pdf_file = 'blank.pdf';
$displays->pdf_dl_filename = 'test.pdf';
$displays->pdf_title = 'PDF Test';
$displays->pdf_author = 'Person McAuthor';
$displays->pdf_creator = '2be';
$displays->pdf_subject = 'A PDF generator test.';
$displays->pdf_keywords = 'test';
// This is an example display setting. The user will be able to edit this.
// These displays will be filled out with the variable named 'favfood' on the
// entity passed to the render() function on the display entity.
// Check out testprint.pdf to see this being done.
$displays->displays['favfood'] = json_decode('[{"page":1,"left":0.393790849673,"top":0.0681003584229,"width":0.566993464052,"height":0.0227001194743,"overflow":false,"bold":true,"italic":true,"fontfamily":"Courier","fontsize":12,"fontcolor":"black","addspacing":false,"border":false,"letterspacing":"normal","wordspacing":"normal","textalign":"left","textdecoration":"none","texttransform":"none","direction":"ltr"}]');
// This will set up any pform fields with the 'display_edit' class to be
// editabled displays.
$displays->load_editors();