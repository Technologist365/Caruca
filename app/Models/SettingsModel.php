<?php
namespace app\Models;

class SettingsModel {
	private $db;
	
	public function __construct($database) {
		$this->db = $database;
	}
	
	public function ec_setting($key) {
		$query = "select ec_value from ec_configuration where ec_key=:key";
		$param = array(':key' => $key);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return $result[0]['ec_value'];
	}
}