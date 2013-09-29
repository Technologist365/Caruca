<?php
namespace app\Models;

class AdminModel {
	private $db;
	private $cookie;
	
	public function __construct($database) {
		$this->db = $database;
	}
	
	/* Misc Methods */
	public function login($email, $pass) {
		$q = "select client_salt from ec_clients where client_email=:email";
		$p = array(':email' => $email);
		$s = $this->db->prepare($q);
		$s->execute($p);
		$r = $s->fetchAll();
		$salt = $r[0]['client_salt'];
	
		$query = "select admin_id,admin_username,admin_firstname,admin_lastname,admin_email,admin_signature from ec_admin 
				where admin_email=:email and admin_password=:pass and admin_active='1'";
		$param = array(':email' => $email, ':pass' => AdminModel::buildPass($salt.$pass.$salt.$pass.$salt));
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
	
		if($stmt->rowCount() == 0) {
			throw new \Exception("Email Address/Password mismatch.");
		} else {
			return $result[0];
		}
	}
	
	private function buildPass($pass) {
		return hash("sha512", $pass);
	}
	
	public function buildPerm($uid, $sid) {
		$query = "select perm_name,perm_value from ec_admin_perms where admin_id=:uid and perm_id=:sid";
		$param = array(':uid' => $uid, ':sid' => $sid);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			throw new \Exception("Permission denied.");
		} else {
			return $result[0];
		}
	}
	
	public function myAccount($id) {
		$query = "select admin_username,admin_firstname,admin_lastname,admin_email,admin_signature from ec_admin where admin_id=:id";
		$param = array(':id' => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			throw new \Exception("Erm, this is embarrassing but, I cannot find your admin profile.");
		} else {
			return json_encode($result);
		}
	}
	
	public function changepassword($id, $old, $new) {
		$q = "select client_salt from ec_clients where client_id=:id";
		$p = array(':id' => $id);
		$s = $this->db->prepare($q);
		$s->execute($p);
		$r = $s->fetchAll();
		$salt = $r[0]['client_salt'];
		$hash_old = AdminModel::buildPass($salt.$old.$salt.$old.$salt);
		$hash_new = AdminModel::buildPass($salt.$new.$salt.$new.$salt);
	
		$query = "update ec_admin set admin_password=:new where admin_password=:old and admin_id=:id";
		$param = array(':new' => $hash_new, ':old' => $hash_old, ':id' => $id);
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function editAccount($id, $firstname, $lastname, $email, $signature) {
		$query = "update ec_admin set admin_firstname=:firstname, admin_lastname=:lastname, admin_email=:email, admin_signature=:signature 
				where admin_id=:id";
		$param = array(":firstname" => $firstname, ":lastname" => $lastname, ":email" => $email, ":signature" => $signature, ":id" => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* At this point, I'll try to group and add structure to the way the Model is laid out */
	
	/* Categories */
	
	/**
	 * Adds a category and defines whether a parent<->child relationship exists.
	 * @param string $catname
	 * @param int $catparent
	 */
	public function addcat($catname) {
		$query = "insert into ec_categories (cat_label,cat_active) values (:catname,'1')";
		$param = array(':catname' => $catname);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/**
	 * Fetch and save a category type based on whether $save is set to true.
	 * @param int $catid
	 * @param string $catname
	 * @param int $catparent
	 * @param bool $save
	 */
	public function editcat($catid, $catname, $checked = null) {
		if($checked == null) {
			$query = "update ec_categories set cat_label=:catname, cat_active='1' where cat_id=:catid";
			$param = array(':catname' => $catname, ':catid' => $catid);
		} elseif($checked == 'on') {
			$query = "update ec_categories set cat_label=:catname, cat_active='0' where cat_id=:catid";
			$param = array(':catname' => $catname, ':catid' => $catid);
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/**
	 * Delete a category completely
	 * @param int $catid
	 */
	
	public function fetchCats($catid = null) {
		if($catid == null) {
			$query = "select cat_id,cat_label,cat_active from ec_categories";
			$param = null;
		} else {
			$query = "select cat_id,cat_label from ec_categories where cat_id=:catid";
			$param = array(':catid' => $catid);
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	/**
	 * View a category
	 * @param int $id
	 * @param int $state
	 */
	public function catView($id, $state) {
		$query = "update ec_categories set cat_active=:state where cat_id=:id";
		$param = array(':state' => $state, ':id' => $id);
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* Products */
	
	public function addproduct($catid, $productname, $productdesc, $price, $weight) {
		$query = "insert into ec_products (cat_id,product_name,product_desc,product_price,product_weight) 
					values (:catid,:name,:desc,:price,:weight)";
		$param = array(':catid' => $catid, ':name' => $productname, ':desc' => $productdesc, ':price' => $price, ':weight' => $weight);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		
		$q = "select product_id from ec_products order by product_id desc limit 1";
		$s = $this->db->query($q);
		$r = $s->fetchAll();
		$query2 = "insert into ec_pictures (product_id,picture_name,picture_caption,picture_main) values (:id,'default.png','null','1')";
		$param2 = array(':id' => $r[0]['product_id']);
		
		$stmt2 = $this->db->prepare($query2);
		$stmt2->execute($param2);
	}
	
	public function listproducts($id = null) {
		if($id == null) {
			$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using (cat_id)
					left join ec_pictures using (product_id) where picture_main='1';";
			$param = null;
		} else {
			$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using (cat_id)
					left join ec_pictures using (product_id) where product_id=:id and picture_main='1'";
			$param = array(':id' => $id);
		}
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("There are no products to display.");
		} else {
			$result = $stmt->fetchAll();
			return json_encode($result);
		}
	}
	
	public function editproduct($productid, $catid, $productname, $productdesc, $price, $weight) {
		$query = "update ec_products set cat_id=:catid, product_name=:name, product_desc=:desc, product_price=:price, product_weight=:weight
				where product_id=:id";
		$param = array(':catid' => $catid, ':name' => $productname, ':desc' => $productdesc, ':price' => $price, ':weight' => $weight, 
				':id' => $productid);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function deleteproduct($productid) {
		$query = "delete from ec_products where product_id=:productid";
		$query2= "delete from ec_pictures where product_id=:productid";
		$param = array(':productid' => $productid);
		$param2 = array(':productid' => $productid);
		
		$stmt = $this->db->prepare($query);
		$stmt2= $this->db->prepare($query2);
		$stmt->execute($param);
		$stmt2->execute($param2);
	}
	
	public function fetchProductImage($id) {
		$query = "select picture_id,product_id,picture_name,picture_caption,picture_main from ec_pictures where product_id=:id";
		$param = array(':id' => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	public function addProductImage($pid, $iname, $icap, $main) {
		if($main == 0) {
			$query = "insert into ec_pictures (product_id,picture_name,picture_caption,picture_main) values (:pid,:iname,:icap,'0')";
		} else {
			$query = "insert into ec_pictures (product_id,picture_name,picture_caption,picture_main) values (:pid,:iname,:icap,'1')";
		}
		
		$param = array(':pid' => $pid, ':iname' => $iname, ':icap' => $icap);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function deleteProductImage($id) {
		$query = "delete from ec_pictures where picture_id=:id";
		$param = array(':id' => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* Admins */
	
	public function addNewAdmin($username, $firstname, $lastname, $email, $pass) {
		$q = "select client_salt from ec_clients where client_email=:email";
		$p = array(':email' => $email);
		$s = $this->db->prepare($q);
		$s->execute($p);
		$r = $s->fetchAll();
		$salt = $r[0]['client_salt'];
		$query = "insert into ec_admin (admin_username,admin_firstname,admin_lastname,admin_password,admin_email)
			values(:user,:first,:last,:pass,:email)";
		$param = array(':user' => $username, ':first' => $firstname, ':last' => $lastname, ':pass' => 
				AdminModel::buildPass($salt.$pass.$salt.$pass.$salt), ':email' => $email);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function editAdmin($id, $username, $firstname, $lastname, $email, $pass) {
		$q = "select client_salt from ec_clients where client_email=:email";
		$p = array(':email' => $email);
		$s = $this->db->prepare($q);
		$s->execute($p);
		$r = $s->fetchAll();
		$salt = $r[0]['client_salt'];
		$query = "update ec_admin set admin_username=:user, admin_firstname=:firstname, admin_lastname=:lastname, 
				admin_email=:email, admin_password=:pass where admin_id=:id";
		$param = array(':id' => $id, ':user' => $username, ':first' => $firstname, ':last' => $lastname, ':pass' =>
				AdminModel::buildPass($salt.$pass.$salt.$pass.$salt), ':email' => $email);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function fetchAdmins($id = null) {
		if($id == null) {
			$query = "select admin_id,admin_username,admin_firstname,admin_lastname,admin_email,
					admin_active as 'a' from ec_admin";
			$param = null;
		} else {
			$query = "select admin_id,admin_username,admin_firstname,admin_lastname,admin_email,
					admin_active as 'a' from ec_admin where	admin_id=:id";
			$param = array(':id' => $id);
		}
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			throw new \Exception("No data returned");
		} else {
			return json_encode($result);
		}
	}
	
	public function banAdmin($id, $state) {
		$query = "update ec_admin set admin_active=:state where admin_id=:id";
		$param = array(':state' => $state, ':id' => $id);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* Members */
	
	public function fetchMembers($id = null) {
		if($id == null) {
			$query = "select client_id,client_firstname,client_lastname,client_email,client_active,client_locked as 'l' from ec_clients";
			$param = null;
		} else {
			$query = "select client_id,client_firstname,client_lastname,client_email,client_active,client_locked as 'l' from ec_clients
					where client_id=:id";
			$param = array(':id' => $id);
		}
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$results = $stmt->fetchAll();
		return json_encode($results);
	}
	
	public function lockMember($id, $state) {
		$query = "update ec_clients set client_locked=:state where client_id=:id";
		$param = array(':state' => $state, ':id' => $id);
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* Tickets */
	
	public function countTickets() {
		$query = "select ticket_id from ec_tickets where ticket_status<>6";
		$param = null; 
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
	
	public function listTicket($ticketid = null) {
		if($ticketid == null) {
			$query = "select ticket_id,ticket_subject,ticket_status,ticket_date,status_id,status_label,client_firstname,
					client_lastname from ec_tickets join ec_ticket_status on (ticket_status = status_id) join ec_clients on 
					(ec_tickets.client_id = ec_clients.client_id)";
			$param = null;
		} else {
			$query = "select ticket_id,ticket_subject,ticket_message,ticket_status,ticket_date,status_id,status_label,ticket_id,
				ec_clients.client_id,client_firstname,client_lastname,ec_tickets.admin_id,admin_firstname,admin_lastname 
				from ec_tickets join ec_ticket_status on (ticket_status = status_id) join ec_clients on 
				(ec_clients.client_id = ec_tickets.client_id) left join ec_admin on (ec_tickets.admin_id = ec_admin.admin_id) 
				where ec_tickets.ticket_id=:tid";
			$param = array(':tid' => $ticketid);
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			throw new \Exception(T001);
		}
		return json_encode($result);
	}
	
	public function getReplies($ticketid) {
		$query = "select ticket_reply,reply_date,client_firstname,client_lastname,ec_ticket_replies.client_id,admin_firstname,
				admin_lastname from ec_ticket_replies join ec_tickets on (parent_id = ticket_id) join ec_clients on 
				(ec_clients.client_id = ec_tickets.client_id) left join ec_admin on (ec_ticket_replies.admin_id = ec_admin.admin_id) 
				where ec_tickets.ticket_id=:tid";
		$param = array(':tid' => $ticketid);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			null;
		}
		return json_encode($result);
	}
	
	public function replyTicket($adminid, $ticketid, $message) {
		AdminModel::changeTicket($ticketid, 2);
		$query = "insert into ec_ticket_replies (parent_id, admin_id, ticket_reply) values (:tid,:id,:msg)";
		$param = array(':tid' => $ticketid, ':id' => $adminid, ':msg' => $message);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function changeTicket($ticketid, $status) {
		$query = "update ec_tickets set ticket_status=:status where ticket_id=:tid";
		$param = array(':tid' => $ticketid, ':status' => $status);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function newTicket($userid, $adminid, $subject, $message) {
		$query = "insert into ec_tickets (client_id, admin_id, ticket_subject, ticket_status, ticket_message) values (:id,:aid,:subject,1,:msg)";
		$param = array(':subject' => $subject, ':id' => $userid, ':aid' => $adminid, ':msg' => $message);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function mySignature($id) {
		$query = "select admin_signature from ec_admin where admin_id=:id";
		$param = array(':id' => $id);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
	
	/* Pages */
	
	public function addPage($aid, $unique, $title, $data) {
		$query = "insert into ec_pages (admin_id,page_unique,page_title,page_data) values(:aid,:unique,:title,:data)";
		$param = array(':aid' => $aid, ':unique' => $unique, ':title' => $title, ':data' => $data);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
	
	public function editPage($pid, $aid, $title, $data) {
		$query = "update ec_pages set admin_id=:aid, page_title=:title, page_data=:data, page_edited=:edited where page_id=:pid";
		$param = array(':aid' => $aid, ':title' => $title, ':data' => $data, ':edited' => date("Y-m-d H:i:s"), ':pid' => $pid);

		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
	
	public function listPage($id = null) {
		if($id == null) {
			$query = "select page_id,page_unique,page_title,page_data,page_created,page_edited,admin_id,admin_firstname,admin_lastname 
					from ec_pages join ec_admin using (admin_id)";
			$param = null;
		} else {
			$query = "select page_id,page_unique,page_title,page_data,page_created,page_edited,admin_id,admin_firstname,admin_lastname 
					from ec_pages join ec_admin using (admin_id) where page_id=:id";
			$param = array(':id' => $id);
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	public function deletePage($id) {
		$query = "delete from ec_pages where page_id=:id";
		$param = array(':id' => $id);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* Orders */
	
	public function listOrders($id = null) {
		if($id == null) {
			$query = "select order_id,order_data,status_label,order_date from ec_orders join ec_order_status on (order_status = status_id)";
			$param = null;
		} else {
			$query = "select order_id,order_data,status_label,order_date,client_firstname,client_lastname from ec_orders join ec_order_status 
					on (order_status = status_id) join ec_clients using (client_id) where order_id=:id";
			$param = array(':id' => $id);
		}
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	public function editOrder($id, $status) {
		$query = "update ec_orders set order_status=:status where order_id=:id";
		$param = array(':id' => $id, ':status' => $status);
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	/* Admins */
}