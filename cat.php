<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");
require_once 'app/Models/ProductsModel.php';
$cat = new \app\Models\ProductsModel($database);

try {
	$template->setVar("products", $cat->fetchProductsByCat($_GET['id']));
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}


if(isset($cd['client_id'])) {
	$template->display("pages/cat", "loggedin");
} else {
	$template->display("pages/cat", "loggedout");
}