<?php
namespace app\Models;

class PageModel {
	private $db;
	
	public function __construct($database) {
		$this->db = $database;
	}
	
	public function fetchPage($unique) {
		$query = "select page_title,page_data,page_created,page_edited,admin_firstname,
				admin_lastname from ec_pages join ec_admin using (admin_id) where 
				page_unique=:unique";
		$param = array(':unique' => $unique);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("Sorry, we cannot find a page with this name.");
		}
		$result = json_encode($stmt->fetchAll());
		return $result;
	}
}