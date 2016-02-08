<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root.'/Framework/Back_Default_Header.php';

/**
 * Contains the methods for interact with the databases
 *
 * @package    Reservation System
 * @subpackage Tropical Casa Blanca Hotel
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Raul Castro <rd.castro.silva@gmail.com>
 */
class Layout_Model
{
    private $db; 
	
    /**
     * Initialize the MySQL Link
     */
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
	
	/**
	 * Get the user info
	 * 
	 * Get's the user detail {user_id, name, ...}
	 * 
	 * @return mixed|bool An array of info or false
	 */
	public function getUserInfo()
	{
		try {
			$query = "SELECT 
					u.user_id,
					u.type,
					d.name, 
					u.type, 
					ue.email as user_email, 
					ue.inbox
					FROM users u 
					LEFT JOIN user_detail d ON u.user_id = d.user_id 
					LEFT JOIN user_emails ue ON u.user_id = ue.user_id
					WHERE u.user_id = ".$_SESSION['userId'];
			return $this->db->getRow($query);
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Get only the active users
	 * 
	 * @return mixed|bool An array of info or false
	 */
	public function getActiveUsers()
	{
		try {
			$query = 'SELECT 
					ud.user_id, 
					ud.name 
					FROM users u 
					LEFT JOIN user_detail ud ON ud.user_id = u.user_id
					WHERE u.active = 1  
					';
			return $this->db->getArray($query);
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Get the last 10 members added
	 * 
	 * If the user is an admin then all the members will show
	 * If not, only the user that belongs to the user will be show
	 * 
	 * @return mixed|bool An array of info or false
	 */
	public function getLastMembers()
	{
		try {
			$filter = '';
			
			if ($_SESSION['loginType'] != 1)
			{
				$filter = 'WHERE m.user_id = '.$_SESSION['userId'];
			}
			
			$query = 'SELECT 
					lpad(m.member_id, 4, 0) AS member_id, 
					m.user_id, 
					m.name, 
					m.last_name, 
					m.address, 
					m.city, 
					m.state, 
					m.country, 
					m.active,
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

	/**
	 * Get all the members 
	 * 
	 * With all the details
	 * 
	 * @return mixed|bool An array of info or false
	 */
	public function getAllMembers()
	{
		try {
			$filter = '';
				
			if ($_SESSION['loginType'] != 1)
			{
				$filter = 'WHERE m.user_id = '.$_SESSION['userId'];
			}
				
			$query = 'SELECT lpad(m.member_id, 4, 0) AS member_id, 
					m.user_id, 
					m.name, 
					m.last_name, 
					m.address, 
					m.city, 
					m.state, 
					m.country, 
					m.active,
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
	
	public function updateMember($data)
	{
		try {
			$query = 'UPDATE members 
					SET name 	= ?, 
					last_name 	= ?, 
					address 	= ?, 
					city 		= ?, 
					state 		= ?, 
					country 	= ?, 
					notes 		= ?
					WHERE member_id = ?';
			
			$prep = $this->db->prepare($query);
			
			$prep->bind_param('sssssssi',
					$data['memberName'],
					$data['memberLastName'],
					$data['memberAddress'],
					$data['city'],
					$data['mState'],
					$data['country'],
					$data['notes'],
					$data['memberId']
					);
			
			if ($prep->execute())
			{
				return $data['memberId'];
			}
			else {
				printf("Errormessage: %s\n", $prep->error);
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
	
	public function updateMemberEmail($data)
	{
		try {
			$query = 'UPDATE member_emails 
					SET email = ? 
					WHERE email_id = ?';
			
			$prep = $this->db->prepare($query);
			
			$prep->bind_param('si', $data['emailVal'], $data['emailId']);
			
			return $prep->execute();
			
		} catch (Exception $e) {
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
	
	public function updateMemberPhone($data)
	{
		try {
			$query = 'UPDATE member_phones 
					SET phone = ? 
					WHERE phone_id = ?';
			
			$prep = $this->db->prepare($query);
			$prep->bind_param('si', 
					$data['phoneVal'], 
					$data['phoneId']);
			
			return $prep->execute();
			
		} catch (Exception $e) {
			printf("Errormessage: %s\n", $prep->error);
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
			$query = 'SELECT * 
					FROM member_emails 
					WHERE member_id = '.$memberId;
			
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
	
	/**
	 * addAgency
	 *
	 * add an agency on the agency table
	 *
	 * @param ustring $agency
	 * @return true on success | false on fail
	 */
	public function addAgency($agency)
	{
		try {
			$query = 'INSERT INTO agencies(agency)
						VALUES(?);';
	
			$prep = $this->db->prepare($query);
	
			$prep->bind_param('s', $agency);
				
			return $prep->execute();
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getAgencies
	 *
	 * returns an array of agencies
	 *
	 * @return multitype:array of agencies on success false on fail
	 */
	public function getAgencies()
	{
		try {
			$query = 'SELECT * FROM agencies ORDER BY agency_id DESC';
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
					';
			
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * searchRooms
	 * 
	 * Execute a search for available rooms depending on check-in & check-out
	 * 
	 * @param array $data
	 * @return multitype:a list of available rooms | false on fail
	 */
	public function searchRooms($data)
	{
		$checkIn 	= Tools::formatToMYSQL($data['checkIn']);
		$checkIn	= date($checkIn);
		
		$checkOut 	= Tools::formatToMYSQL($data['checkOut']);
		$checkOut 	= date($checkOut);
		
		try {
			$query = 'SELECT r.*, rt.room_type_id, rt.room_type
			FROM rooms r
			LEFT JOIN room_types rt ON r.room_type_id = rt.room_type_id
			WHERE r.room_id NOT IN (SELECT room_id
			FROM reservations 
			WHERE (check_in < "'.$checkOut.'" AND check_out >="'.$checkOut.'")
			OR (check_in <= "'.$checkIn.'" AND check_out >"'.$checkIn.'")
			OR (check_in >= "'.$checkIn.'" AND check_out <= "'.$checkOut.'"))
			ORDER BY r.room_order ASC		
			;';
// 			echo $query;
			return $this->db->getArray($query);
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}
	
	/**
	 * searchSingleRoom
	 *
	 * Execute a search for the availability of a singles specific room, according to check in and check out date
	 *
	 * @param array $data
	 * @return multitype:a list of available rooms | false on fail
	 */
	public function searchSingleRoom($data)
	{
		$checkIn 	= Tools::formatToMYSQL($data['checkIn']);
		$checkOut 	= Tools::formatToMYSQL($data['checkOut']);
	
		$room_id = (int) $data['roomId'];
		try {
			$query = 'SELECT r.*, rt.room_type_id, rt.room_type
			FROM rooms r
			LEFT JOIN room_types rt ON r.room_type_id = rt.room_type_id
			WHERE r.room_id NOT IN (SELECT room_id
			FROM reservations
			WHERE (check_in < "'.$checkOut.'" AND check_out >="'.$checkOut.'")
			OR (check_in <= "'.$checkIn.'" AND check_out >"'.$checkIn.'")
			OR (check_in >= "'.$checkIn.'" AND check_out <= "'.$checkOut.'"))
			AND r.room_id = '.$data['roomId'].'
			ORDER BY r.room_order ASC
			;';
			return $this->db->getRow($query);
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
	
	/**
	 * addReservation
	 * 
	 * add a new reservation with a room id, check-in and checkout and other parameters
	 * 
	 * @param array $data
	 * @return true on success | false on fail
	 */
	public function addReservation($data)
	{
		$checkIn 		= Tools::formatToMYSQL($data['checkIn']);
		$checkInDate	= date($checkIn);
		$checkInDate	= date('Y-m-d', strtotime('+1 day', strtotime($checkInDate)));
		
		$checkOut 		= Tools::formatToMYSQL($data['checkOut']);
		$checkOutDate 	= date($checkOut);
		$checkOutDate 	= date('Y-m-d', strtotime('-1 day', strtotime($checkOutDate)));
		
		try {
			$query = 'INSERT INTO
					reservations(
						member_id,
						room_id,
						check_in,
						check_out,
						date,
						price,
						status,
						adults,
						children,
						agency,
						price_per_night,
						external_id)
					VALUES(?, ?, ?, ?, CURDATE(), ?, 1, ?, ?, ?, ?, ?)';

			$prep = $this->db->prepare($query);
			
			$prep->bind_param('iissiiiiis',
					$data['memberId'],
					$data['roomId'],
					$checkIn,
					$checkOut,
					$data['price'],
					$data['reservationAdults'],
					$data['reservationChildren'],
					$data['agency'],
					$data['pricePerNight'],
					$data['externalId']
					);
			
			if ($prep->execute())
			{
// 				$info = array('reservationId' => $prep->insert_id, 'description' => "Staying cost", 'cost' => $data['price']);
// 				$this->addPayment($info);
				return $prep->insert_id;
				
			}
			
// 			return $this->db->run($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getMemberReservationByMemberId
	 * 
	 * it gets all the reservation related to a member
	 * 
	 * @param int $memberId
	 * @return array on success | false on fail
	 */
	public function getMemberReservationsByMemberId($memberId)
	{
		$memberId = (int) $memberId;
		try {
			$query = 'SELECT s.reservation_id,
					s.check_in,
					DATE_ADD(s.check_in, INTERVAL -1 DAY) AS check_in_mask,
					s.check_out,
					DATE_ADD(s.check_out, INTERVAL 1 DAY) AS check_out_mask,
					s.date,
					s.price,
					s.adults,
					s.children,
					s.status,
					s.external_id,
					s.room_id,
					rt.room_type,
					rt.abbr,
					r.room,
					m.name,
					m.last_name,
					a.agency,
					a.agency_id
					FROM reservations s
					LEFT JOIN rooms r ON s.room_id = r.room_id
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					LEFT JOIN members m ON m.member_id = s.member_id
					LEFT JOIN agencies a ON s.agency = a.agency_id
					WHERE s.member_id = '.$memberId.' ORDER BY s.check_in ASC';
				
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getMemberCancelationsByMemberId
	 *
	 * it gets all the reservation canceled related to a member
	 *
	 * @param int $memberId
	 * @return array on success | false on fail
	 */
	public function getMemberCancelationsByMemberId($memberId)
	{
		$memberId = (int) $memberId;
		try {
			$query = 'SELECT s.reservation_id,
					s.check_in,
					DATE_ADD(s.check_in, INTERVAL -1 DAY) AS check_in_mask,
					s.check_out,
					DATE_ADD(s.check_out, INTERVAL 1 DAY) AS check_out_mask,
					s.date,
					s.price,
					s.adults,
					s.children,
					s.status,
					s.external_id,
					s.room_id,
					rt.room_type,
					rt.abbr,
					r.room,
					m.name,
					m.last_name,
					a.agency
					FROM cancelations s
					LEFT JOIN rooms r ON s.room_id = r.room_id
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					LEFT JOIN members m ON m.member_id = s.member_id
					LEFT JOIN agencies a ON s.agency = a.agency_id
					WHERE s.member_id = '.$memberId.' ORDER BY s.reservation_id DESC';
	
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getAllRooms
	 * 
	 * Returns the collection of rooms on table rooms
	 * it works for the section 'Rooms'
	 * 
	 * @return multitype:unknown |boolean
	 */
	
	public function getAllRooms()
	{
		try {
			$query = 'SELECT r.*, rt.room_type, rt.abbr
					FROM rooms r
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id 
					ORDER BY r.room_order ASC
					';
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getSingleRoomById
	 *
	 * Return the info a single room by a given room id
	 *
	 * @return multitype:unknown |boolean
	 */
	
	public function getSingleRoomById($roomId)
	{
		try {
			$roomId = (int) $roomId;
			$query = 'SELECT r.*, rt.room_type, rt.abbr
					FROM rooms r
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					WHERE r.room_id = '.$roomId.'
					';
			return $this->db->getRow($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getReservationsByRoomId
	 * 
	 * returns all the reservations related to an specific room
	 * 
	 * @param int $room_id
	 * @return multitype:array of reservations on success | false on fail
	 */
	public function getReservationsByRoomId($room_id)
	{
		try {
			$room_id = (int) $room_id;
			$query = 'SELECT s.reservation_id, 
					s.check_in,
					DATE_ADD(s.check_in, INTERVAL -1 DAY) AS check_in_mask,
					s.check_out,
					DATE_ADD(s.check_out, INTERVAL 1 DAY) AS check_out_mask,
					s.status,
					s.reservation_id,
					rt.room_type,
					rt.abbr,
					r.room,
					m.member_id, 
					m.name,
					m.last_name,
					a.agency
					FROM reservations s
					LEFT JOIN rooms r ON s.room_id = r.room_id
					LEFT JOIN room_types rt ON rt.room_type_id = r.room_type_id
					LEFT JOIN members m ON m.member_id = s.member_id
					LEFT JOIN agencies a ON s.agency = a.agency_id
					WHERE r.room_id = '.$room_id.' ORDER BY s.check_in';
			
			return $this->db->getArray($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * addPayment
	 * 
	 * add a payment item to an reservation
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function addPayment($data)
	{
		try {
			$query = 'INSERT INTO payments(reservation_id, description, cost, staying)
						VALUES(?, ?, ?, ?);';
	
			$prep = $this->db->prepare($query);
	
			$prep->bind_param('isii',
					$data['reservationId'],
					$data['description'],
					$data['cost'],
					$data['staying']);
			
			return $prep->execute();
// 			if ($prep->execute())
// 			{
// 				return $prep->insert_id;
// 			}
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getPaymentsByReservationId
	 * 
	 * get all the payments related to a reservation
	 * 
	 * @param int $reservation_id
	 * @return multitype:unknown |boolean
	 */
	public function getPaymentsByReservationId($reservation_id)
	{
		try {
			$reservation_id = (int) $reservation_id;
			
			$query = "SELECT * FROM payments WHERE reservation_id = ".$reservation_id;
			return $this->db->getArray($query);
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getReservationGrandTotalByReservationId
	 * 
	 * returns the sum of the active payments by reservation id
	 * 
	 * @param int $reservation_id
	 * @return int | false on failed
	 */
	public function getReservationGrandTotalByReservationId($reservation_id)
	{
		try {
			$reservation_id = (int) $reservation_id;
			
			$stayingTotal = $this->getReservationStayingCostTotal($reservation_id);
			
			$query = 'SELECT SUM(cost) as grand_total FROM payments WHERE reservation_id = '.$reservation_id." AND active = 1 AND staying = 0";
			
			return ($this->db->getValue($query) + $stayingTotal);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getReservationPaidByReservationId
	 *
	 * returns the sum of the paid payments by reservation id
	 *
	 * @param int $reservation_id
	 * @return int | false on failed
	 */
	public function getReservationPaidByReservationId($reservation_id)
	{
		try {
			$reservation_id = (int) $reservation_id;
			$query = 'SELECT IFNULL(SUM(cost), 0) as grand_total FROM payments WHERE reservation_id = '.$reservation_id." AND active = 1 AND status = 1";
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * getReservationUnpaidByReservationId
	 *
	 * returns the sum of the pending payments by reservation id
	 *
	 * @param int $reservation_id
	 * @return int | false on failed
	 */
	public function getReservationUnpaidByReservationId($reservation_id)
	{
		try {
			
			$total = $this->getReservationGrandTotalByReservationId($reservation_id);
			$paid = $this->getReservationPaidByReservationId($reservation_id);
			
			return ($total - $paid);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getReservationStayingCostTotal($reservation_id)
	{
		try {
			$reservation_id = (int) $reservation_id;
			$query = 'SELECT price FROM reservations WHERE reservation_id = '.$reservation_id;
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getReservationStayingCostPaid($reservation_id)
	{
		try {
			$query = 'SELECT IFNULL(SUM(cost), 0) as staying_paid FROM payments WHERE reservation_id = '.$reservation_id." AND active = 1 AND status = 1 AND staying = 1";
			return $this->db->getValue($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function getReservationStayingPending($reservation_id)
	{
		try {
			$total = $this->getReservationStayingCostTotal($reservation_id);
			$paid = $this->getReservationStayingCostPaid($reservation_id);
			
			return $total - $paid;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function setPaymentStatus($paymentId)
	{
		try {
			$query = 'UPDATE payments SET status = 1 WHERE payment_id = '.$paymentId;
			
			return $this->db->run($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function setPaymentType($data)
	{
		try {
			$query = 'UPDATE payments SET payment_type = '.$data['payType'].' WHERE payment_id = '.$data['paymentId'];
			return $this->db->run($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function unActivePayment($paymentId)
	{
		try {
			$query = 'UPDATE payments SET active = 0 WHERE payment_id = '.$paymentId;
			return $this->db->run($query);
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * uptadeSingleReservation
	 * 
	 * change the status of the reservation to pending, confirmed, checked-in, checked-out. BUT NOT TO CANCELED
	 * 
	 * @param array of data
	 * @return true on success, false on failed
	 */
	public function uptadeSingleReservation($data)
	{
		try {
			$query = 'UPDATE reservations 
					SET status = ?, 
					agency = ?
					WHERE reservation_id = '.$data['reservationId'];
				
			$prep = $this->db->prepare($query);
				
			$prep->bind_param('ii',
					$data['optRes'],
					$data['agencyId']);
			return $prep->execute();
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * addCancelation
	 * 
	 * Place a cancelation, creates a copy of the interested row on the cancelation table, 
	 * then the reservation is deleted
	 * 
	 * @param array $data array of data
	 * @return Ambigous <boolean, mixed>|boolean
	 */
	public function addCancelation($data)
	{
		try {
			$reservation_id = (int) $data['reservationId'];
				
			$query = 'INSERT INTO cancelations
					SELECT * FROM reservations
					WHERE reservation_id = '.$reservation_id;
				
			if ($this->db->run($query))
			{
				return $this->deleteReservation($reservation_id);
			}
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * deleteReservation
	 * 
	 * 
	 * 
	 * @param unknown $reservationId
	 * @return Ambigous <boolean, mixed>|boolean
	 */
	public function deleteReservation($reservationId)
	{
		try {
			$reservationId = (int) $reservationId;
			
			$query = 'DELETE 
					FROM reservations 
					WHERE reservation_id = '.$reservationId;
			
			return $this->db->run($query);
			
		} catch (Exception $e) {
			return false;
		}
	}
	
	public function updateReservation($data)
	{
		try {
			$checkIn 	= Tools::formatToMYSQL($data['checkIn']);
			$checkIn	= date($checkIn);
		
			$checkOut 	= Tools::formatToMYSQL($data['checkOut']);
			$checkOut 	= date($checkOut);
		
			$query = 'UPDATE reservations
					SET check_in = ?,
					check_out = ?,
					room_id = ?
					WHERE reservation_id = '.$data['reservationId'];
			
			$prep = $this->db->prepare($query);
			
			$prep->bind_param('ssi',
					$checkIn,
					$checkOut,
					$data['roomId']);
			
			return $prep->execute();
			
		} catch (Exception $e) {
			return false;
		}
	}
	
}















