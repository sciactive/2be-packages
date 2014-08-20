<?php
/**
 * Upload a temporary file.
 *
 * @package Components\elfinderupload
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_FILES['file'] || $_FILES['file']['error'] != 0)
	throw new HttpClientException(null, 400);

if ($_->config->com_elfinder->upload_check && !in_array($_FILES['file']['type'], $_->config->com_elfinder->upload_allow))
	throw new HttpClientException(null, 415);

$dir = $_SESSION['elfinder_request_id'][(int) $_REQUEST['request_id']];
if (!isset($dir)) {
	pines_session('write');
	$dir = $_SESSION['elfinder_request_id'][(int) $_REQUEST['request_id']] = uniqid('2be_upload_');
	pines_session('close');
}

$tmp = sys_get_temp_dir().'/'.$dir.'/';
if (!is_dir($tmp))
	mkdir($tmp, 0700, true);

$filename = $tmp.clean_filename($_FILES['file']['name']);
if (!move_uploaded_file($_FILES['file']['tmp_name'], $filename))
	throw new HttpClientException(null, 500);

$result = array(
	'name' => $_FILES['file']['name'],
	'path' => $_REQUEST['request_id'].'/'.$_FILES['file']['name'],
	'url' => $_REQUEST['request_id'].'/'.$_FILES['file']['name'],
	'mime' => $_FILES['file']['type']
);


$_->page->ajax(json_encode($result));
