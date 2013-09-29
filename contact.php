<?php
require_once 'app/bootstrap.php';
$cd = $session->getSetting("clientdata");

if(!empty($_POST)) {
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
	$message = htmlentities($_POST['message']);
	$headers = "From: " . $name . "<" . $email . ">\r\n";
	$headers.= 'MIME-Version: 1.0' . "\r\n";
	$headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";
	mail("denver@denverfreeburn.com", $subject, $message, $headers);

	$msg = "<b>Hey " . $name . "!</b><br><br>";
	$msg.= "Thanks for your message! I'll try and get back to you within 24 hours<br>";
	$msg.= "but that isn't always possible. Sorry :(";
	$headers2 = "From: ". SYSTEM_NAME ." <". SYSTEM_EMAIL .">\r\n";
	$headers2.= 'MIME-Version: 1.0' . "\r\n";
	$headers2.= 'Content-type: text/html; charset=utf-8' . "\r\n";
	mail($name . "<" .  $email . ">", "Thank you from ". SYSTEM_NAME, $msg, $headers2);
}

if(isset($cd['client_id'])) {
	header("location: /mysupport.php");
} else {
	$template->display("pages/contact", "loggedout");
}