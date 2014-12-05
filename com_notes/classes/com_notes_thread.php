<?php
/**
 * com_notes_thread class.
 *
 * @package Components\notes
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A thread.
 *
 * @package Components\notes
 */
class com_notes_thread extends Entity {
	const etype = 'com_notes_thread';
	protected $tags = array('com_notes', 'thread');

	public function __construct($id = 0) {
		if (parent::__construct($id) !== null)
			return;
		// Defaults.
		$this->ac = (object) array('user' => 3, 'group' => 2, 'other' => 2);
		$this->notes = array();
		$this->hidden = false;
	}

	public function info($type) {
		switch ($type) {
			case 'name':
				return "Note Thread $this->guid";
			case 'type':
				return 'note thread';
			case 'types':
				return 'note threads';
			case 'url_view':
				if (gatekeeper('com_notes/seethreads') && isset($this->entities[0]->guid)) {
					$view = $this->entities[0]->info('url_view');
					if ($view)
						return $view;
					$edit = $this->entities[0]->info('url_edit');
					if ($edit)
						return $edit;
				}
				break;
			case 'url_edit':
				if (gatekeeper('com_notes/editthread'))
					return pines_url('com_notes', 'thread/edit', array('id' => $this->guid));
				break;
			case 'url_list':
				if (gatekeeper('com_notes/listthreads'))
					return pines_url('com_notes', 'thread/list');
				break;
			case 'icon':
				return 'picon-view-pim-notes';
		}
		return null;
	}

	/**
	 * Print a form to edit the thread.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_notes', 'thread/form', 'content');
		$module->entity = $this;

		return $module;
	}
}