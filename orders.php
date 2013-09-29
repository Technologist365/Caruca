<?php
require_once 'app/bootstrap.php';
require_once 'app/Models/ProductsModel.php';
$product = new \app\Models\ProductsModel($database); 
$cd = $session->getSetting("clientdata");

if(!empty($_SESSION['items'])) {
	$item = $session->getSetting("items");
	$order = array();
	foreach($item as $i) {
		$order[] = $i['name'].' - '.$i['qty']."<br>"; 
	}
	if(!empty($order)) {
		if($_GET['action'] == 'paid') {
			$product->addOrder($cd['client_id'], $order, 2);
		} else {
			$product->addOrder($cd['client_id'], $order, 1);
		}
		unset($_SESSION['items']);
		unset($_SESSION['cart']);
	}
}

if(empty($_GET)) {
	$template->setVar("orders", $product->listOrders($cd['client_id']));
} elseif($_GET['action'] == 'change') {
	$product->editOrder($_GET['id'], $cd['client_id'], $_GET['status']);
	header("location: /orders.php");
}

if(!empty($cd['client_id'])) {
	$template->display("pages/orders", "loggedin");
} else {
	header("location: /signin.php");
}
