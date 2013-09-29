<?php
/**
 * Stores $_SESSION data in $session allowing us to 
 * manipulate sessions whatever way we want. - Denver
 * @author Stevan
 *
 */
namespace sys\config;

require_once dirname(__FILE__)."/../config.php";

class Session extends \sys\Config
{
	public function __construct()
	{
		session_start();
		parent::__construct($_SESSION);
	}
	
	/**
	 * Replaces the current session with a new one
	 */
	public function restart_session()
	{
		session_destroy();
		session_regenerate_id(true);
		parent::__construct($_SESSION);
	}
	
	/**
	 * Gets the current sessions id
	 * 
	 * @return string
	 */
	public function session_id()
	{
		return session_id();
	}

	public function setSetting($key, $value)
	{
		parent::setSetting($key, $value);
		$_SESSION[$key] = $value; 
	}
	
	public function __unset($key) {
		parent::__unset($key);
		unset ($_SESSION[$key]);
	}
}

?>