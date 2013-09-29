<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");
$template->setVar("ad", json_encode($ad));

try {
	if(!empty($_GET)) {
		if($_GET['action'] == 'edit') {
			if(!empty($_POST)) {
				$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
				$data = filter_var($_POST['data'], FILTER_SANITIZE_STRING);
				$aid = filter_var($_POST['author'], FILTER_SANITIZE_NUMBER_INT);
				$pid = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
				$admin->editPage($pid, $aid, $title, $data);
				$template->setVar("sucmsg", "Page updated successfully.");
			}
			$template->setVar("pages", $admin->listPage($_GET['id']));
		} elseif($_GET['action'] == 'delete') {
			$admin->deletePage($_GET['id']);
			header("location: /admin/pages.php");
		} elseif($_GET['action'] == 'new') {
			if(!empty($_POST)) {
				$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
				$data = filter_var($_POST['data'], FILTER_SANITIZE_STRING);
				$aid = filter_var($_POST['author'], FILTER_SANITIZE_NUMBER_INT);
				$unique = filter_var($_POST['unique'], FILTER_SANITIZE_STRING);
				$admin->addPage($aid, $unique, $title, $data);
				header("location: /admin/pages.php");
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
		$template->display("admin/pages", "admin-in");
	} else {
		header("location: /signin.php");
	}
}