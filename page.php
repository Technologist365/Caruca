<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");
require_once 'app/Models/PageModel.php';
$page = new \app\Models\PageModel($database);

try {
	$template->setVar("page", $page->fetchPage($_GET['p']));
} catch(Exception $e) {
	$template->setVar("errmsg", $e->getMessage());
}

if(isset($cd['client_id'])) {
	$template->display("pages/page", "loggedin");
} else {
	$template->display("pages/page", "loggedout");
}