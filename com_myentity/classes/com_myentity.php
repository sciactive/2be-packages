<?php
/**
 * com_myentity class.
 *
 * @package Components\myentity
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_myentity main class.
 *
 * Provides a MySQL based entity manager for WonderPHP.
 *
 * @package Components\myentity
 */
class com_myentity extends component implements entity_manager_interface {
	/**
	 * A cache to make entity retrieval faster.
	 * @access private
	 * @var array
	 */
	private $entity_cache = array();
	/**
	 * A counter for the entity cache to determine the most accessed entities.
	 * @access private
	 * @var array
	 */
	private $entity_count = array();
	/**
	 * Sort case sensitively.
	 * @access private
	 * @var bool
	 */
	private $sort_case_sensitive;
	/**
	 * Parent property to sort by.
	 * @access private
	 * @var string
	 */
	private $sort_parent;
	/**
	 * Property to sort by.
	 * @access private
	 * @var string
	 */
	private $sort_property;

	/**
	 * Remove all copies of an entity from the cache.
	 *
	 * @param int $guid The GUID of the entity to remove.
	 * @access private
	 */
	private function clean_cache($guid) {
		unset($this->entity_cache[$guid]);
	}

	/**
	 * Create entity tables in the database.
	 * 
	 * @param string $etype The entity type to create a table for. If this is blank, the default tables are created.
	 * @return bool True on success, false on failure.
	 */
	private function create_tables($etype = null) {
		global $pines;
		if (isset($etype))
			$etype =  '_'.mysql_real_escape_string($etype, $pines->com_mysql->link);
		if ( !(mysql_query('SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";', $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error());
			return false;
		}
		// Create the entity table.
		$query = sprintf("CREATE TABLE IF NOT EXISTS `%scom_myentity_entities%s` (`guid` bigint(20) unsigned NOT NULL, `tags` text, `varlist` text, `cdate` decimal(18,6) NOT NULL, `mdate` decimal(18,6) NOT NULL, PRIMARY KEY (`guid`), KEY `id_tags` (`tags`(1000)), KEY `id_varlist` (`varlist`(1000))) DEFAULT CHARSET=utf8;",
			$pines->config->com_mysql->prefix,
			$etype);
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		// Create the data table.
		$query = sprintf("CREATE TABLE IF NOT EXISTS `%scom_myentity_data%s` (`guid` bigint(20) unsigned NOT NULL, `name` text NOT NULL, `value` longtext NOT NULL, PRIMARY KEY (`guid`,`name`(255))) DEFAULT CHARSET=utf8;",
			$pines->config->com_mysql->prefix,
			$etype);
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		if (!isset($etype)) {
			// Create the GUID table.
			$query = sprintf("CREATE TABLE IF NOT EXISTS `%scom_myentity_guids` (`guid` bigint(20) unsigned NOT NULL, PRIMARY KEY (`guid`)) DEFAULT CHARSET=utf8;",
				$pines->config->com_mysql->prefix);
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
			// Create the UID table.
			$query = sprintf("CREATE TABLE IF NOT EXISTS `%scom_myentity_uids` (`name` text NOT NULL, `cur_uid` bigint(20) unsigned NOT NULL, PRIMARY KEY (`name`(100))) DEFAULT CHARSET=utf8;",
				$pines->config->com_mysql->prefix);
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
		}
		return true;
	}

	public function delete_entity(&$entity) {
		$class = get_class($entity);
		$return = $this->delete_entity_by_id($entity->guid, $class::etype());
		if ( $return )
			$entity->guid = null;
		return $return;
	}

	public function delete_entity_by_id($guid, $etype = null) {
		global $pines;
		$etype = isset($etype) ? '_'.mysql_real_escape_string($etype, $pines->com_mysql->link) : '';
		$query = sprintf("DELETE e, d FROM `%scom_myentity_entities%s` e LEFT JOIN `%scom_myentity_data%s` d ON e.`guid`=d.`guid` WHERE e.`guid`=%u;",
			$pines->config->com_mysql->prefix,
			$etype,
			$pines->config->com_mysql->prefix,
			$etype,
			(int) $guid);
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		$query = sprintf("DELETE FROM `%scom_myentity_guids` g WHERE g.`guid`=%u;",
			$pines->config->com_mysql->prefix,
			(int) $guid);
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		// Removed any cached versions of this entity.
		if ($pines->config->com_myentity->cache)
			$this->clean_cache($guid);
		return true;
	}

	public function delete_uid($name) {
		if (!$name)
			return false;
		global $pines;
		$query = sprintf("DELETE FROM `%scom_myentity_uids` WHERE `name`='%s';",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link));
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		return true;
	}

	/**
	 * Search through a value for an entity reference.
	 *
	 * @param mixed $value Any value to search.
	 * @param array|entity|int $entity An entity, GUID, or array of either to search for.
	 * @return bool True if the reference is found, false otherwise.
	 */
	private function entity_reference_search($value, $entity) {
		if ((array) $value !== $value || !isset($entity))
			return false;
		// Get the GUID, if the passed $entity is an object.
		if ((array) $entity === $entity) {
			foreach($entity as &$cur_entity) {
				if ((object) $cur_entity === $cur_entity)
					$cur_entity = $cur_entity->guid;
			}
			unset($cur_entity);
		} elseif ((object) $entity === $entity) {
			$entity = array($entity->guid);
		} else {
			$entity = array((int) $entity);
		}
		if ($value[0] == 'pines_entity_reference') {
			return in_array($value[1], $entity);
		} else {
			// Search through multidimensional arrays looking for the reference.
			foreach ($value as $cur_value) {
				if ($this->entity_reference_search($cur_value, $entity))
					return true;
			}
		}
		return false;
	}

