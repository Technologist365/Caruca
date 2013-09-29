<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");
require_once 'app/Models/SupportModel.php';
$support = new \app\Models\SupportModel($database);
require_once 'app/Models/UserModel.php';
$user = new \app\Models\UserModel($database);

try {
	if(empty($_GET)) {
		$template->setVar("tickets", $support->listTicket($cd['client_id']));
	} elseif($_GET['action'] == 'view') {
		$template->setVar("ticket", $support->listTicket($cd['client_id'], $_GET['id']));
		$template->setVar("replies", $support->getReplies($cd['client_id'], $_GET['id']));
	} elseif($_GET['action'] == 'reply') {
		if(!empty($_POST)) {
			$message = htmlentities($_POST['ticket_reply']);
			$support->replyTicket($cd['client_id'], $_GET['id'], $message);
			header("location: /mysupport.php?action=view&id=".$_GET['id']);
		}
	} elseif($_GET['action'] == 'close') {
		$support->changeTicket($cd['client_id'], $_GET['id'], 6);
		header("location: /mysupport.php");
	} elseif($_GET['action'] == 'new') {
		if(!empty($_POST)) {
			$subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
			$message = htmlentities($_POST['new_ticket']);
			$support->newTicket($cd['client_id'], $subject, $message);
			header("location: /mysupport.php");
		}
	}
} catch (Exception $e) {
	$template->setVar("tickerrmsg", $e->getMessage());
}

if(!isset($cd['client_id'])) {
	header("location: /contact.php");
} else {
	$template->display("pages/mysupport", "loggedin");
}