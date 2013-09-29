<?php
require_once 'app/bootstrap.php';
require_once 'app/Models/UserModel.php';
$user = new \app\Models\UserModel($database);
$cd = $session->getSetting("clientdata");

/* Again, the most messy function ever known to man. No matter how much I try
 * to optimize it, it still comes out very, very untidy... but at least it
 * works. - Denver
 */

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
		if($_POST['password'] == $_POST['confirmpassword']) {
			$password = $_POST['password'];
		} else {
			throw new Exception("Sorry, your passwords do not match.");
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
		
		$user->register($firstname, $lastname, $email, $password, $phone, $line1, $line2, $city, $county, $country, $postcode);
		$mail->register($firstname, $lastname, $email, $password);
		header("location: /signin.php");
	} catch (Exception $e) {
		$template->setVar("errmsg", $e->getMessage());
	}
}

if(isset($cd['client_id'])) {
	header("location: /index.php");
} else {
	$template->display("pages/signup", "loggedout");
}