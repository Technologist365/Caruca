<?php
/**
 * Stores $_GET data as an object for use in the model, but 
 * it is deprecated now. - Denver
 * @author Stevan
 *
 */
namespace sys\config;

require_once dirname(__FILE__)."/../config.php";

class Get extends \sys\Config
{
	public function __construct()
	{
		parent::__construct($_GET);
	}
}

?>