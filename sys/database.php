<?php
/**
 * The lifeblood of the system. Without this, the entire website would just fail to work.
 * Provides the database access.
 * @author Stevan
 *
 */
namespace sys;

require_once "config.php";

class Database extends \PDO {
	private $_prefix;

	/**
	 * @param Config $config
	 * @throws \PDOException
	 */
	public function __construct($config) {
		$this->_prefix = $config->getSetting("database.prefix");

		$hostname = $config->getSetting("database.hostname");
		$username = $config->getSetting("database.username");
		$password = $config->getSetting("database.password");
		$database = $config->getSetting("database.database");
		$driver = $config->getSetting("database.driver");

		try {
			$dsn = "{$driver}:host={$hostname};dbname={$database}";
			parent::__construct($dsn, $username, $password);
		} catch (\PDOException $e) {
			die('ERROR: Cannot connect to database server. Try again later.');
		}
	}

	/**
	 * Creates a table name. Basically it's the table name with the prefix attached.
	 * @param string $table
	 */
	public function tbl($table) {
		return $this->_prefix.$table;
	}
}

?>
