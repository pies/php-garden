<?php
/**
 * Database: Driver
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
 */

abstract class DatabaseDriver implements DatabaseDriverApi
{
	/**
	 * Connection configuration
	 * 
	 * @var DatabaseConfig
	 */
	protected $config = array();
	
	/**
	 * Connection object
	 *
	 * @var PDO
	 */
	static private $connection = null;

	private function connect () {
		$CFG = $this->config;

		switch ($CFG['driver']){
			case 'sqlite': 
				$dsn = "{$CFG['driver']}:{$CFG['host']}"; break;
			default:
				$dsn = "{$CFG['driver']}:host={$CFG['host']};dbname={$CFG['database']}";
				if (isset($CFG['port'])) $dsn .= ";port={$CFG['port']}";
		}	
		
		try { 
			$this->connection = new PDO($dsn, $CFG['login'], $CFG['password']); 
		}
		catch (PdoException $E) {
			throw new DatabaseError("Could not connect to [{$dsn}] using [{$CFG['login']}] and [{$CFG['password']}]", $CFG, $E);
		}
	}

	public function query ($sql) {
		if (empty($this->connection)) $this->connect();
		return $this->connection->query($sql);

/*		try {
			return $this->connection->query($sql);
		}
		catch (PdoException $E) {
			throw new DatabaseError("Could not query [$sql]", $sql, $E);
		}
*/	}

	function getError () {}
	function getModified () {}
	function getInsertId () {}	
};

?>