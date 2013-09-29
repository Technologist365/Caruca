<?php
namespace app\Models;

class MailModel {
	
	public function headers() {
		$headers = 'From: '. SYSTEM_NAME .'<'. SYSTEM_EMAIL .'>';
		$headers.= 'X-Mailer: PHP/' . phpversion();
		return $headers;
	}
	
	public function passwordchange($first, $email) {
		$message = require_once 'app/emails/changepassword.php';
		$to = $email;
		mail($to, "Your recently changed your password on " . SYSTEM_NAME, $message, MailModel::headers());
	}
	
	public function register($first, $last, $email, $pass) {
		$message = require_once 'app/emails/register.php';
		$to = $email;
		mail($to, "Thank you for registering on " . SYSTEM_NAME, $message, MailModel::headers());
	}
	
	public function newadmin($username,$firstname,$email,$password) {
		$message = require_once 'app/emails/newadmin.php';
		$to = $email;
		mail($to, "Your New Administrator Account on " . SYSTEM_NAME, $message, MailModel::headers());
	}
	
	public function adminlogin($username,$firstname,$email,$password) {
		$message = require_once 'app/emails/adminlogin.php';
		$to = $email;
		mail($to, "Administrator Account Log.", $message, MailModel::headers());
	}
}