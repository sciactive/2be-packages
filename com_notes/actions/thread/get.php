<?php
/**
 * Get all the attached threads and return a JSON structure.
 *
 * @package Components\notes
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_notes/seethreads') )
	punt_user(null, pines_url('com_notes', 'thread/list'));

$entity = $_->nymph->getEntity(array('class' => $_REQUEST['context']), array('&', 'guid' => (int) $_REQUEST['id']));
if (!isset($entity->guid)) {
	$_->page->ajax(json_encode(false));
	return;
}

$threads = $_->nymph->getEntities(
		array('class' => com_notes_thread),
		array('&',
			'tag' => array('com_notes', 'thread'),
			'ref' => array('entities', $entity)
		),
		array('!&',
			'strict' => array('hidden', true)
		)
	);
// Order threads by their modification date.
$_->nymph->sort($threads, 'mdate');

$return = array();
foreach ($threads as $cur_thread) {
	$cur_struct = array(
		'guid' => "$cur_thread->guid",
		'date' => format_date($cur_thread->cdate, 'date_short'),
		'user' => $cur_thread->user->name,
		'privacy' => ($cur_thread->ac->other ? 'everyone' : ($cur_thread->ac->group ? 'my-group' : 'only-me')),
		'notes' => array()
	);
	foreach ($cur_thread->notes as $key => $cur_note) {
		$cur_struct['notes'][] = array(
			'key' => $key,
			'date' => format_date($cur_note['date'], 'date_short'),
			'time' => format_date($cur_note['date'], 'time_short'),
			'user' => $cur_note['user']->name,
			'text' => $cur_note['text']
		);
	}
	$return[] = $cur_struct;
}

$_->page->ajax(json_encode($return));