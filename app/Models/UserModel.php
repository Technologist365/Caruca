<?php
/**
 * UserModel for User-orientated actions.
 * @author Denver
 */
namespace app\Models;

class UserModel {
	/**
	 * Set the database property
	 * @var property
	 */
	private $db;
	
	/**
	 * Construct the database object so we can make calls via PDO.
	 * @param object $database
	 */
	public function __construct($database) {
		$this->db = $database;
	}
	
	/**
	 * If I have to explain what this does, you're a fucking retard.
	 * @param string $username
	 * @param string $password
	 * @throws \Exception
	 * @return array
	 */
	public function login($email, $pass) {
		$q = "select client_salt from ec_clients where client_email=:email";
		$p = array(':email' => $email);
		$s = $this->db->prepare($q);
		$s->execute($p);
		$r = $s->fetchAll();
		$salt = $r[0]['client_salt'];
		
		$query = "select client_id,client_email,client_salt,client_firstname,client_lastname from ec_clients where client_email=:email and client_password=:pass and client_locked='0' and client_active='1'";
		$param = array(':email' => $email, ':pass' => UserModel::buildPass($salt.$pass.$salt.$pass.$salt));
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		
		if($stmt->rowCount() == 0) {
			throw new \Exception("Email Address/Password mismatch.");
		} else {
			return $result[0];
		}
	}
	
	public function register($firstname, $lastname, $email, $password, $phone, $line1, $line2, $city, $county, $country, $postcode) {
		$salt = hash("crc32", rand(1,99999));
		
		$q = "select client_email from ec_clients where client_email=:email";
		$p = array(':email' => $email);
		
		$stmt = $this->db->prepare($q);
		$stmt->execute($p);
		if($stmt->rowCount() != 0) {
			throw new \Exception("A record already exists for the email address you specified");
		} else {
			$query = "insert into ec_clients (client_firstname,client_lastname,client_email,client_password,client_phone,
					client_line1,client_line2,client_city,client_county,client_country,client_postcode,client_salt) values 
					(:firstname,:lastname,:email,:password,:phone,:line1,:line2,:city,:county,:country,:postcode,:salt)";
			$param = array(":firstname" => $firstname, ":lastname" => $lastname, ":email" => $email, ":password" => UserModel::buildPass($salt.$password.$salt.$password.$salt), 
					":phone" => $phone, ":line1" => $line1, ":line2" => $line2, ":city" => $city, ":county" => $county, ":country" => $country, 
					":postcode" => $postcode, ":salt" => $salt);
			$stmt = $this->db->prepare($query);
			$stmt->execute($param);
		}
	}
	
	public function fetchAccount($id) {
		$query = "select client_firstname,client_lastname,client_joined,client_email,client_phone,client_line1,client_line2,client_city,client_county,
				client_country,client_postcode,gw_vanity as client_gw from ec_clients join ec_gateways on (client_gateway = gw_id) where client_id=:id";
		$param = array(":id" => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
	
	/**
	 * Hash the password
	 * @param string $pass
	 * @return string
	 */
	private function buildPass($pass) {
		return hash("sha512", $pass);
	}
	
	public function getCreds($id) {
		$query = "select client_firstname,client_lastname from ec_clients where client_id=:id";
		$param = array(":id" => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			
		} else {
			return $result[0];
		}
	}
	
	public function isAdmin($email) {
		$query = "select admin_id from ec_admin where admin_email=:email";
		$param = array(':email' => $email);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
	
	public function myGateway($id) {
		$query = "select gw_name,gw_vanity from ec_gateways join ec_clients on (gw_id = client_gateway) where client_id=:id";
		$param = array(':id' => $id);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	public function fetchGateways() {
		$query = "select gw_name,gw_vanity from ec_gateways";
		$param = null;
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	public function editAccount($id, $firstname, $lastname, $email, $phone, $line1, $line2, $city, $county, $country, $postcode) {
		$query = "update ec_clients set client_firstname=:firstname, client_lastname=:lastname, client_email=:email, client_phone=:phone,
					client_line1=:line1, client_line2=:line2, client_city=:city, client_county=:county, client_country=:country,
					client_postcode=:postcode where client_id=:id";
		$param = array(":firstname" => $firstname, ":lastname" => $lastname, ":email" => $email, ":phone" => $phone, ":line1" => $line1, 
				":line2" => $line2, ":city" => $city, ":county" => $county, ":country" => $country,	":postcode" => $postcode, ":id" => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function changepassword($id, $old, $new) {
		$q = "select client_salt from ec_clients where client_id=:id";
		$p = array(':id' => $id);
		$s = $this->db->prepare($q);
		$s->execute($p);
		$r = $s->fetchAll();
		$salt = $r[0]['client_salt'];
		$hash_old = UserModel::buildPass($salt.$old.$salt.$old.$salt);
		$hash_new = UserModel::buildPass($salt.$new.$salt.$new.$salt);
		
		$query = "update ec_clients set client_password=:new where client_password=:old and client_id=:id";
		$param = array(':new' => $new, ':old' => $old, ':id' => $id);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function deleteAccount($id) {
		$query = "update ec_clients set client_locked='1' where client_id=:id";
		$param = array(':id' => $id);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
}
