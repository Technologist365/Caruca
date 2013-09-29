<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");
require_once 'app/Models/ProductsModel.php';
$products = new \app\Models\ProductsModel($database);

try {
	if(empty($_GET['id'])) {
		$template->setVar("products", $products->fetchProducts());
	} else {
		$template->setVar("products", $products->fetchProductById($_GET['id']));
		try {
			$template->setVar("pictures", $products->fetchMorePictures($_GET['id']));
		} catch (Exception $e) {
			$template->setVar("picerror", $e->getMessage());
		}
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}


if(isset($cd['client_id'])) {
	$template->display("pages/products", "loggedin");
} else {
	$template->display("pages/products", "loggedout");
}