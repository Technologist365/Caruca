<?php
/**
 * Session control mechanism. We rarely use this. Infact,
 * I've been using this system for a number of years now 
 * and I've never once used it.
 * @author Stevan
 *
 */
namespace sys\database;
require_once dirname(__FILE__)."/../config/session.php";

class Session extends \sys\config\Session
{
	/**
	 * @var \jimp\system\Database
	 */
	private $_database;
	private $_table;
	
	private $_save_path;
	private $_session_name;
	
	public function __construct($database, $table = "core_sessions")
	{
		$this->_database = $database;
		$this->_table    = $table;
		session_set_save_handler(
			array($this, "open"),
			array($this, "close"),
			array($this, "read"),
			array($this, "write"),
			array($this, "destroy"),
			array($this, "gc")
		);
		parent::__construct();
	}
	
	public function open($save_path, $session_name)
	{
		$this->_save_path    = $save_path;
		$this->_session_name = $session_name;
		return true;
	}
	
	public function close()
	{
		return true;
	}
	
	public function read($id)
	{
		$stmt = $this->_database->prepare("select session_content from {$this->_table} where session_name=:id");
		$stmt->execute(array(":id"=>$id));
		$value = $stmt->fetch();
		return $value['session_content'];
	}
	
	public function write($id, $data)
	{
		$stmt = $this->_database->prepare("select count(*) as \"n\" from {$this->_table} where session_name=:session_name");
		$stmt->execute(array(":session_name"=>$id));
		$result = $stmt->fetch();
		if ($result['n'] == 0)
		{
			$stmt = $this->_database->prepare("insert into {$this->_table} (session_name, session_content, last_access) values (:id, :data, transaction_timestamp())");
		} else {
			$stmt = $this->_database->prepare("update {$this->_table} set session_content=:data, last_access=transaction_timestamp() where session_name=:id");
		}
		$stmt->execute(array(":data"=>$data, ":id"=>$id));
		
		return true;
	}
	
	public function destroy($id)
	{
		$stmt = $this->_database->prepare("delete from {$this->_table} where session_name=:id");
		$stmt->execute(array(":id"=>$id));
		return true;
	}
	
	public function gc($maxlifetime)
	{
		$stmt = $this->_database->prepare("delete from {$this->_table} where last_access < (now() - INTERVAL '{$maxlifetime} SECONDS')");
		$stmt->execute();
	}
}

?>