	public function export($filename) {
		global $pines;
		$filename = clean_filename((string) $filename);
		if (!$fhandle = fopen($filename, 'w'))
			return false;
		fwrite($fhandle, "# WonderPHP Entity Export\n");
		fwrite($fhandle, "# com_myentity version {$pines->info->com_myentity->version}\n");
		fwrite($fhandle, "# sciactive.com\n");
		fwrite($fhandle, "#\n");
		fwrite($fhandle, "# Generation Time: ".date('r')."\n");
		fwrite($fhandle, "# WonderPHP Version: {$pines->info->version}\n\n");

		fwrite($fhandle, "#\n");
		fwrite($fhandle, "# UIDs\n");
		fwrite($fhandle, "#\n\n");

		// Export UIDs.
		$query = sprintf("SELECT * FROM `%scom_myentity_uids`;",
			$pines->config->com_mysql->prefix);
		if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		$row = mysql_fetch_assoc($result);
		while ($row) {
			$row['name'];
			$row['cur_uid'];
			fwrite($fhandle, "<{$row['name']}>[{$row['cur_uid']}]\n");
			// Make sure that $row is incremented :)
			$row = mysql_fetch_assoc($result);
		}

		fwrite($fhandle, "#\n");
		fwrite($fhandle, "# Entities\n");
		fwrite($fhandle, "#\n\n");

		// Export entities.
		$query = sprintf("SELECT e.*, d.`name` AS `dname`, d.`value` AS `dvalue` FROM `%scom_myentity_entities` e LEFT JOIN `%scom_myentity_data` d ON e.`guid`=d.`guid` ORDER BY e.`guid`;",
			$pines->config->com_mysql->prefix,
			$pines->config->com_mysql->prefix);
		if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		$row = mysql_fetch_assoc($result);
		while ($row) {
			$guid = (int) $row['guid'];
			$tags = explode(',', substr($row['tags'], 1, -1));
			$p_cdate = (float) $row['cdate'];
			$p_mdate = (float) $row['mdate'];
			fwrite($fhandle, "{{$guid}}[".implode(',', $tags)."]\n");
			fwrite($fhandle, "\tp_cdate=".json_encode(serialize($p_cdate))."\n");
			fwrite($fhandle, "\tp_mdate=".json_encode(serialize($p_mdate))."\n");
			if (isset($row['dname'])) {
				// This do will keep going and adding the data until the
				// next entity is reached. $row will end on the next entity.
				do {
					fwrite($fhandle, "\t{$row['dname']}=".json_encode($row['dvalue'])."\n");
					$row = mysql_fetch_assoc($result);
				} while ((int) $row['guid'] === $guid);
			} else {
				// Make sure that $row is incremented :)
				$row = mysql_fetch_assoc($result);
			}
		}
		return fclose($fhandle);
	}

	public function export_print() {
		global $pines;
		$pines->page->override = true;
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=entities.pex;');
		// End all output buffering.
		while (@ob_end_clean());
		echo "# WonderPHP Entity Export\n";
		echo "# com_myentity version {$pines->info->com_myentity->version}\n";
		echo "# sciactive.com\n";
		echo "#\n";
		echo "# Generation Time: ".date('r')."\n";
		echo "# WonderPHP Version: {$pines->info->version}\n\n";

		echo "#\n";
		echo "# UIDs\n";
		echo "#\n\n";

		// Export UIDs.
		$query = sprintf("SELECT * FROM `%scom_myentity_uids`;",
			$pines->config->com_mysql->prefix);
		if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		$row = mysql_fetch_assoc($result);
		while ($row) {
			$row['name'];
			$row['cur_uid'];
			echo "<{$row['name']}>[{$row['cur_uid']}]\n";
			// Make sure that $row is incremented :)
			$row = mysql_fetch_assoc($result);
		}

		echo "#\n";
		echo "# Entities\n";
		echo "#\n\n";

		// Export entities.
		$query = sprintf("SELECT e.*, d.`name` AS `dname`, d.`value` AS `dvalue` FROM `%scom_myentity_entities` e LEFT JOIN `%scom_myentity_data` d ON e.`guid`=d.`guid` ORDER BY e.`guid`;",
			$pines->config->com_mysql->prefix,
			$pines->config->com_mysql->prefix);
		if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		$row = mysql_fetch_assoc($result);
		while ($row) {
			$guid = (int) $row['guid'];
			$tags = explode(',', substr($row['tags'], 1, -1));
			$p_cdate = (float) $row['cdate'];
			$p_mdate = (float) $row['mdate'];
			echo "{{$guid}}[".implode(',', $tags)."]\n";
			echo "\tp_cdate=".json_encode(serialize($p_cdate))."\n";
			echo "\tp_mdate=".json_encode(serialize($p_mdate))."\n";
			if (isset($row['dname'])) {
				// This do will keep going and adding the data until the
				// next entity is reached. $row will end on the next entity.
				do {
					echo "\t{$row['dname']}=".json_encode($row['dvalue'])."\n";
					$row = mysql_fetch_assoc($result);
				} while ((int) $row['guid'] === $guid);
			} else {
				// Make sure that $row is incremented :)
				$row = mysql_fetch_assoc($result);
			}
		}
		return true;
	}

