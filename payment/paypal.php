<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/PayPalModel.php';
$paypal = new \app\Models\PayPal();

$template->setVar("paypal", json_encode($paypal));
$template->setVar("cost", $session->getSetting("total"));
$template->setVar("items", $session->getSetting("items"));

if(isset($cd['client_id'])) {
	$template->display("pages/paypal", "loggedin");
} else {
	header("location: /signin.php");
}