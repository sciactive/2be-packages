<?php
/**
 * Setup the database.
 *
 * @package Components\mysql
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (isset($_SESSION['user']) || $_->config->com_mysql->host != 'localhost' || $_->config->com_mysql->user != '2be' || $_->config->com_mysql->password != 'password' || $_->config->com_mysql->database != '2be' || $_->config->com_mysql->prefix != '2be_')
	return;

// Get the provided or default info.
$host = isset($_REQUEST['host']) ? $_REQUEST['host'] : $_->config->com_mysql->host;
$user = isset($_REQUEST['user']) ? $_REQUEST['user'] : $_->config->com_mysql->user;
$password = $_REQUEST['password'];
$database = isset($_REQUEST['database']) ? $_REQUEST['database'] : $_->config->com_mysql->database;
$prefix = isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : $_->config->com_mysql->prefix;
$setup_user = $_REQUEST['setup_user'];
$setup_password = $_REQUEST['setup_password'];

if (isset($_REQUEST['host'])) {
	// The user already filled out the form.
	$pass = true;
	if (!empty($_REQUEST['setup_user'])) {
		// Can the user connect already?
		$can_connect = @mysqli_connect($host, $user, $password);
		if ($can_connect)
			@mysqli_close($can_connect);
		if ($link = @mysqli_connect($host, $setup_user, $setup_password)) {
			// Create the user/database.
			$pass = $pass && @mysqli_query($link, 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";');
			// Find out our hostname.
			$resource = @mysqli_query('SELECT USER();');
			$my_host = mysqli_fetch_row($resource);
			mysqli_free_result($resource);
			$my_host = mysqli_real_escape_string($link, preg_replace('/.*@/', '', $my_host[0]));
			if ($pass && !$can_connect) {
				// Create the user.
				$pass = $pass && @mysqli_query($link, 'CREATE USER \''.mysqli_real_escape_string($link, $user).'\'@\''.$my_host.'\' IDENTIFIED BY \''.mysqli_real_escape_string($link, $password).'\';');
				if ($pass)
					$pass = $pass && @mysqli_query($link, 'GRANT USAGE ON *.* TO \''.mysqli_real_escape_string($link, $user).'\'@\''.$my_host.'\' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;');
			}
			// Create the database.
			if ($pass)
				$pass = $pass && @mysqli_query($link, 'CREATE DATABASE IF NOT EXISTS `'.mysqli_real_escape_string($link, $database).'`;');
			// Grant priveleges to use it.
			if ($pass)
				$pass = $pass && @mysqli_query($link, 'GRANT ALL PRIVILEGES ON `'.mysqli_real_escape_string($link, $database).'`.* TO \''.mysqli_real_escape_string($link, $user).'\'@\''.$my_host.'\';');
			if (!$pass)
				pines_error('User/database could not be created.');
			@mysqli_close($link);
		} else {
			$pass = false;
			pines_error('Can\'t connect to host using setup user: '.mysqli_connect_error());
		}
	}
	if ($pass) {
		// Can the user connect?
		$can_connect = @mysqli_connect($host, $user, $password);
		if ($can_connect) {
			if (@mysqli_select_db($can_connect, $database)) {
				// User can select the DB, so save the config.
				$conf = configurator_component::factory('com_mysql');
				$conf->set_config(array(
					'host' => $host,
					'user' => $user,
					'password' => $password,
					'database' => $database,
					'prefix' => $prefix,
				));
				$conf->save_config();
				pines_redirect(pines_url());
			} else {
				pines_error('Can\'t select database: '.mysqli_error($can_connect));
			}
			@mysqli_close($can_connect);
		} else {
			pines_error('Can\'t connect to host: '.mysqli_connect_error());
		}
	}
}

// Print out the setup form.
$module = new module('com_mysql', 'setup', 'content');
$module->host = $host;
$module->user = $user;
$module->password = $password;
$module->database = $database;
$module->prefix = $prefix;
$module->setup_user = $setup_user;
$module->setup_password = $setup_password;
