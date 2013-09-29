<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");

if(isset($cd['client_id'])) {
	$template->display("error", "loggedin");
} else {
	$template->display("error", "loggedout");
}