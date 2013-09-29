<?php
chdir("../");
require_once 'app/bootstrap.php';
if(isset($_GET['product_id']) && isset($_GET['qty'])){
	$product_id = $_GET['product_id'];
	$qty = $_GET['qty'];
	$cart = $_SESSION['cart'];
	if($cart == null) {
		$cart = array();
	}
	if(!isset($cart[$product_id])) {
		$cart[$product_id] = 0;
	}
	$cart[$product_id] += $qty;
	//var_dump($cart);
}
$session->setSetting("cart", $cart);

/* $quantity = $this->getRequest()->request->get('qty');
$item = $this->getRequest()->request->get('item');
$cart = $this->get('session')->get('cart');
if ($cart == null) {
	$cart = array();
}
if (!isset($cart[$item])) {
	$cart[$item] = 0;
}
$cart[$item] += $quantity; */

//header("location: /cart.php");
?>
