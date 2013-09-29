<?php
namespace app\Models;

class SupportModel {
	private $db;
	
	public function __construct($database) {
		$this->db = $database;
	}
	
	
	/** 
	 * All the ticket methods are listed below. I've kept things as short as 
	 * possible in here because there is no point making things overly 
	 * complicated! - Denver
	 */
	
	public function listTicket($userid, $ticketid = null) {
		if($ticketid == null) {
			$query = "select ticket_id,ticket_subject,ticket_status,ticket_date,status_id,status_label from 
					ec_tickets join	ec_ticket_status on (ticket_status = status_id) where client_id=:id";
			$param = array(':id' => $userid);
		} else {
			$query = "select ticket_id,ticket_subject,ticket_message,ticket_status,ticket_date,status_id,status_label,ticket_id,
					client_firstname,client_lastname,ec_clients.client_id,ec_tickets.admin_id,admin_firstname,admin_lastname from 
					ec_tickets join ec_ticket_status on (ticket_status = status_id) join ec_clients on 
					(ec_clients.client_id = ec_tickets.client_id) left join ec_admin using (admin_id) where ec_tickets.client_id=:id and 
					ec_tickets.ticket_id=:tid";
			$param = array(':id' => $userid, ':tid' => $ticketid);
		}
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			throw new \Exception(T001);
		}
		return json_encode($result);
	}
	
	public function getReplies($userid, $ticketid) {
		$query = "select ticket_reply,reply_date,client_firstname,client_lastname,ec_ticket_replies.client_id,admin_firstname,admin_lastname
				from ec_ticket_replies join ec_tickets on (parent_id = ticket_id) join ec_clients on (ec_clients.client_id = ec_tickets.client_id) 
				left join ec_admin on (ec_ticket_replies.admin_id = ec_admin.admin_id) where ec_tickets.client_id=:id and ec_tickets.ticket_id=:tid";
		$param = array(':id' => $userid, ':tid' => $ticketid);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		$result = $stmt->fetchAll();
		if($stmt->rowCount() == 0) {
			null;
		}
		return json_encode($result);
	}
	
	public function replyTicket($userid, $ticketid, $message) {
		SupportModel::changeTicket($userid, $ticketid, 3);
		$query = "insert into ec_ticket_replies (parent_id, client_id, ticket_reply) values (:tid,:id,:msg)";
		$param = array(':tid' => $ticketid, ':id' => $userid, ':msg' => $message);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function changeTicket($userid, $ticketid, $status) {
		$query = "update ec_tickets set ticket_status=:status where client_id=:id and ticket_id=:tid";
		$param = array(':id' => $userid, ':tid' => $ticketid, ':status' => $status);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function newTicket($userid, $subject, $message) {
		$query = "insert into ec_tickets (client_id, ticket_subject, ticket_status, ticket_message) values (:id,:subject,1,:msg)";
		$param = array(':subject' => $subject, ':id' => $userid, ':msg' => $message);
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
	}
	
	public function countTickets($userid) {
		$query = "select ticket_id from ec_tickets where ticket_status<>'6' and client_id=:id";
		$param = array(':id' => $userid);
		
		$stmt = $this->db->prepare($query);
		$stmt->execute($param);
		return $result = $stmt->fetchAll();
	}
}