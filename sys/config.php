<?php
/**
 * Allows us to $var->setSetting and $var->getSetting. This is heavily used
 * within the system and it's a very handy class. - Denver
 * @author Stevan
 *
 */
namespace sys;

class Config
{
	private $_config;
	
	/**
	 * Initial constructor. Stores the config passed to it for easy
	 * retrieval
	 * 
	 * @param array $config
	 */
	public function __construct($config)
	{
		$this->_config = $config;
	}
	
	/**
	 * Fetches a setting from the config passed at initialisation.
	 * 
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public function getSetting($key)
	{
		if (!isset($this->_config[$key])) {
			return null;
		}
		return $this->_config[$key];
	}
	
	public function setSetting($key, $value)
	{
		$this->_config[$key] = $value;
	}
	
	public function __unset($key) {
		unset ($this->_config[$key]);
	}
}

?>