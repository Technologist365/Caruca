<?php
require_once 'app/error.php';
$config = array();

/* The database server hostname.. this is usually localhost */
$config["database.hostname"] = "192.168.1.11";

/* The username to log into the database server */
$config["database.username"] = "root";

/* The password you assigned or were assigned by your mysql host */
$config["database.password"] = "";

/* The name of your database. In cPanel this is usually account_dbname. */
$config["database.database"] = "ecom";

/* The database driver. We're using PDO which supports many database types 
 * but our application uses mysql. */
$config["database.driver"]   = "mysql";

/* The table prefix in your database. Useful if you have multiple installations.
 * Do not put an underscore. It is automatically included. */ 
$config["database.prefix"]   = "ec"; 

$config["template.template_dir"] = "app/Views";
$config["template.template"]     = "paperstash";

define('systemname', "Aplan");
define('systemvers', "1.0");

/* Your timezome. To change: Select your Continent and nearest major city, like: America/Boston. */
date_default_timezone_set("Europe/London");
?>
