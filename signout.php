<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");

if(isset($cd['client_id'])) {
	$session->__unset("clientdata");
	unset($_SESSION);
	header("location: /index.php");
}