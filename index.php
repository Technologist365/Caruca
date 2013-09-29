<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");
require_once 'app/Models/UserModel.php';
$user = new \app\Models\UserModel($database);
require_once 'app/Models/ProductsModel.php';
$products = new \app\Models\ProductsModel($database);

try {
	$template->setVar("products", $products->fetchLatestProducts(SYSTEM_LATEST_PRODUCTS));
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}
 
if(isset($cd['client_id'])) {
	$template->display("pages/index", "loggedin");
} else {
	$template->display("pages/index", "loggedout");
}