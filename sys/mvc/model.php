<?php

namespace app\Models;

class Model
{
	/**
	 * @var \jimp\system\Database
	 */
	protected $db;
	private $_name;
	
	public function __construct($database)
	{
		$this->db = $database;
		if ($this->_name == null)
		{
			$this->_name = strtolower(basename(get_class($this)));
		}
	}
	
	public function run($query, $params = array())
	{
		$stmt = $this->db->prepare($query);
		$stmt->execute($params);
		return $stmt;
	}
}

?>