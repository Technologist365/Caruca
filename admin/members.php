<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");

try {
	if(!empty($_GET)) {
		if($_GET['s'] == 'manage') {
			$template->setVar("members", $admin->fetchMembers($_GET['id']));
		} elseif($_GET['s'] == 'lock') {
			$admin->lockMember($_GET['id'], 1);
			header("location: /admin/members.php");
		} elseif($_GET['s'] == 'unlock') {
			$admin->lockMember($_GET['id'], 0);
			header("location: /admin/members.php");
		}
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(!isset($cd['client_id'])) {
	header("location: /error.php?err=403");
} else {
	if(isset($ad['admin_id'])) {
		$template->display("admin/members", "admin-in");
	} else {
		header("location: /signin.php");
	}
}