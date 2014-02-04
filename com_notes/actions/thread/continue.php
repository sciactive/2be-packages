<?php
/**
 * Continue a thread.
 *
 * @package Components\notes
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_notes/continueownthread') && !gatekeeper('com_notes/continuethread') )
	punt_user(null, pines_url('com_notes', 'thread/list'));

$_->page->override = true;

if ($_REQUEST['text'] == '') {
	$_->page->override_doc(json_encode(false));
	return;
}

// Get the thread.
$thread = com_notes_thread::factory((int) $_REQUEST['id']);
if (!isset($thread->guid)) {
	$_->page->override_doc(json_encode(false));
	return;
}

// Check their ability.
if (!gatekeeper('com_notes/continuethread') && !$_SESSION['user']->is($thread->user)) {
	// User doesn't have permission to comment on others' threads.
	$_->page->override_doc(json_encode(false));
	return;
}

// Add the note.
$thread->notes[uniqid()] = array(
	'date' => time(),
	'user' => $_SESSION['user'],
	'text' => $_REQUEST['text']
);

if ($thread->save()) {
	$_->page->override_doc(json_encode(true));
} else {
	$_->page->override_doc(json_encode(false));
}