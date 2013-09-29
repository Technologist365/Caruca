<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database); 
$cd = $session->getSetting("clientdata");

if(empty($_GET)) {
	$template->setVar("orders", $admin->listOrders());
} elseif($_GET['action'] == 'change') {
	$admin->editOrder($_GET['id'], $_GET['status']);
	header("location: /admin/orders.php");
} elseif($_GET['action'] == 'view') {
	$template->setVar("orders", $admin->listOrders($_GET['id']));
}

if(!empty($cd['client_id'])) {
	$template->display("admin/orders", "admin-in");
} else {
	header("location: /signin.php");
}
