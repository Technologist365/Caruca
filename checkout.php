<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");

$template->setVar("totalprice", $session->getSetting("total"));

if(!empty($_POST)) {
	if($_POST['payment'] == 'paypal') {
		header("location: payment/paypal.php");
	}
}

if(isset($cd['client_id'])) {
	$template->display("pages/checkout", "loggedin");
} else {
	header("location: /signin.php");
}