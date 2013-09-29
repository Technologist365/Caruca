<?php
/**
 * Stores $_POST data as an object for use in the model, but
 * it is deprecated now. - Denver
 * @author Stevan
 *
 */
namespace syst\config;

require_once dirname(__FILE__)."/../config.php";

class Post extends \sys\Config
{
	public function __construct()
	{
		parent::__construct($_POST);
	}
}

?>