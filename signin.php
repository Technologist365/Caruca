<?php
require_once 'app/bootstrap.php';
require_once 'app/Models/UserModel.php';
$user = new \app\Models\UserModel($database);
$cd = $session->getSetting("clientdata");

if(!empty($_POST)) {
	try {
		if(empty($_POST['email'])) {
			throw new Exception("Your email address must not be empty!");
		} else {
			if(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) {
				$email = $_POST['email'];
			} else {
				throw new Exception("Your email address is not correct. Example: john.doe@gmail.com");
			}
		}
		if(empty($_POST['password'])) {
			throw new Exception("Your password must not be empty!");
		} else {
			$pass = $_POST['password']; 
		}
		$session->setSetting("clientdata", $user->login($email, $pass));
		header("location: /index.php");
	} catch (Exception $e) {
		$template->setVar("errmsg", $e->getMessage());
	}
}

if(isset($cd['client_id'])) {
	header("location: /index.php");
} else {
	$template->display("pages/signin", "loggedout");
}