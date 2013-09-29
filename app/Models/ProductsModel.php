<?php 
namespace app\Models;
class ProductsModel {
	private $db;
	
	public function __construct($database) {
		$this->db = $database; 
	}
	
	public function fetchCat($catGId = null) {
		$query = "select cat_id,cat_label from ec_categories where cat_active='1'";
		$param = null;
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("No categories.");
		} else {
			return $result = $stmt->fetchAll();
		}
	}
	
	public function fetchProductsByCat($id = null) {
		if($id == null) {
			throw new \Exception("Please select a valid category.");
		} else {
			$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using (cat_id) 
					join ec_pictures using (product_id) where cat_id=:id";
			$param = array(':id' => $id);

			$stmt = $this->db->prepare($query);
			$stmt->execute($param);
			if($stmt->rowCount() == 0) {
				throw new \Exception("There are no products in this category.");
			} else {
				$result = $stmt->fetchAll();
				return json_encode($result);
			}
		}
	}
	
	public function fetchProducts() {
		$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using (cat_id)
					join ec_pictures using (product_id) where picture_main='1';";
		$param = null;
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("There are no products to display.");
		} else {
			$result = $stmt->fetchAll();
			return json_encode($result);
		}
	}
	
	public function fetchProductById($id) {
		$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using 
					(cat_id) left join ec_pictures using (product_id) where product_id=:id and picture_main='1' limit 1";
		$param = array(':id' => $id);
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("There are no products to display.");
		} else {
			$result = $stmt->fetchAll();
			return json_encode($result);
		} 
	}
	
	public function populateCart($id) {
		$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using
					(cat_id) left join ec_pictures using (product_id) where product_id=:id and picture_main='1'";
		$param = array(':id' => $id);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("Empty cart.");
		} else {
			$result = $stmt->fetchAll();
			return $result;
		}
	}
	
	public function buildTotal($id) {
		$query = "select product_price from ec_products where product_id=:id";
		$param = array(':id' => $id);
	
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return $result[0]['product_price'];
	}

	public function fetchLatestProducts($limit) {
		$query = "select product_id,product_name,product_price,product_desc,product_weight,cat_label,
					cat_id,picture_name,picture_caption from ec_products join ec_categories using 
					(cat_id) left join ec_pictures using (product_id) where picture_main='1' limit %d";
		$param = null;
		
		$stmt = $this->db->prepare(sprintf($query, $limit));
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("There are no products to display.");
		} else {
			$result = $stmt->fetchAll();
			return json_encode($result);
		}
	}
	
	public function fetchMorePictures($productid) {
		$query = "select product_id,picture_name,picture_caption from ec_pictures where picture_main='0' and product_id=:id";
		$param = array(':id' => $productid);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		if($stmt->rowCount() == 0) {
			throw new \Exception("There are no more images to display.");
		} else {
			$result = $stmt->fetchAll();
			return json_encode($result);
		}
	}
	
	public function addOrder($uid, $data, $status) {
		$deets = serialize($data);
		$query = "insert into ec_orders (client_id,order_data,order_status) values (:uid,:data,:status)";
		$param = array(':uid' => $uid, ':data' => $deets, ':status' => $status);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function listOrders($uid, $id = null) {
		if($id == null) {
			$query = "select order_id,order_data,status_label,order_date from ec_orders join ec_order_status on (order_status = status_id)
					where client_id=:uid";
			$param = array(':uid' => $uid);
		} else {
			$query = "select order_id,order_data,status_label,order_date from ec_orders join ec_order_status on (order_status = status_id)
					where order_id=:id";
			$param = array(':id' => $id);
		}
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		return json_encode($result);
	}
	
	public function editOrder($id, $uid, $status) {
		$query = "update ec_orders set order_status=:status where order_id=:id and client_id=:uid";
		$param = array(':id' => $id, ':uid' => $uid, ':status' => $status);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
}
