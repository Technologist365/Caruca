<?php
chdir("../");
require_once 'app/bootstrap.php';
$ad = $session->getSetting("admindata");

if(isset($ad['admin_id'])) {
	$session->__unset("admindata");
	unset($_SESSION);
	header("location: /admin/index.php");
}