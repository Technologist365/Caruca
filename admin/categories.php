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
		if($_GET['s'] == 'inactive') {
			$admin->catView($_GET['id'], 0);
			header("location: /admin/categories.php");
		} elseif($_GET['s'] == 'active') {
			$admin->catView($_GET['id'], 1);
			header("location: /admin/categories.php");
		} elseif($_GET['s'] == 'new') {
			if(!empty($_POST)) {
				$admin->addcat(filter_var($_POST['category'], FILTER_SANITIZE_STRING));
				header("location: /admin/categories.php");
			} 
		} elseif($_GET['s'] == 'delete') {
			$admin->banAdmin($_GET['id'], 2);
			header("location: /admin/admins.php");
		} elseif($_GET['s'] == 'manage') {
			$template->setVar("cat", $admin->fetchCats($_GET['id']));
			if(!empty($_POST)) {
				$cat_name = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
				if($_POST['active'] == 'on') {
					$checked = $_POST['active'];
				} else {
					$cheked = null; 
				}
				$admin->editcat($_GET['id'], $cat_name, $checked);
				header("location: /admin/categories.php");
			}
		}
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(!isset($cd['client_id'])) {
	header("location: /error.php?err=403");
} else {
	if(isset($ad['admin_id'])) {
		$template->display("admin/categories", "admin-in");
	} else {
		header("location: /signin.php");
	}
}