<?php
$root = $_SERVER['DOCUMENT_ROOT'].'/';

/**
 * Includes the file /models/front/Layout_Model.php
 * in order to interact with the database
 */
require_once $root.'models/back/Layout_Model.php';

/**
 * Contains the classes for access to the basic app after log-in
 *
 * @package    Reservation System
 * @subpackage Tropical Casa Blanca Hotel
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Raul Castro <rd.castro.silva@gmail.com>
 */
class generalBackend
{
	protected  $model;
	
	/**
	 * Initialize a class, the model one
	 */
	
	public function __construct()
	{
		$this->model = new Layout_Model();
	}
	
	/**
	 * Based on the section it returns the right info that could be propagated along the application
	 *
	 * @param string $section
	 * @return array Array with the asked info of the application
	 */
	public function loadBackend($section = '', $memberId = '')
	{
		$data 		= array();
		
// 		Info of the Application
		
		$appInfoRow = $this->model->getGeneralAppInfo();
		
		$appInfo = array( 
				'title' 		=> $appInfoRow['title'],
				'siteName' 		=> $appInfoRow['site_name'],
				'url' 			=> $appInfoRow['url'],
				'content' 		=> $appInfoRow['content'],
				'description'	=> $appInfoRow['description'],
				'keywords' 		=> $appInfoRow['keywords'],
				'location'		=> $appInfoRow['location'],	
				'creator' 		=> $appInfoRow['creator'],
				'creatorUrl' 	=> $appInfoRow['creator_url'],
				'twitter' 		=> $appInfoRow['twitter'],
				'facebook' 		=> $appInfoRow['facebook'],
				'googleplus' 	=> $appInfoRow['googleplus'],
				'pinterest' 	=> $appInfoRow['pinterest'],
				'linkedin' 		=> $appInfoRow['linkedin'],
				'youtube' 		=> $appInfoRow['youtube'],
				'instagram'		=> $appInfoRow['instagram'],
				'email'			=> $appInfoRow['email'],
				'lang'			=> $appInfoRow['lang']
				 
		);
		
		$data['appInfo'] = $appInfo;

		// Active Users
		$usersActiveArray 			= $this->model->getActiveUsers();
		$data['usersActive'] 		= $usersActiveArray;
		
		// User Info
		$userInfoRow 				= $this->model->getUserInfo();
		$data['userInfo'] 			= $userInfoRow;
		
		// Last 20 members
		$lastMembersArray 			= $this->model->getLastMembers();
		$data['lastMembers'] 		= $lastMembersArray;
		
		// Task Info
		$data['taskInfo']['today'] 		= $this->model->getTotalTodayTasksByMemberId();
		$data['taskInfo']['pending'] 	= $this->model->getTotalPendingTasksByMemberId();
		$data['taskInfo']['future'] 	= $this->model->getTotalFutureTasksByMemberId();
		$data['recentMembers'] 			= $this->model->getRecentMembers();
		
		switch ($section) 
		{
			case 'companies':
				// 		get All companies
				$companiesArray 	= $this->model->getCompanies();
				$data['companies'] 	= $companiesArray;
			break;
			
			case 'members':
				// 		get all members
				$membersArray 		= $this->model->getAllMembers();
				$data['members'] 	= $membersArray;
			break;
			
			case 'member-info':
				// 		get all countries
				$countriesArray 		= $this->model->getAllCountries();
				$data['countries'] 		= $countriesArray;
				
				$memberInfoRow 			= $this->model->getMemberByMemberId($memberId);
				$data['memberInfo'] 	= $memberInfoRow;
				
// 				Emails
				$memberEmailsArray  	= $this->model->getMemberEmailsById($memberId);
				$data['memberEmails'] 	= $memberEmailsArray;
				
// 				Phones
				$memberPhonesArray		= $this->model->getMemberPhonesById($memberId);
				$data['memberPhones'] 	= $this->model->getMemberPhonesById($memberId);
				
// 				History
				$memberHistoryArray 	= $this->model->getMemberHistoryById($memberId);
				$data['memberHistory'] 	= $memberHistoryArray;
				
// 				Tasks
				$memberTasksArray		= $this->model->getMemberTaskByMemberId($memberId);
				$data['memberTasks'] 	= $memberTasksArray; 
				
// 				Reservations
				$memberReservationsArray 	= $this->model->getMemberReservationsByMemberId($memberId);
				
				$data['memberReservations'] = array();
				
				foreach ($memberReservationsArray as $reservation)
				{
					$grandTotal = $this->model->getReservationGrandTotalByReservationId($reservation['reservation_id']);
					$paid 		= $this->model->getReservationPaidByReservationId($reservation['reservation_id']);
					$unpaid 	= $this->model->getReservationUnpaidByReservationId($reservation['reservation_id']);
					
					$grandTotalStaying 	= $this->model->getReservationStayingCostTotal($reservation['reservation_id']);
					$paidStaying		= $this->model->getReservationStayingCostPaid($reservation['reservation_id']);
					$pendingStaying		= $this->model->getReservationStayingPending($reservation['reservation_id']);
					
					// List of available rooms of for the current range of date
					$reservationDate 	= array('checkIn' => $reservation['check_in'], 'checkOut' => $reservation['check_out']);
					$availableRooms 	= $this->model->searchRooms($reservationDate);
					
					$reservationInfo = array(
							'reservation_id'	=> $reservation['reservation_id'],
							'room_id' 			=> $reservation['room_id'],
							'date'				=> $reservation['date'],
							'check_in' 			=> $reservation['check_in'],
							'check_in_mask'		=> $reservation['check_in_mask'],
							'check_out' 		=> $reservation['check_out'],
							'check_out_mask'	=> $reservation['check_out_mask'],
							'room' 				=> $reservation['room'],
							'room_type' 		=> $reservation['room_type'],
							'adults' 			=> $reservation['adults'],
							'children' 			=> $reservation['children'],
							'agency' 			=> $reservation['agency'],
							'agency_id'			=> $reservation['agency_id'],
							'external_id' 		=> $reservation['external_id'],
							'status' 			=> $reservation['status'],
							'n_days'			=> $reservation['n_days'],
							'grandTotal' 		=> $grandTotal,
							'paid' 				=> $paid,
							'unpaid' 			=> $unpaid,
							'staying_total'		=> $grandTotalStaying,
							'staying_paid'		=> $paidStaying,
							'staying_pending'	=> $pendingStaying,
							'availableRooms'	=> $availableRooms
					);
					
					$payments['payments'] = $this->model->getPaymentsByReservationId($reservation['reservation_id']);
					array_push($reservationInfo, $payments);
					array_push($data['memberReservations'], $reservationInfo);
				}
				
				// 				Cancelations
				$memberReservationsArray 	= $this->model->getMemberCancelationsByMemberId($memberId);
				
				foreach ($memberReservationsArray as $reservation)
				{
					$grandTotal = $this->model->getReservationGrandTotalByReservationId($reservation['reservation_id']);
					$paid 		= $this->model->getReservationPaidByReservationId($reservation['reservation_id']);
					$unpaid 	= $this->model->getReservationUnpaidByReservationId($reservation['reservation_id']);
						
					$cancelationInfo = array(
							'reservation_id'	=> $reservation['reservation_id'],
							'room_id' 		=> $reservation['room_id'],
							'date'			=> $reservation['date'],
							'check_in' 		=> $reservation['check_in'],
							'check_in_mask'	=> $reservation['check_in_mask'],
							'check_out' 	=> $reservation['check_out'],
							'check_out_mask'=> $reservation['check_out_mask'],
							'room' 			=> $reservation['room'],
							'room_type' 	=> $reservation['room_type'],
							'adults' 		=> $reservation['adults'],
							'children' 		=> $reservation['children'],
							'agency' 		=> $reservation['agency'],
							'external_id' 	=> $reservation['external_id'],
							'status' 		=> 5,
							'grandTotal' 	=> $grandTotal,
							'paid' 			=> $paid,
							'unpaid' 		=> $unpaid
					);
					
					$payments['payments'] = $this->model->getPaymentsByReservationId($reservation['reservation_id']);
					array_push($cancelationInfo, $payments);
					
					array_push($data['memberReservations'], $cancelationInfo);
				}
				
// 				/Cancelations
				
// 				Agencies
				$agenciesArray 		= $this->model->getAgencies();
				$data['agencies'] 	= $agenciesArray;
				
// 				Rooms
				$roomsArray 	= $this->model->getAllRooms();
				$data['rooms'] 	= array();
				foreach ($roomsArray as $room)
				{
					$roomInfo = array(
							'room_id'	=> $room['room_id'],
							'room' 		=> $room['room'],
							'abbr' 		=> $room['abbr']
					);
				
					array_push($data['rooms'], $roomInfo);
				}
				
			break;
			
// 			Reservations
			case 'reservations':
				$agenciesArray 		= $this->model->getAgencies();
				$data['agencies'] 	= $agenciesArray;
			break;
			
// 			Rooms
			case 'rooms':
				$roomsArray 	= $this->model->getAllRooms();
				$data['rooms'] 	= array();
				foreach ($roomsArray as $room)
				{
					$roomInfo = array(
							'room_id'	=> $room['room_id'],
							'room' 		=> $room['room'],
							'abbr' 		=> $room['abbr']
					);
						
					$reservations['reservations'] = $this->model->getReservationsByRoomId($room['room_id']);
					array_push($roomInfo, $reservations);
					array_push($data['rooms'], $roomInfo);
				}
			break;
			
// 			Calendar
			case 'calendar':
				$calendarArray 			= $this->model->getAllReservations();
				$data['reservations'] 	= $calendarArray;
			break;
					
// 			Agencias
			case 'agencies':
				$agenciesArray 		= $this->model->getAgencies();
				$data['agencies'] 	= $agenciesArray;
			break;
			
// 			Tasks
			case 'tasks':
				if ($data['userInfo']['type'] == 1)
					$memberTasksArray	= $this->model->getAllMemberTasks();
				else
					$memberTasksArray	= $this->model->getAllTasksByUser();
				
				$data['memberTasks'] 	= $memberTasksArray;
			break;
			
			case 'reports':
				if (!$_GET['from'])
				{
					$from = date('Y-m-d', strtotime(' -1 day'));
					$start = date('Y-m-d', strtotime(' -1 day', strtotime($from)));
					$end = date('Y-m-d', strtotime(' +31 day', strtotime($from)));
				}
				else
				{
					$from = date('Y-m-d', strtotime($_GET['from']));
					$start = date('Y-m-d', strtotime(' -1 day', strtotime($_GET['from'])));
					$end = date('Y-m-d', strtotime(' +32 day', strtotime($_GET['from'])));
				}
				
				$reservationsArray = $this->model->getReservationsByRange($start, $end);
				$data['reservations'] = $reservationsArray; 
			break;
			
			default:
			break;
		}
		
		return $data;
	}
}

$backend = new generalBackend();

// $info = $backend->loadBackend();
// var_dump($info['categoryInfo']);