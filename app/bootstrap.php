<?php
require_once "app/config.php";

require_once "sys/config.php";
require_once "sys/database.php";
require_once "sys/template.php";

require_once "sys/config/session.php";
$session = new \sys\config\Session();

$config = new \sys\Config($config);
$database = new \sys\Database($config);

$template = new \sys\Template($config);
$template->session = $session;

require_once "app/Models/SettingsModel.php";
$settings = new \app\Models\SettingsModel($database);

define("SYSTEM_NAME", $settings->ec_setting("SYSTEM_NAME"));
define("SYSTEM_URL", $settings->ec_setting("SYSTEM_URL"));
define("SYSTEM_EMAIL", $settings->ec_setting("SYSTEM_EMAIL"));
define("SYSTEM_THEME", $settings->ec_setting("SYSTEM_THEME"));
define("SYSTEM_PHONE", $settings->ec_setting("SYSTEM_PHONE"));
define("SYSTEM_ANALYTICS", $settings->ec_setting("SYSTEM_ANALYTICS"));
define("SYSTEM_DESC", $settings->ec_setting("SYSTEM_DESC"));
define("SYSTEM_LATEST_PRODUCTS", $settings->ec_setting("SYSTEM_LATEST_PRODUCTS"));
define("FACEBOOK", $settings->ec_setting("FACEBOOK"));
define("TWITTER", $settings->ec_setting("TWITTER"));
define("OTHER", $settings->ec_setting("OTHER"));


error_reporting(E_ALL);
$cd = $session->getSetting("clientdata");
require_once "app/Models/ProductsModel.php";
$products = new \app\Models\ProductsModel($database);
try {
	$template->setVar("cats", $products->fetchCat());
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

require_once "app/Models/UserModel.php";
$user = new \app\Models\UserModel($database);
try {
	$cd = $session->getSetting("clientdata");
	$template->setVar("creds", $user->getCreds($cd['client_id']));
	$template->setVar("isAdmin", $user->isAdmin($cd['client_email']));
} catch (Exception $e){
	return false;
}

require_once "app/Models/MailModel.php";
$mail = new \app\Models\MailModel();

require_once 'app/Models/SupportModel.php';
$support = new \app\Models\SupportModel($database);
$template->setVar("total", count($support->countTickets($cd['client_id'])));

require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$template->setVar("ticketcount", $admin->countTickets());