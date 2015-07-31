<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root.'/Framework/Back_Default_Header.php';

class Layout_Model
{
    private $db; 
	
	public function __construct()
	{
		$this->db = new Mysqli_Tool(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	}
	
	/**
	 * getGeneralAppInfo
	 *
	 * get all the info that from the table app_info, this is about the application
	 * the name, url, creator and so
	 *
	 * @return array row containing the info
	 */
	
	public function getGeneralAppInfo()
	{
		try {
			$query = 'SELECT * FROM app_info';
	
			return $this->db->getRow($query);
	
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getUserInfo()
	{
		try {
			$query = "SELECT u.user_id, d.name, u.type, 
					ue.email as user_email, ue.inbox
					FROM users u 
					LEFT JOIN user_detail d ON u.user_id = d.user_id 
					LEFT JOIN user_emails ue ON u.user_id = ue.user_id
					WHERE u.user_id = ".$_SESSION['userId'];
			return $this->db->getRow($query);
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getActiveUsers()
	{
		try {
			$query = 'SELECT ud.user_id, ud.name 
					FROM users u 
					LEFT JOIN user_detail ud ON ud.user_id = u.user_id
					WHERE u.active = 1  
					';
			return $this->db->getArray($query);
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getLastMembers()
	{
		try {
			$filter = '';
			
			if ($_SESSION['loginType'] != 1)
			{
				$filter = 'WHERE m.user_id = '.$_SESSION['userId'];
			}
			
			$query = 'SELECT lpad(m.member_id, 4, 0) AS member_id, m.user_id, m.name, m.last_name, 
					m.address, m.city, m.state, m.country, m.active,
					d.name AS user_name
					FROM members m
					LEFT JOIN user_detail d ON m.user_id = d.user_id
					'.$filter.'
					 ORDER BY m.member_id DESC
					LIMIT 0, 10
					';
			
			return $this->db->getArray($query);
			
		} catch (Exception $e) {
			return false;
		}
	}

	public function getAllMembers()
	{
		try {
			$filter = '';
				
			if ($_SESSION['loginType'] != 1)
			{
				$filter = 'WHERE m.user_id = '.$_SESSION['userId'];
			}
				
			$query = 'SELECT lpad(m.member_id, 4, 0) AS member_id, m.user_id, m.name, 
					m.last_name, m.address, m.city, m.state, m.country, m.active,
					d.name AS user_name
					FROM members m
					LEFT JOIN user_detail d ON m.user_id = d.user_id
					'.$filter.'
					 ORDER BY m.member_id DESC
					';
				
			return $this->db->getArray($query);
				
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getAllCountries()
	{
		try {
			$query = 'SELECT Name, Code FROM Country;';
	
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getAllStatesByCountry($country)
	{
		try
		{
			$query = 'SELECT District, CountryCode 
					FROM City 
					WHERE CountryCode = "'.$country.'" 
					GROUP BY District;';
	
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getCitiesByEstate($code)
	{
		try
		{
			$query = 'SELECT Name, CountryCode 
					FROM City 
					WHERE District = "'.$code.'" 
					ORDER BY Name;';
	
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function addMember($data)
	{
		try {
			$query = 'INSERT INTO members(name, user_id, last_name, address, city, state, country, notes, active, date)
						VALUES(?, '.$_SESSION["userId"].', ?, ?, ?, ?, ?, ?, 1, CURDATE());';
			
			$prep = $this->db->prepare($query);
			
			$prep->bind_param('sssssss',
					$data['memberName'],
					$data['memberLastName'],
					$data['memberAddress'],
					$data['city'],
					$data['mState'],
					$data['country'],
					$data['notes']);
			
			if ($prep->execute())
			{
				return $prep->insert_id;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function addMemberEmail($data)
	{
		try
		{	
			$query = 'INSERT INTO member_emails (member_id, email, active) 
					VALUES(?, ?, 1)';
	
			$prep = $this->db->prepare($query);
			 
			$prep->bind_param('is',
					$data['memberId'],
					$data['emailVal']);
			 
			return $prep->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function addMemberPhone($data)
	{
		try
		{
			$query = 'INSERT INTO member_phones(member_id, phone, active) 
					VALUES(?, ?, 1)';
	
			$prep = $this->db->prepare($query);
			 
			$prep->bind_param('is',
					$data['memberId'],
					$data['phoneVal']);
			 
			return $prep->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getMemberByMemberId($memberId)
	{
		try {
			$query = 'SELECT m.*, c.Name as country, c.Code as country_code
					FROM members m
					LEFT JOIN Country c ON m.country = c.Code
					WHERE m.member_id = 
					'.$memberId;
			return $this->db->getRow($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getMemberEmailsById($memberId)
	{
		try {
			$query = 'SELECT * FROM member_emails WHERE member_id = '.$memberId;
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getMemberPhonesById($memberId)
	{
		try {
			$query = 'SELECT * FROM member_phones WHERE member_id = '.$memberId;
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getMemberHistoryById($memberId)
	{
		try {
			$query = 'SELECT mh.* , ud.name
					FROM member_history mh 
					LEFT JOIN user_detail ud ON mh.user_id = ud.user_id
					WHERE mh.member_id = '.$memberId.'
					ORDER BY mh.history_id DESC		
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function addHistory($data)
	{
	    try
	    {
	    	$query = 'INSERT INTO member_history(user_id, member_id, date, time, history) 
	    			VALUES('.$_SESSION["userId"].', ?, CURDATE(), CURTIME(), ?)';
			
	        $prep = $this->db->prepare($query);

	        $prep->bind_param('is', 
	        		$data['memberId'],
	        		$data['historyEntry']);
			
             return $prep->execute();
	    }
	    catch (Exception $e)
	    {
	    	echo $e->getMessage();
	    }
	}
	
	public function getHistoryEntries($member_id)
	{
		try 
		{
			$member_id = (int) $member_id;
			$query = 'SELECT h.*, ud.name
					FROM member_history h
					LEFT JOIN user_detail ud ON ud.user_id = h.user_id
					WHERE h.member_id = '.$member_id.'
					ORDER BY h.history_id DESC';
			
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;			
		}
	}
	
	public function addMemberTask($data)
	{
		$date = Tools::formatToMYSQL($data['task_date']);
	
		$time = $data['task_hour'].':00';
		$member_id = (int) $data['memberId'];
		try {
			$query = 'INSERT INTO member_tasks(task_to, task_from, date, created_on, time, content, member_id)
					VALUES(?, ?, ?, CURDATE(), ?, ?, ?)';
			$prep = $this->db->prepare($query);
				
			$prep->bind_param('iisssi',
					$data['task_to'],
					$_SESSION['userId'],
					$date,
					$time,
					$data['task_content'],
					$member_id);
			// 			Pretty good piece of code!
			// 			if(!$prep->execute())
				// 			{
				// 				printf("Errormessage: %s\n", $prep->error);
				// 			}
				return $prep->execute();
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}
	
	public function getMemberTaskByMemberId($member_id)
	{
		try {
			$member_id = (int) $member_id;
			
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					WHERE t.member_id = '.$member_id.'
					ORDER BY t.date ASC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getAllMemberTasks()
	{
		try {
			$member_id = (int) $member_id;
			
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to,
					m.name, m.last_name
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					LEFT JOIN members m ON m.member_id = t.member_id
					WHERE t.status = 0
					ORDER BY t.date DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getAllTasksByUser()
	{
		try {
			$member_id = (int) $member_id;
	
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to,
					m.name, m.last_name
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					LEFT JOIN members m ON m.member_id = t.member_id
					WHERE t.assigned_to = '.$_SESSION['userId'].'
					AND t.status = 0
					ORDER BY t.date DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getTotalTodayTasksByMemberId()
	{
		try {
			$query = 'SELECT COUNT(*) 
					FROM member_tasks 
					WHERE date = CURDATE() 
					AND task_to = '.$_SESSION['userId'].'
					AND status = 0';
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getTodayTasksByUser()
	{
		try {
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to,
					m.name, m.last_name
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					LEFT JOIN members m ON m.member_id = t.member_id
					WHERE t.date = CURDATE() 
					AND t.task_to = '.$_SESSION['userId'].'
					AND t.status = 0
					ORDER BY t.date DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getTotalPendingTasksByMemberId()
	{
		try {
			$query = 'SELECT COUNT(*) 
					FROM member_tasks 
					WHERE date < CURDATE()
					AND task_to = '.$_SESSION['userId'].'
					AND status = 0';
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getPendingTasksByUser()
	{
		try {
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to,
					m.name, m.last_name
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					LEFT JOIN members m ON m.member_id = t.member_id
					WHERE t.date < CURDATE()
					AND t.task_to = '.$_SESSION['userId'].'
					AND t.status = 0
					ORDER BY t.date DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getTotalFutureTasksByMemberId()
	{
		try {
			$query = 'SELECT COUNT(*)
					FROM member_tasks
					WHERE date > CURDATE()
					AND task_to = '.$_SESSION['userId'].'
					AND status = 0';
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getFutureTasksByUser()
	{
		try {
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to,
					m.name, m.last_name
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					LEFT JOIN members m ON m.member_id = t.member_id
					WHERE t.date > CURDATE()
					AND t.task_to = '.$_SESSION['userId'].'
					AND t.status = 0
					ORDER BY t.date DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getCompletedTasksByUser()
	{
		try {
			$query = 'SELECT t.*,
					ud.name AS assigned_by,
					uds.name AS assigned_to,
					m.name, m.last_name
					FROM member_tasks t
					LEFT JOIN user_detail ud ON ud.user_id = t.task_from
					LEFT JOIN user_detail uds ON uds.user_id = t.task_to
					LEFT JOIN members m ON m.member_id = t.member_id
					WHERE t.task_to = '.$_SESSION['userId'].'
					AND t.status = 1
					ORDER BY t.date DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getRecentMembers()
	{
		try {
			$query = 'SELECT COUNT(*) FROM members WHERE date = CURDATE() AND user_id = '.$_SESSION['userId'];
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getRecentBrokers()
	{
		try {
			$query = 'SELECT COUNT(*) FROM brokers WHERE date = CURDATE() AND user_id = '.$_SESSION['userId'];
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function completeTask($task_id)
	{
		try {
			$task_id = (int) $task_id;
			$query = 'UPDATE member_tasks SET status = 1, completed_by = '.$_SESSION['userId'].', completed_date = CURDATE()
					WHERE task_id = '.$task_id;
			return $this->db->run($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function addBroker($data)
	{
		try {
			$query = 'INSERT INTO brokers(name, user_id, last_name, address, city, state, country, notes, active, date)
						VALUES(?, '.$_SESSION["userId"].', ?, ?, ?, ?, ?, ?, 1, CURDATE());';
				
			$prep = $this->db->prepare($query);
				
			$prep->bind_param('sssssss',
					$data['memberName'],
					$data['memberLastName'],
					$data['memberAddress'],
					$data['city'],
					$data['mState'],
					$data['country'],
					$data['notes']);
				
			if ($prep->execute())
			{
				return $prep->insert_id;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function addBrokerEmail($data)
	{
		try
		{
			$query = 'INSERT INTO broker_emails(broker_id, email, active)
					VALUES(?, ?, 1)';
	
			$prep = $this->db->prepare($query);
	
			$prep->bind_param('is',
					$data['memberId'],
					$data['emailVal']);
	
			return $prep->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function addBrokerPhone($data)
	{
		try
		{
			$query = 'INSERT INTO broker_phones(broker_id, phone, active)
					VALUES(?, ?, 1)';
	
			$prep = $this->db->prepare($query);
	
			$prep->bind_param('is',
					$data['memberId'],
					$data['phoneVal']);
	
			return $prep->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function addHistoryBroker($data)
	{
		try
		{
	
			$query = 'INSERT INTO broker_history(user_id, broker_id, date, time, history)
	    			VALUES('.$_SESSION["userId"].', ?, CURDATE(), CURTIME(), ?)';
				
			$prep = $this->db->prepare($query);
	
			$prep->bind_param('is',
					$data['memberId'],
					$data['historyEntry']);
				
			return $prep->execute();
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function getHistoryBrokerEntries($member_id)
	{
		try
		{
			$member_id = (int) $member_id;
			$query = 'SELECT h.*, ud.name
					FROM broker_history h
					LEFT JOIN user_detail ud ON ud.user_id = h.user_id
					WHERE h.broker_id = '.$member_id.'
					ORDER BY h.history_id DESC';
				
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getAllBrokers()
	{
		try {
			$filter = '';
	
			if ($_SESSION['loginType'] != 1)
			{
				$filter = 'WHERE m.user_id = '.$_SESSION['userId'];
			}
	
			$query = 'SELECT lpad(m.broker_id, 4, 0) AS broker_id, m.user_id, m.name,
					m.last_name, m.address, m.city, m.state, m.country, m.active, m.date, 
					d.name AS user_name
					FROM brokers m
					LEFT JOIN user_detail d ON m.user_id = d.user_id
					'.$filter.'
					 ORDER BY m.broker_id DESC
					';
	
			return $this->db->getArray($query);
	
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getBrokerByBrokerId($brokerId)
	{
		try {
			$query = 'SELECT m.*, c.Name as country, c.Code as country_code
					FROM brokers m
					LEFT JOIN Country c ON m.country = c.Code
					WHERE m.broker_id =
					'.$brokerId;
			return $this->db->getRow($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getBrokerEmailsById($brokerId)
	{
		try {
			$query = 'SELECT * FROM broker_emails WHERE broker_id = '.$brokerId;
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getBrokerPhonesById($brokerId)
	{
		try {
			$query = 'SELECT * FROM broker_phones WHERE broker_id = '.$brokerId;
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getBrokerHistoryById($brokerId)
	{
		try {
			$query = 'SELECT mh.* , ud.name
					FROM broker_history mh
					LEFT JOIN user_detail ud ON mh.user_id = ud.user_id
					WHERE mh.broker_id = '.$brokerId.'
					ORDER BY mh.history_id DESC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getLastBrokers()
	{
		try {
			$filter = '';
	
			if ($_SESSION['loginType'] != 1)
			{
				$filter = 'WHERE m.user_id = '.$_SESSION['userId'];
			}
	
			$query = 'SELECT lpad(m.broker_id, 4, 0) AS broker_id, m.user_id, m.name,
					m.last_name, m.address, m.city, m.state, m.country, m.active, m.date, 
					d.name AS user_name
					FROM brokers m
					LEFT JOIN user_detail d ON m.user_id = d.user_id
					'.$filter.'
					 ORDER BY m.broker_id DESC
					LIMIT 0, 10
					';
	
			return $this->db->getArray($query);
	
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getAllReservations()
	{
		try {
			$query = 'SELECT s.reservation_id, 
					s.check_in,
					DATE_ADD(s.check_out, INTERVAL 1 DAY) AS check_out,
					rt.room_type,
					rt.abbr,
					r.room,
					m.name,
					m.last_name
					FROM reservations s
					LEFT JOIN rooms r ON s.room_id = r.room_id
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					LEFT JOIN members m ON m.member_id = s.member_id
					WHERE s.status = 1	
					';
			
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function searchRooms($data)
	{
		$checkIn = Tools::formatToMYSQL($data['checkIn']);
		$checkOut = Tools::formatToMYSQL($data['checkOut']);
	
		$member_id = (int) $data['memberId'];
		try {
			$query = 'SELECT r.*, rt.room_type_id, rt.room_type
			FROM rooms r
			LEFT JOIN room_types rt ON r.room_type_id = rt.room_type_id
			WHERE r.room_id NOT IN (SELECT room_id
			FROM reservations 
			WHERE (check_in <= "'.$checkIn.'" AND check_out >="'.$checkIn.'")
			OR (check_in <= "'.$checkOut.'" AND check_out >="'.$checkOut.'")
			OR (check_in >= "'.$checkIn.'" AND check_out <= "'.$checkOut.'"));';
			
			return $this->db->getArray($query);
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}
	
	public function addMemberFromReservation($data)
	{
		try {
			$query = 'INSERT INTO members(name, user_id, last_name, active, date)
						VALUES(?, '.$_SESSION["userId"].', ?, 1, CURDATE());';
				
			$prep = $this->db->prepare($query);
				
			$prep->bind_param('ss',
					$data['memberName'],
					$data['memberLastName']);
			if ($prep->execute())
			{
				return $prep->insert_id;
			}
		} catch (Exception $e) {
			return false;
		}
	}
	
// 	public function addMemberFromReservation($data)
// 	{
// 		try {
// 			$query = 'INSERT INTO members(name, user_id, last_name, active, date)
// 						VALUES(?, '.$_SESSION["userId"].', ?, 1, CURDATE());';
	
// 			$prep = $this->db->prepare($query);
	
// 			$prep->bind_param('ss',
// 					$data['memberName'],
// 					$data['memberLastName']);
	
// 			if ($prep->execute())
// 			{
// 				return $prep->insert_id;
// 			}
// 		} catch (Exception $e) {
// 			return false;
// 		}
// 	}
	
	public function addReservation($data)
	{
		$checkIn = Tools::formatToMYSQL($data['checkIn']);
		$checkOut = Tools::formatToMYSQL($data['checkOut']);
		
		try {
			$query = 'INSERT INTO reservations(member_id, room_id, check_in, check_out, date, price, status, adults, children, agency, price_per_night)
					VALUES('.$data['memberId'].', '.$data['roomId'].', "'.$checkIn.'", 
						"'.$checkOut.'", CURDATE(), '.$data['price'].', 1, '.$data['reservationAdults'].', 
						'.$data['reservationChildren'].', '.$data['agency'].', '.$data['pricePerNight'].')';
			echo $query;
			return $this->db->run($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getMemberReservationsByMemberId($memberId)
	{
		$memberId = (int) $memberId;
		try {
			$query = 'SELECT s.reservation_id,
					s.check_in,
					s.check_out,
					s.date,
					s.price,
					s.adults,
					s.children,
					rt.room_type,
					rt.abbr,
					r.room,
					m.name,
					m.last_name
					FROM reservations s
					LEFT JOIN rooms r ON s.room_id = r.room_id
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					LEFT JOIN members m ON m.member_id = s.member_id
					WHERE s.status = 1 AND s.member_id = '.$memberId;
				
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getAllRooms()
	{
		try {
			$query = 'SELECT r.*, rt.room_type, rt.abbr
					FROM rooms r
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id 
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getReservationsByRoomId($room_id)
	{
		try {
			$room_id = (int) $room_id;
			$query = 'SELECT s.reservation_id, 
					s.check_in,
					s.check_out,
					rt.room_type,
					rt.abbr,
					r.room,
					m.member_id, 
					m.name,
					m.last_name
					FROM reservations s
					LEFT JOIN rooms r ON s.room_id = r.room_id
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					LEFT JOIN members m ON m.member_id = s.member_id
					WHERE s.status = 1 AND r.room_id = '.$room_id.' ORDER BY s.check_in';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function addAgency($agency)
	{
		try {
		$query = 'INSERT INTO agencies(agency)
						VALUES(?);';
				
			$prep = $this->db->prepare($query);
				
			$prep->bind_param('s',
					$agency);
			
			return $prep->execute();
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getAgencies()
	{
		try {
			$query = 'SELECT * FROM agencies ORDER BY agency_id DESC';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
}















