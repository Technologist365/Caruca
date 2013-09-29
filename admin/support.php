<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");
$template->setVar("signature", $admin->mySignature($ad['admin_id']));

try {
	if(empty($_GET)) {
		$template->setVar("tickets", $admin->listTicket());
	} elseif($_GET['action'] == 'view') {
		$template->setVar("ticket", $admin->listTicket($_GET['id']));
		$template->setVar("replies", $admin->getReplies($_GET['id']));
	} elseif($_GET['action'] == 'reply') {
		if(!empty($_POST)) {
			$message = htmlentities($_POST['ticket_reply']);
			$admin->replyTicket($ad['admin_id'], $_GET['id'], $message);
			header("location: /admin/support.php?action=view&id=" . $_GET['id']);
		}
	} elseif($_GET['action'] == 'close') {
		$admin->changeTicket($_GET['id'], 6);
		header("location: /admin/support.php");
	} elseif($_GET['action'] == 'new') {
		$template->setVar("member", $admin->fetchMembers($_GET['id']));
		if(!empty($_POST)) {
			$subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
			$message = htmlentities($_POST['new_ticket']);
			$admin->newTicket($ad['admin_id'], $subject, $message);
			header("location: /mysupport.php");
		}
	}
} catch (Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(!isset($cd['client_id'])) {
	header("location: /error.php?err=403");
} else {
	if(isset($ad['admin_id'])) {
		$template->display("admin/support", "admin-in");
	} else {
		header("location: /signin.php");
	}
}