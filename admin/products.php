<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/AdminModel.php';
$admin = new \app\Models\AdminModel($database);
$ad = $session->getSetting("admindata");
$cd = $session->getSetting("clientdata");

try {
	if(empty($_GET)) {
		$template->setVar("products", $admin->listproducts());
	} elseif($_GET['action'] == 'edit') {
		$template->setVar("product", $admin->listproducts($_GET['id']));
		$template->setVar("cats", $admin->fetchCats());
		if(!empty($_POST)) {
			$productname = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
			$productdesc = filter_var($_POST['desc'], FILTER_SANITIZE_STRING);
			$price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$weight = filter_var($_POST['weight'], FILTER_SANITIZE_NUMBER_INT);
			$admin->editproduct($_GET['id'], $_POST['cat'], $productname, $productdesc, $price, $weight);
			header("location: /admin/products.php");
		}
	} elseif($_GET['action'] == 'new') {
		$template->setVar("cats", $admin->fetchCats());
		if(!empty($_POST)) {
			$productname = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
			$productdesc = filter_var($_POST['desc'], FILTER_SANITIZE_STRING);
			$price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$weight = filter_var($_POST['weight'], FILTER_SANITIZE_NUMBER_INT);
			$admin->addproduct($_POST['cat'], $productname, $productdesc, $price, $weight);
			header("location: /admin/products.php");
		}
	} elseif($_GET['action'] == 'delete') {
		$admin->deleteproduct($_GET['id']);
		header("location: /admin/products.php");
	} elseif($_GET['action'] == 'images') {
		$template->setVar("images", $admin->fetchProductImage($_GET['id']));
	} elseif($_GET['action'] == 'deleteimage') {
		$admin->deleteProductImage($_GET['id']);
		header("location: /admin/products.php?action=images&id=". $_GET['pid']);
	} elseif($_GET['action'] == 'addimage') {
		if(!empty($_POST)) {
			list($pic,$ext) = explode(".", $_FILES['image']["name"]);
			$img = hash("md5", $pic);
			$image = strtoupper($img).'.'.$ext;
			if (file_exists("/uploads/" . $image)) {
				$template->setVar("errmsg", $image . " already exists. ");
			} else {
				$admin->addProductImage($_GET['id'], $image, $_POST['caption'], 0);
				move_uploaded_file($_FILES["image"]['tmp_name'], "uploads/" . $image);
				header("location: /admin/products.php?action=images&id=".$_GET['id']);
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
		$template->display("admin/products", "admin-in");
	} else {
		header("location: /admin/index.php");
	}
}