	public function get_entities() {
		global $pines;
		if (!$pines->com_mysql->connected)
			return null;
		// Set up options and selectors.
		$selectors = func_get_args();
		if (!$selectors) {
			$options = $selectors = array();
		} else {
			$options = $selectors[0];
			unset($selectors[0]);
		}
		foreach ($selectors as $key => $selector) {
			if (!$selector || (count($selector) === 1 && in_array($selector[0], array('!&', '!|', '|', '!|'))))
				unset($selectors[$key]);
		}

		$entities = array();
		$class = isset($options['class']) ? $options['class'] : entity;
		if (isset($options['etype'])) {
			$etype_dirty = $options['etype'];
			$etype = '_'.mysql_real_escape_string($etype_dirty, $pines->com_mysql->link);
		} else {
			if (method_exists($class, 'etype')) {
				$etype_dirty = $class::etype();
				$etype = '_'.mysql_real_escape_string($etype_dirty, $pines->com_mysql->link);
			} else {
				$etype_dirty = null;
				$etype = '';
			}
		}
		$sort = isset($options['sort']) ? $options['sort'] : 'guid';
		$count = $ocount = 0;

		// Check if the requested entity is cached.
		if ($pines->config->com_myentity->cache && is_int($selectors[1]['guid'])) {
			// Only safe to use the cache option with no other selectors than a GUID and tags.
			if (
					count($selectors) == 1 &&
					$selectors[1][0] == '&' &&
					(
						(count($selectors[1]) == 2) ||
						(count($selectors[1]) == 3 && isset($selectors[1]['tag']))
					)
				) {
				$entity = $this->pull_cache($selectors[1]['guid'], $class);
				if (isset($entity) && (!isset($selectors[1]['tag']) || $entity->has_tag($selectors[1]['tag']))) {
					$entity->_p_use_skip_ac = (bool) $options['skip_ac'];
					return array($entity);
				}
			}
		}

		$query_parts = array();
		foreach ($selectors as &$cur_selector) {
			$cur_selector_query = '';
			foreach ($cur_selector as $key => &$value) {
				if ($key === 0) {
					$type = $value;
					$type_is_not = ($type == '!&' || $type == '!|');
					$type_is_or = ($type == '|' || $type == '!|');
					continue;
				}
				$clause_not = $key[0] === '!';
				$cur_query = '';
				if ((array) $value !== $value)
					$value = array(array($value));
				elseif ((array) $value[0] !== $value[0])
					$value = array($value);
				// Any options having to do with data only return if the entity has
				// the specified variables.
				foreach ($value as $cur_value) {
					switch ($key) {
						case 'guid':
						case '!guid':
							foreach ($cur_value as $cur_guid) {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`guid`='.(int) $cur_guid;
							}
							break;
						case 'tag':
						case '!tag':
							foreach ($cur_value as $cur_tag) {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'LOCATE(\','.mysql_real_escape_string($cur_tag, $pines->com_mysql->link).',\', e.`tags`)';
							}
							break;
						case 'isset':
						case '!isset':
							if (!($type_is_not xor $clause_not)) {
								foreach ($cur_value as $cur_var) {
									if ( $cur_query )
										$cur_query .= $type_is_or ? ' OR ' : ' AND ';
									$cur_query .= 'LOCATE(\','.mysql_real_escape_string($cur_var, $pines->com_mysql->link).',\', e.`varlist`)';
								}
							}
							break;
						case 'data':
						case '!data':
						case 'strict':
						case '!strict':
							if ($cur_value[0] == 'p_cdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`cdate`='.((float) $cur_value[1]);
								break;
							} elseif ($cur_value[0] == 'p_mdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`mdate`='.((float) $cur_value[1]);
								break;
							}
						case 'gt':
						case '!gt':
							if ($cur_value[0] == 'p_cdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`cdate`>'.((float) $cur_value[1]);
								break;
							} elseif ($cur_value[0] == 'p_mdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`mdate`>'.((float) $cur_value[1]);
								break;
							}
						case 'gte':
						case '!gte':
							if ($cur_value[0] == 'p_cdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`cdate`>='.((float) $cur_value[1]);
								break;
							} elseif ($cur_value[0] == 'p_mdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`mdate`>='.((float) $cur_value[1]);
								break;
							}
						case 'lt':
						case '!lt':
							if ($cur_value[0] == 'p_cdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`cdate`<'.((float) $cur_value[1]);
								break;
							} elseif ($cur_value[0] == 'p_mdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`mdate`<'.((float) $cur_value[1]);
								break;
							}
						case 'lte':
						case '!lte':
							if ($cur_value[0] == 'p_cdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`cdate`<='.((float) $cur_value[1]);
								break;
							} elseif ($cur_value[0] == 'p_mdate') {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= (($type_is_not xor $clause_not) ? 'NOT ' : '' ).'e.`mdate`<='.((float) $cur_value[1]);
								break;
							}
						case 'array':
						case '!array':
						case 'match':
						case '!match':
						case 'ref':
						case '!ref':
							if (!($type_is_not xor $clause_not)) {
								if ( $cur_query )
									$cur_query .= $type_is_or ? ' OR ' : ' AND ';
								$cur_query .= 'LOCATE(\','.mysql_real_escape_string($cur_value[0], $pines->com_mysql->link).',\', e.`varlist`)';
							}
							break;
					}
				}
				if ( $cur_query ) {
					if ($cur_selector_query)
						$cur_selector_query .= $type_is_or ? ' OR ' : ' AND ';
					$cur_selector_query .= $cur_query;
				}
			}
			unset($value);
			if ($cur_selector_query)
				$query_parts[] = $cur_selector_query;
		}
		unset($cur_selector);

		switch ($sort) {
			case 'cdate':
				// This should ensure that two entities with the same cdate get
				// sorted correctly.
				$sort = 'e.`cdate`+(e.`guid`*0.000000000001)';
				break;
			case 'mdate':
				$sort = 'e.`mdate`+(e.`guid`*0.000000000001)';
				break;
			case 'guid':
			default:
				$sort = 'e.`guid`';
				break;
		}
		if ($query_parts) {
			$query = sprintf("SELECT e.`guid`, e.`tags`, e.`cdate`, e.`mdate`, d.`name`, d.`value`, e.`varlist` FROM `%scom_myentity_entities%s` e LEFT JOIN `%scom_myentity_data%s` d ON e.`guid`=d.`guid` HAVING %s ORDER BY %s;",
				$pines->config->com_mysql->prefix,
				$etype,
				$pines->config->com_mysql->prefix,
				$etype,
				'('.implode(') AND (', $query_parts).')',
				$options['reverse'] ? $sort.' DESC' : $sort);
		} else {
			$query = sprintf("SELECT e.`guid`, e.`tags`, e.`cdate`, e.`mdate`, d.`name`, d.`value` FROM `%scom_myentity_entities%s` e LEFT JOIN `%scom_myentity_data%s` d ON e.`guid`=d.`guid` ORDER BY %s;",
				$pines->config->com_mysql->prefix,
				$etype,
				$pines->config->com_mysql->prefix,
				$etype,
				$options['reverse'] ? $sort.' DESC' : $sort);
		}
		if ( !($result = @mysql_query($query, $pines->com_mysql->link)) ) {
			// If the tables don't exist yet, create them.
			if (mysql_errno() == 1146 && $this->create_tables()) {
				if (isset($etype_dirty))
					$this->create_tables($etype_dirty);
				if ( !($result = @mysql_query($query, $pines->com_mysql->link)) ) {
					if (function_exists('pines_error')) pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
					return null;
				}
			} else {
				if (function_exists('pines_error')) pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return null;
			}
		}

		$row = mysql_fetch_row($result);
		while ($row) {
			$guid = (int) $row[0];
			$tags = $row[1];
			$data = array('p_cdate' => (float) $row[2], 'p_mdate' => (float) $row[3]);
			// Serialized data.
			$sdata = array();
			if (isset($row[4])) {
				// This do will keep going and adding the data until the
				// next entity is reached. $row will end on the next entity.
				do {
					$sdata[$row[4]] = $row[5];
					$row = mysql_fetch_row($result);
				} while ((int) $row[0] === $guid);
			} else {
				// Make sure that $row is incremented :)
				$row = mysql_fetch_row($result);
			}
			// Check all conditions.
			$pass_all = true;
			foreach ($selectors as &$cur_selector) {
				$pass = false;
				foreach ($cur_selector as $key => &$value) {
					if ($key === 0) {
						$type = $value;
						$type_is_not = ($type == '!&' || $type == '!|');
						$type_is_or = ($type == '|' || $type == '!|');
						$pass = !$type_is_or;
						continue;
					}
					$clause_not = $key[0] === '!';
					// Check if it doesn't pass any for &, check if it
					// passes any for |.
					foreach ($value as $cur_value) {
						if (($key === 'ref' || $key === '!ref') && isset($sdata[$cur_value[0]])) {
							// If possible, do a quick entity reference check
							// instead of unserializing all the data.
							if ((array) $cur_value[1] === $cur_value[1]) {
								foreach ($cur_value[1] as $cur_entity) {
									if ((object) $cur_entity === $cur_entity) {
										$pass = ((strpos($sdata[$cur_value[0]], "a:3:{i:0;s:22:\"pines_entity_reference\";i:1;i:{$cur_entity->guid};") !== false) xor ($type_is_not xor $clause_not));
										if (!($type_is_or xor $pass)) break;
									} else {
										$pass = ((strpos($sdata[$cur_value[0]], "a:3:{i:0;s:22:\"pines_entity_reference\";i:1;i:{$cur_entity};") !== false) xor ($type_is_not xor $clause_not));
										if (!($type_is_or xor $pass)) break;
									}
								}
							} elseif ((object) $cur_value[1] === $cur_value[1]) {
								$pass = ((strpos($sdata[$cur_value[0]], "a:3:{i:0;s:22:\"pines_entity_reference\";i:1;i:{$cur_value[1]->guid};") !== false) xor ($type_is_not xor $clause_not));
							} else {
								$pass = ((strpos($sdata[$cur_value[0]], "a:3:{i:0;s:22:\"pines_entity_reference\";i:1;i:{$cur_value[1]};") !== false) xor ($type_is_not xor $clause_not));
							}
						} else {
							// Unserialize the data for this variable.
							if (isset($sdata[$cur_value[0]])) {
								$data[$cur_value[0]] = unserialize($sdata[$cur_value[0]]);
								unset($sdata[$cur_value[0]]);
							}
							switch ($key) {
								case 'guid':
								case '!guid':
								case 'tag':
								case '!tag':
									// These are handled by the query.
									$pass = true;
									break;
								case 'isset':
								case '!isset':
									$pass = (isset($data[$cur_value[0]]) xor ($type_is_not xor $clause_not));
									break;
								case 'data':
								case '!data':
									$pass = (($data[$cur_value[0]] == $cur_value[1]) xor ($type_is_not xor $clause_not));
									break;
								case 'strict':
								case '!strict':
									$pass = (($data[$cur_value[0]] === $cur_value[1]) xor ($type_is_not xor $clause_not));
									break;
								case 'array':
								case '!array':
									$pass = (((array) $data[$cur_value[0]] === $data[$cur_value[0]] && in_array($cur_value[1], $data[$cur_value[0]])) xor ($type_is_not xor $clause_not));
									break;
								case 'match':
								case '!match':
									$pass = ((isset($data[$cur_value[0]]) && preg_match($cur_value[1], $data[$cur_value[0]])) xor ($type_is_not xor $clause_not));
									break;
								case 'gt':
								case '!gt':
									$pass = (($data[$cur_value[0]] > $cur_value[1]) xor ($type_is_not xor $clause_not));
									break;
								case 'gte':
								case '!gte':
									$pass = (($data[$cur_value[0]] >= $cur_value[1]) xor ($type_is_not xor $clause_not));
									break;
								case 'lt':
								case '!lt':
									$pass = (($data[$cur_value[0]] < $cur_value[1]) xor ($type_is_not xor $clause_not));
									break;
								case 'lte':
								case '!lte':
									$pass = (($data[$cur_value[0]] <= $cur_value[1]) xor ($type_is_not xor $clause_not));
									break;
								case 'ref':
								case '!ref':
									$pass = ((isset($data[$cur_value[0]]) && (array) $data[$cur_value[0]] === $data[$cur_value[0]] && $this->entity_reference_search($data[$cur_value[0]], $cur_value[1])) xor ($type_is_not xor $clause_not));
									break;
							}
						}
						if (!($type_is_or xor $pass)) break;
					}
					if (!($type_is_or xor $pass)) break;
				}
				unset($value);
				if (!$pass) {
					$pass_all = false;
					break;
				}
			}
			unset($cur_selector);
			if ($pass_all) {
				if ($ocount < $options['offset']) {
					// We must be sure this entity is actually a match before
					// incrementing the offset.
					$ocount++;
					continue;
				}
				if ($pines->config->com_myentity->cache)
					$entity = $this->pull_cache($guid, $class);
				else
					$entity = null;
				if (!isset($entity) || $data['p_mdate'] > $entity->p_mdate) {
					$entity = call_user_func(array($class, 'factory'));
					$entity->guid = $guid;
					$entity->tags = explode(',', substr($tags, 1, -1));
					$entity->put_data($data, $sdata);
					if ($pines->config->com_myentity->cache)
						$this->push_cache($entity, $class);
				}
				$entity->_p_use_skip_ac = (bool) $options['skip_ac'];
				$entities[] = $entity;
				$count++;
				if ($options['limit'] && $count >= $options['limit'])
					break;
			}
		}

		mysql_free_result($result);

		return $entities;
	}

