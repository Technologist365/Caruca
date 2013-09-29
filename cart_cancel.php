<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");

unset($_SESSION['cart']);

if(isset($cd['client_id'])) {
	$template->display("pages/cart_cancel", "loggedin");
} else {
	header("location: /signin.php");
}