<?php
/**
 * com_user's information.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'User Manager',
	'author' => 'SciActive',
	'version' => '1.2.0dev',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'services' => array('user_manager'),
	'short_description' => 'Entity based user manager',
	'description' => 'Provides and manages users, groups, and abilities (access control). Uses an entity manager as a storage backend.',
	'depend' => array(
		'core' => '<3',
		'service' => 'uploader&icons',
		'component' => 'com_mailer&com_jquery&com_bootstrap&com_pgrid&com_markdown&com_pnotify&com_pform', //&com_jstree
		'package' => 'com_user-data'
	),
	'recommend' => array(
		'component' => 'com_modules'
	),
	'abilities' => array(
		array('login', 'Log In', 'User can log in to the system.'),
		array('self', 'Change Info', 'User can change his own information. Email address changes may be subject to verification.'),
		array('listusers', 'List Users', 'User can see users.'),
		array('newuser', 'Create Users', 'User can create new users.'),
		array('edituser', 'Edit Users', 'User can edit other users. Email address changes take place immediately. Can verify unverified email addresses.'),
		array('deleteuser', 'Delete Users', 'User can delete other users.'),
		array('enabling', 'Manage Enabling', 'User can enable and disable users and groups.'),
		array('usernames', 'Manage Usernames', 'User can change usernames and groupnames.'),
		array('passwords', 'Manage Passwords', 'User can change other users\' passwords.'),
		array('abilities', 'Manage Abilities', 'Let user manage other users\' and his own abilities.'),
		array('assignpin', 'Assign PIN', 'User can assign PIN codes for users.'),
		array('conditions', 'Manage Conditions', 'Let user manage user and group conditions.'),
		array('listgroups', 'List Groups', 'User can see groups.'),
		array('newgroup', 'Create Groups', 'User can create new groups.'),
		array('editgroup', 'Edit Groups', 'User can edit other groups.'),
		array('deletegroup', 'Delete Groups', 'User can delete other groups.'),
		array('assigngroup', 'Assign Groups', 'User can assign users to groups, possibly granting them more abilities.'),
		array('defaultgroups', 'Change Default Groups', 'User can change which groups will be assigned to new users.')
	),
);