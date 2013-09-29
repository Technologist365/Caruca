<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");

if(isset($cd['client_data'])) {
	$template->display("credits", "loggedin");
} else {
	$template->display("credits", "loggedout");
}