	public function get_entity() {
		// Set up options and selectors.
		$args = func_get_args();
		if (!$args)
			$args = array(array());
		if ((array) $args[0] !== $args[0])
			$args = array(array(), array('&', 'guid' => (int) $args[0]));
		$args[0]['limit'] = 1;
		$entities = call_user_func_array(array($this, 'get_entities'), $args);
		if (!$entities)
			return null;
		return $entities[0];
	}

	public function get_uid($name) {
		if (!$name)
			return null;
		global $pines;
		$query = sprintf("SELECT `cur_uid` FROM `%scom_myentity_uids` WHERE `name`='%s';",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link));
		if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return null;
		}
		$row = mysql_fetch_row($result);
		mysql_free_result($result);
		return isset($row[0]) ? (int) $row[0] : null;
	}

	public function hsort(&$array, $property = null, $parent_property = null, $case_sensitive = false, $reverse = false) {
		// First sort by the requested property.
		$this->sort($array, $property, $case_sensitive, $reverse);
		if (!isset($parent_property))
			return;
		// Now sort by children.
		$new_array = array();
		// Count the children.
		$child_counter = array();
		while ($array) {
			// Look for entities ready to go in order.
			$changed = false;
			foreach ($array as $key => &$cur_entity) {
				// Must break after adding one, so any following children don't go in the wrong order.
				if (!isset($cur_entity->$parent_property) || !$cur_entity->$parent_property->in_array(array_merge($new_array, $array))) {
					// If they have no parent (or their parent isn't in the array), they go on the end.
					$new_array[] = $cur_entity;
					unset($array[$key]);
					$changed = true;
					break;
				} else {
					// Else find the parent.
					$pkey = $cur_entity->$parent_property->array_search($new_array);
					if ($pkey !== false) {
						// And insert after the parent.
						// This makes entities go to the end of the child list.
						$cur_ancestor = $cur_entity->$parent_property;
						while (isset($cur_ancestor)) {
							$child_counter[$cur_ancestor->guid]++;
							$cur_ancestor = $cur_ancestor->$parent_property;
						}
						// Where to place the entity.
						$new_key = $pkey + $child_counter[$cur_entity->$parent_property->guid];
						if (isset($new_array[$new_key])) {
							// If it already exists, we have to splice it in.
							array_splice($new_array, $new_key, 0, array($cur_entity));
							$new_array = array_values($new_array);
						} else {
							// Else just add it.
							$new_array[$new_key] = $cur_entity;
						}
						unset($array[$key]);
						$changed = true;
						break;
					}
				}
			}
			unset($cur_entity);
			if (!$changed) {
				// If there are any unexpected errors and the array isn't changed, just stick the rest on the end.
				$entities_left = array_splice($array, 0);
				$new_array = array_merge($new_array, $entities_left);
			}
		}
		// Now push the new array out.
		$array = $new_array;
	}

	public function import($filename) {
		global $pines;
		$filename = clean_filename((string) $filename);
		if (!$fhandle = fopen($filename, 'r'))
			return false;
		$line = '';
		$data = array();
		while (!feof($fhandle)) {
			$line .= fgets($fhandle, 8192);
			if (substr($line, -1) != "\n")
				continue;
			if (preg_match('/^\s*#/S', $line)) {
				$line = '';
				continue;
			}
			$matches = array();
			if (preg_match('/^\s*{(\d+)}\[([\w,]+)\]\s*$/S', $line, $matches)) {
				// Save the current entity.
				if ($guid) {
					$query = sprintf("REPLACE INTO `%scom_myentity_entities` (`guid`, `tags`, `varlist`, `cdate`, `mdate`) VALUES (%u, '%s', '%s', %F, %F);",
						$pines->config->com_mysql->prefix,
						$guid,
						mysql_real_escape_string(','.$tags.',', $pines->com_mysql->link),
						mysql_real_escape_string(','.implode(',', array_keys($data)).',', $pines->com_mysql->link),
						unserialize($data['p_cdate']),
						unserialize($data['p_mdate']));
					if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
						if (function_exists('pines_error'))
							pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
						return false;
					}
					$query = sprintf("DELETE FROM `%scom_myentity_data` WHERE `guid`=%u;",
						$pines->config->com_mysql->prefix,
						$guid);
					if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
						if (function_exists('pines_error'))
							pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
						return false;
					}
					unset($data['p_cdate'], $data['p_mdate']);
					if ($data) {
						$query = "INSERT INTO `{$pines->config->com_mysql->prefix}com_myentity_data` (`guid`, `name`, `value`) VALUES ";
						foreach ($data as $name => $value) {
							$query .= sprintf("(%u, '%s', '%s'),",
								$guid,
								mysql_real_escape_string($name, $pines->com_mysql->link),
								mysql_real_escape_string($value, $pines->com_mysql->link));
						}
						$query = substr($query, 0, -1).';';
						if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
							if (function_exists('pines_error'))
								pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
							return false;
						}
					}
					$guid = null;
					$tags = array();
					$data = array();
				}
				// Record the new entity's info.
				$guid = (int) $matches[1];
				$tags = $matches[2];
			} elseif (preg_match('/^\s*([\w,]+)\s*=\s*(\S.*\S)\s*$/S', $line, $matches)) {
				// Add the variable to the new entity.
				if ($guid)
					$data[$matches[1]] = json_decode($matches[2]);
			} elseif (preg_match('/^\s*<([^>]+)>\[(\d+)\]\s*$/S', $line, $matches)) {
				// Add the UID.
				$query = sprintf("INSERT INTO `%scom_myentity_uids` (`name`, `cur_uid`) VALUES ('%s', %u) ON DUPLICATE KEY UPDATE `cur_uid`=%u;",
					$pines->config->com_mysql->prefix,
					mysql_real_escape_string($matches[1], $pines->com_mysql->link),
					(int) $matches[2],
					(int) $matches[2]);
				if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
					if (function_exists('pines_error'))
						pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
					return false;
				}
			}
			$line = '';
			// Clear the entity cache.
			$this->entity_cache = array();
		}
		// Save the last entity.
		if ($guid) {
			$query = sprintf("REPLACE INTO `%scom_myentity_entities` (`guid`, `tags`, `varlist`, `cdate`, `mdate`) VALUES (%u, '%s', '%s', %F, %F);",
				$pines->config->com_mysql->prefix,
				$guid,
				mysql_real_escape_string(','.$tags.',', $pines->com_mysql->link),
				mysql_real_escape_string(','.implode(',', array_keys($data)).',', $pines->com_mysql->link),
				unserialize($data['p_cdate']),
				unserialize($data['p_mdate']));
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
			$query = sprintf("DELETE FROM `%scom_myentity_data` WHERE `guid`=%u;",
				$pines->config->com_mysql->prefix,
				$guid);
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
			if ($data) {
				$query = "INSERT INTO `{$pines->config->com_mysql->prefix}com_myentity_data` (`guid`, `name`, `value`) VALUES ";
				unset($data['p_cdate'], $data['p_mdate']);
				foreach ($data as $name => $value) {
					$query .= sprintf("(%u, '%s', '%s'),",
						$guid,
						mysql_real_escape_string($name, $pines->com_mysql->link),
						mysql_real_escape_string($value, $pines->com_mysql->link));
				}
				$query = substr($query, 0, -1).';';
				if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
					if (function_exists('pines_error'))
						pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
					return false;
				}
			}
		}
		return true;
	}

	public function new_uid($name) {
		if (!$name)
			return null;
		global $pines;
		$query = sprintf("SELECT GET_LOCK('%scom_myentity_uids_%s', 10);",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link));
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return null;
		}
		$query = sprintf("INSERT INTO `%scom_myentity_uids` (`name`, `cur_uid`) VALUES ('%s', 1) ON DUPLICATE KEY UPDATE `cur_uid`=`cur_uid`+1;",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link));
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return null;
		}
		$query = sprintf("SELECT `cur_uid` FROM `%scom_myentity_uids` WHERE `name`='%s';",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link));
		if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return null;
		}
		$row = mysql_fetch_row($result);
		mysql_free_result($result);
		$query = sprintf("SELECT RELEASE_LOCK('%scom_myentity_uids_%s');",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link));
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return null;
		}
		return isset($row[0]) ? (int) $row[0] : null;
	}

	public function psort(&$array, $property = null, $parent_property = null, $case_sensitive = false, $reverse = false) {
		// Sort by the requested property.
		if (isset($property)) {
			$this->sort_property = $property;
			$this->sort_parent = $parent_property;
			$this->sort_case_sensitive = $case_sensitive;
			@usort($array, array($this, 'sort_property'));
		}
		if ($reverse)
			$array = array_reverse($array);
	}

	/**
	 * Pull an entity from the cache.
	 *
	 * @param int $guid The entity's GUID.
	 * @param string $class The entity's class.
	 * @return entity|null The entity or null if it's not cached.
	 * @access private
	 */
	private function pull_cache($guid, $class) {
		// Increment the entity access count.
		if (!isset($this->entity_count[$guid]))
			$this->entity_count[$guid] = 0;
		$this->entity_count[$guid]++;
		if (isset($this->entity_cache[$guid][$class]))
			return (clone $this->entity_cache[$guid][$class]);
		return null;
	}

	/**
	 * Push an entity onto the cache.
	 *
	 * @param entity &$entity The entity to push onto the cache.
	 * @param string $class The class of the entity.
	 * @access private
	 */
	private function push_cache(&$entity, $class) {
		global $pines;
		if (!isset($entity->guid))
			return;
		// Increment the entity access count.
		if (!isset($this->entity_count[$entity->guid]))
			$this->entity_count[$entity->guid] = 0;
		$this->entity_count[$entity->guid]++;
		// Check the threshold.
		if ($this->entity_count[$entity->guid] < $pines->config->com_myentity->cache_threshold)
			return;
		// Cache the entity.
		if ((array) $this->entity_cache[$entity->guid] === $this->entity_cache[$entity->guid]) {
			$this->entity_cache[$entity->guid][$class] = clone $entity;
		} else {
			while ($pines->config->com_myentity->cache_limit && count($this->entity_cache) >= $pines->config->com_myentity->cache_limit) {
				// Find which entity has been accessed the least.
				asort($this->entity_count);
				foreach ($this->entity_count as $key => $val) {
					if (isset($this->entity_cache[$key]))
						break;
				}
				// Remove it.
				if (isset($this->entity_cache[$key]))
					unset($this->entity_cache[$key]);
			}
			$this->entity_cache[$entity->guid] = array($class => (clone $entity));
		}
		$this->entity_cache[$entity->guid][$class]->clear_cache();
	}

	public function rename_uid($old_name, $new_name) {
		if (!$old_name || !$new_name)
			return false;
		global $pines;
		$query = sprintf("UPDATE `%scom_myentity_uids` SET `name`='%s' WHERE `name`='%s';",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($new_name, $pines->com_mysql->link),
			mysql_real_escape_string($old_name, $pines->com_mysql->link));
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		return true;
	}

	/**
	 * @todo Check that the big insert query doesn't fail.
	 */
	public function save_entity(&$entity) {
		global $pines;
		// Save the created date.
		if ( !isset($entity->guid) )
			$entity->p_cdate = microtime(true);
		// Save the modified date.
		$entity->p_mdate = microtime(true);
		$data = $entity->get_data();
		$sdata = $entity->get_sdata();
		$varlist = array_merge(array_keys($data), array_keys($sdata));
		$class = get_class($entity);
		$etype_dirty = $class::etype();
		$etype = '_'.mysql_real_escape_string($etype_dirty, $pines->com_mysql->link);
		if ( !isset($entity->guid) ) {
			while (true) {
				$new_id = mt_rand(1, 0x7FFFFFFFFFFFFFFF);
				$query = sprintf("SELECT `guid` FROM `%scom_myentity_guids` WHERE `guid`=%u;",
					$pines->config->com_mysql->prefix,
					$new_id);
				if ( !($result = mysql_query($query, $pines->com_mysql->link)) ) {
					if (function_exists('pines_error'))
						pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
					return null;
				}
				$row = mysql_fetch_row($result);
				mysql_free_result($result);
				if (!isset($row[0]))
					break;
			}
			$entity->guid = $new_id;
			$query = sprintf("INSERT INTO `%scom_myentity_guids` (`guid`) VALUES (%u);",
				$pines->config->com_mysql->prefix,
				$entity->guid);
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
			$query = sprintf("INSERT INTO `%scom_myentity_entities%s` (`guid`, `tags`, `varlist`, `cdate`, `mdate`) VALUES (%u, '%s', '%s', %F, %F);",
				$pines->config->com_mysql->prefix,
				$etype,
				$entity->guid,
				mysql_real_escape_string(','.implode(',', array_diff($entity->tags, array(''))).',', $pines->com_mysql->link),
				mysql_real_escape_string(','.implode(',', $varlist).',', $pines->com_mysql->link),
				(float) $data['p_cdate'],
				(float) $data['p_mdate']);
			if ( !(mysql_query($query, $pines->com_mysql->link))  ) {
				// If the tables don't exist yet, create them.
				if (mysql_errno() == 1146 && $this->create_tables()) {
					if (isset($etype_dirty))
						$this->create_tables($etype_dirty);
					if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
						if (function_exists('pines_error')) pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
						return false;
					}
				} else {
					if (function_exists('pines_error')) pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
					return false;
				}
			}
			unset($data['p_cdate'], $data['p_mdate']);
			$values = array();
			foreach ($data as $name => $value) {
				$values[] = sprintf('(%u, \'%s\', \'%s\')',
					$entity->guid,
					mysql_real_escape_string($name, $pines->com_mysql->link),
					mysql_real_escape_string(serialize($value), $pines->com_mysql->link));
			}
			foreach ($sdata as $name => $value) {
				$values[] = sprintf('(%u, \'%s\', \'%s\')',
					$entity->guid,
					mysql_real_escape_string($name, $pines->com_mysql->link),
					mysql_real_escape_string($value, $pines->com_mysql->link));
			}
			$query = sprintf("INSERT INTO `%scom_myentity_data%s` (`guid`, `name`, `value`) VALUES %s;",
				$pines->config->com_mysql->prefix,
				$etype,
				implode(',', $values));
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
		} else {
			// Removed any cached versions of this entity.
			if ($pines->config->com_myentity->cache)
				$this->clean_cache($entity->guid);
			$query = sprintf("UPDATE `%scom_myentity_entities%s` SET `tags`='%s', `varlist`='%s', `cdate`=%F, `mdate`=%F WHERE `guid`=%u;",
				$pines->config->com_mysql->prefix,
				$etype,
				mysql_real_escape_string(','.implode(',', array_diff($entity->tags, array(''))).',', $pines->com_mysql->link),
				mysql_real_escape_string(','.implode(',', $varlist).',', $pines->com_mysql->link),
				(float) $data['p_cdate'],
				(float) $data['p_mdate'],
				(int) $entity->guid);
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
			$query = sprintf("DELETE FROM `%scom_myentity_data%s` WHERE `guid`=%u;",
				$pines->config->com_mysql->prefix,
				$etype,
				(int) $entity->guid);
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
			unset($data['p_cdate'], $data['p_mdate']);
			$values = array();
			foreach ($data as $name => $value) {
				$values[] = sprintf('(%u, \'%s\', \'%s\')',
					(int) $entity->guid,
					mysql_real_escape_string($name, $pines->com_mysql->link),
					mysql_real_escape_string(serialize($value), $pines->com_mysql->link));
			}
			foreach ($sdata as $name => $value) {
				$values[] = sprintf('(%u, \'%s\', \'%s\')',
					(int) $entity->guid,
					mysql_real_escape_string($name, $pines->com_mysql->link),
					mysql_real_escape_string($value, $pines->com_mysql->link));
			}
			$query = sprintf("INSERT INTO `%scom_myentity_data%s` (`guid`, `name`, `value`) VALUES %s;",
				$pines->config->com_mysql->prefix,
				$etype,
				implode(',', $values));
			if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
				if (function_exists('pines_error'))
					pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
				return false;
			}
		}
		// Cache the entity.
		if ($pines->config->com_myentity->cache) {
			$class = get_class($entity);
			// Replace hook override in the class name.
			if (strpos($class, 'hook_override_') === 0)
				$class = substr($class, 14);
			$this->push_cache($entity, $class);
		}
		return true;
	}

	public function set_uid($name, $value) {
		if (!$name)
			return false;
		global $pines;
		$query = sprintf("INSERT INTO `%scom_myentity_uids` (`name`, `cur_uid`) VALUES ('%s', %u) ON DUPLICATE KEY UPDATE `cur_uid`=%u;",
			$pines->config->com_mysql->prefix,
			mysql_real_escape_string($name, $pines->com_mysql->link),
			(int) $value,
			(int) $value);
		if ( !(mysql_query($query, $pines->com_mysql->link)) ) {
			if (function_exists('pines_error'))
				pines_error('Query failed: ' . mysql_errno() . ': ' . mysql_error() . ($pines->config->com_myentity->show_failures ? ' --- '.$query : ''));
			return false;
		}
		return true;
	}

	public function sort(&$array, $property = null, $case_sensitive = false, $reverse = false) {
		// Sort by the requested property.
		if (isset($property)) {
			$this->sort_property = $property;
			$this->sort_parent = null;
			$this->sort_case_sensitive = $case_sensitive;
			@usort($array, array($this, 'sort_property'));
		}
		if ($reverse)
			$array = array_reverse($array);
	}

	/**
	 * Determine the sort order between two entities.
	 *
	 * @param entity $a Entity A.
	 * @param entity $b Entity B.
	 * @return int Sort order.
	 * @access private
	 */
	private function sort_property($a, $b) {
		$property = $this->sort_property;
		$parent = $this->sort_parent;
		if (isset($parent) && (isset($a->$parent->$property) || isset($b->$parent->$property))) {
			if (!$this->sort_case_sensitive && is_string($a->$parent->$property) && is_string($b->$parent->$property)) {
				$aprop = strtoupper($a->$parent->$property);
				$bprop = strtoupper($b->$parent->$property);
				if ($aprop > $bprop)
					return 1;
				if ($aprop < $bprop)
					return -1;
			} else {
				if ($a->$parent->$property > $b->$parent->$property)
					return 1;
				if ($a->$parent->$property < $b->$parent->$property)
					return -1;
			}
		}
		// If they have the same parent, order them by their own property.
		if (!$this->sort_case_sensitive && is_string($a->$property) && is_string($b->$property)) {
			$aprop = strtoupper($a->$property);
			$bprop = strtoupper($b->$property);
			if ($aprop > $bprop)
				return 1;
			if ($aprop < $bprop)
				return -1;
		} else {
			if ($a->$property > $b->$property)
				return 1;
			if ($a->$property < $b->$property)
				return -1;
		}
		return 0;
	}
}