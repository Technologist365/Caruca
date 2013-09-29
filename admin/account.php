<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");

try {
	$template->setVar("account", $admin->myAccount($ad['admin_id']));
	if($_GET['a'] == 'password') {
		if(!empty($_POST)) {
			try {
				if($_POST['newpassword'] == $_POST['confirmpassword']) {
					$admin->changepassword($ad['admin_id'], $_POST['oldpassword'], $_POST['newpassword']);
				} else {
					throw new Exception("Please check that new passwords match.");
				}
				$mail->passwordchange($ad['admin_firstname'], $ad['admin_lastname'], $ad['admin_email']);
				header("location: /admin/account.php?a=password&save=1");
			} catch (Exception $e) {
				$template->setVar("errmsg", $e->getMessage());
			}
		}
	} elseif($_GET['a'] == 'edit') {
		if(!empty($_POST)) {
			if(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING)) {
				$firstname = $_POST['firstname'];
			} else {
				throw new Exception("Please make sure you use only A-Z characters");
			}
			if(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING)) {
				$lastname = $_POST['lastname'];
			} else {
				throw new Exception("Please make sure you use only A-Z characters");
			}
			if(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) {
				$email = $_POST['email'];
			} else {
				throw new Exception("Your address is not correct. Example: john.doe@gmail.com");
			}
			$signature = $_POST['signature'];
	
			$admin->editAccount($ad['admin_id'], $firstname, $lastname, $email, $signature);
			header("location: /admin/account.php?a=view");
		}
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(isset($_GET['save']) == 1) {
	$template->setVar("sucmsg", S001);
}

if(!isset($cd['client_id'])) {
	header("location: /error.php?err=403");
} else {
	if(isset($ad['admin_id'])) {
		$template->display("admin/account", "admin-in");
	} else {
		header("location: /admin/index.php");
	}
}