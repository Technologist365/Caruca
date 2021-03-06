<?php
require_once 'app/bootstrap.php';
require_once 'app/Models/UserModel.php';
$user = new \app\Models\UserModel($database); 
$cd = $session->getSetting("clientdata");

$template->setVar("account", $user->fetchAccount($cd['client_id']));
$template->setVar("payment", $user->myGateway($cd['client_id']));
$template->setVar("gateways", $user->fetchGateways());

if($_GET['a'] == 'edit') {
	if(!empty($_POST)) {
		try {
			if(filter_var($_POST['firstname'], FILTER_SANITIZE_STRING)) {
				$firstname = $_POST['firstname'];
			} else {
				throw new Exception("Please make sure you use only A-Z characters");
			}
			if(filter_var($_POST['lastname'], FILTER_SANITIZE_STRING)) {
				$lastname = $_POST['lastname'];
			} else {
				throw new Exception("Please make sure you use only A-Z characters");
			}
			if(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) {
				$email = $_POST['email'];
			} else {
				throw new Exception("Your address is not correct. Example: john.doe@gmail.com");
			}
			if(filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT)) {
				$phone = $_POST['phone'];
			} else {
				throw new Exception("Sorry, your phone number is not in the correct format. Example: 07700123456 or 02838331234");
			}
	
			/* Validate the city and country names inputted into the system before
			 * sending them to the database.
			*/
			$line1 = filter_var($_POST['line1'], FILTER_SANITIZE_STRING);
			$line2 = filter_var($_POST['line2'], FILTER_SANITIZE_STRING);
			if(preg_match("/^[a-z0-9- ]/i", $_POST['city']) == true) {
				$city = $_POST['city'];
			} else {
				throw new Exception("Sorry, that city name is invalid. Please use only a-z and hyphens (-)");
			}
			if(preg_match("/^[a-z0-9- ]/i", $_POST['county']) == true) {
				$county = $_POST['county'];
			} else {
				throw new Exception("Sorry, that county name is invalid. Please use only a-z and hyphens (-)");
			}
			if(preg_match("/^[a-z0-9- ]/i", $_POST['country']) == true) {
				$country = $_POST['country'];
			} else {
				throw new Exception("Sorry, that country name is invalid. Please use only a-z and hyphens (-)");
			}
			if(preg_match("/^[a-z0-9 ]/i", $_POST['postcode']) == true) {
				$postcode = $_POST['postcode'];
			} else {
				throw new Exception("Sorry, that post/zip code is wrong. Example: AB12 3CD");
			}
	
			$user->editAccount($cd['client_id'], $firstname, $lastname, $email, $phone, $line1, $line2, $city, $county, $country, $postcode);
			header("location: /account.php?a=edit&save=1");
		} catch (Exception $e) {
			$template->setVar("errmsg", $e->getMessage());
		}
	}
} elseif($_GET['a'] == 'password') {
	if(!empty($_POST)) {
		try {
			if($_POST['newpassword'] == $_POST['confirmpassword']) {
				$user->changepassword($cd['client_id'], $_POST['oldpassword'], $_POST['newpassword']);
			} else {
				throw new Exception("Please check that new passwords match.");
			}
			$mail->passwordchange($cd['client_firstname'], $cd['client_lastname'], $cd['client_email']);
			header("location: /account.php?a=password&save=1");
		} catch (Exception $e) {
			$template->setVar("errmsg", $e->getMessage());
		}
	}
} elseif($_GET['a'] == 'delete') {
	$user->deleteAccount($cd['client_id']);
}

if(isset($_GET['save']) == 1) {
	$template->setVar("sucmsg", S001);
}

if(isset($cd['client_id'])) {
	$template->display("pages/account", "loggedin");
} else {
	header("location: /signin.php");
}