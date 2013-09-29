<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");
require_once 'app/Models/ProductsModel.php';
$product = new \app\Models\ProductsModel($database);
$template->setVar("product", $product);
try {
	if(empty($_GET)) {
		$cart = $session->getSetting("cart");
			if($cart == null) {
				throw new Exception("Empty cart.");
			} else {
				$template->setVar("cart", $cart);
			}
	} elseif($_GET['action'] == 'empty') {
		unset($_SESSION['cart']);
		header("location: /cart.php");
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(isset($cd['client_id'])) {
	$template->display("pages/cart", "loggedin");
} else {
	header("location: /signin.php");
}