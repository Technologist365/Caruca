<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");

if(!empty($_POST)) {
	try {
		if(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) {
			$email = $_POST['email'];
		} else {
			throw new Exception("Your address is not correct. Example: john.doe@gmail.com");
		}
		$pass = $_POST['password'];
		$session->setSetting("admindata", $admin->login($email, $pass));
		header("location: /admin/index.php");
	} catch (Exception $e) {
		$template->setVar("errmsg", $e->getMessage());
	}
}

if(!isset($cd['client_id'])) {
	header("location: /error.php?err=403");
} else {
	if(isset($ad['admin_id'])) {
		$template->display("admin/index", "admin-in");
	} else {
		$template->display("admin/login", "admin-out");
	}
}