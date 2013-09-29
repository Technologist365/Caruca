<?php
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/ProductsModel.php';
$product = new \app\Models\ProductsModel($database);
$cd = $session->getSetting("clientdata"); 

echo $product->listOrders($cd['client_id'], $_GET['id']);