<?php
namespace app\Models;

class InfoModel {
	private $db;
	
	public function __construct($database) {
		$this->db = $database;
	}
	
	/**
	 * This fetches data-lookup matches from the database to be displayed as a
	 * modal on the website.
	 * @param string $data
	 * @return array
	 */
	public function getInfo($data) {
		$query = "select info_title,info_data,info_last_updated,client_firstname,client_lastname from ec_info 
				join ec_clients on (client_id = info_author) where info_tag=:data";
		$param = array(':data'=>$data);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return $result[0];
	}
}