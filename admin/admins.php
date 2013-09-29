<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
require_once 'app/Models/MailModel.php';
$admin = new \app\Models\AdminModel($database);
$mail = new \app\Models\MailModel();
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");

try {
	if(!empty($_GET)) {
		if($_GET['s'] == 'lock') {
			$admin->banAdmin($_GET['id'], 0);
			header("location: /admin/admins.php");
		} elseif($_GET['s'] == 'unlock') {
			$admin->banAdmin($_GET['id'], 1);
			header("location: /admin/admins.php");
		} elseif($_GET['s'] == 'new') {
			if(!empty($_POST)) {
				if(filter_var($_POST['username'], FILTER_SANITIZE_STRING)) {
					$username = $_POST['username'];
				} else {
					throw new Exception("Please make sure you use only A-Z characters");
				}
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
				$password = $_POST['password'];
				$admin->addNewAdmin($username, $firstname, $lastname, $email, $password);
				$mail->newadmin($username,$firstname,$email,$password);
			} elseif($_GET['s'] == 'manage') {
				$template->setVar("admin", $admin->fetchAdmins($_GET['id']));
				if(!empty($_POST)) {
					if(filter_var($_POST['username'], FILTER_SANITIZE_STRING)) {
						$username = $_POST['username'];
					} else {
						throw new Exception("Please make sure you use only A-Z characters");
					}
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
					$password = $_POST['password'];
					$admin->editAdmin($_GET['id'], $username, $firstname, $lastname, $email, $password);
					$mail->newadmin($username,$firstname,$email,$password);
				}
			}
		} elseif($_GET['s'] == 'delete') {
			$admin->banAdmin($_GET['id'], 2);
			header("location: /admin/admins.php");
		}
	} else {
		$template->setVar("admin", $admin->fetchAdmins());
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(!isset($cd['client_id'])) {
	header("location: /error.php?err=403");
} else {
	if(isset($ad['admin_id'])) {
		$template->display("admin/admins", "admin-in");
	} else {
		header("location: /signin.php");
	}
}