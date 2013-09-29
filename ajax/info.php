<?php 
chdir('../');
require_once 'app/bootstrap.php';
require_once 'app/Models/InfoModel.php';
$info = new \app\Models\InfoModel($database);

/**
 * Check that `d` is set but empty then return the repsonse
 */
if(empty($_GET['d'])) {
	echo "401: Empty data response";
} else {
	/**
	 * Or else if it is not empty, we pass it along to InfoModel and
	 * search the database, then encode the results in JSON for passing
	 * to jQuery for outputting.
	 */
	echo json_encode($info->getInfo($_GET['d']));
}

?>
