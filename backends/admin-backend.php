<?php
$root = $_SERVER['DOCUMENT_ROOT'].'/';
require_once $root.'models/back/Layout_Model.php';
require_once $root.'/models/back/Email_Model.php';

class generalBackend
{
	protected  $model;
	
	public function __construct()
	{
		$this->model = new Layout_Model();
		$this->email = new Email_Model();
	}
	
	public function loadBackend($section = '')
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
		$usersActiceArray = $this->model->getActiveUsers();
		$data['usersActive'] = $usersActiceArray;
		
		// User Info
		$userInfoRow = $this->model->getUserInfo();
		$data['userInfo'] = $userInfoRow;
		
		// Last 20 members
		$lastMembersArray = $this->model->getLastMembers();
		$data['lastMembers'] = $lastMembersArray;
		
		$lastBrokersArray = $this->model->getLastBrokers();
		$data['lastBrokers'] = $lastBrokersArray;
		
		// Task Info
		$data['taskInfo']['today'] = $this->model->getTotalTodayTasksByMemberId();
		$data['taskInfo']['pending'] = $this->model->getTotalPendingTasksByMemberId();
		$data['taskInfo']['future'] = $this->model->getTotalFutureTasksByMemberId();
		$data['recentMembers'] = $this->model->getRecentMembers();
		$data['recentBrokers'] = $this->model->getRecentBrokers();
		
		switch ($section) 
		{
			case 'companies':
				// 		get All companies
				$companiesArray = $this->model->getCompanies();
				$data['companies'] = $companiesArray;
			break;
			
			
			case 'add-member':
				// 		get all countries
				$countriesArray = $this->model->getAllCountries();
				$data['countries'] = $countriesArray;
			break;
			
			case 'add-broker':
				// 		get all countries
				$countriesArray = $this->model->getAllCountries();
				$data['countries'] = $countriesArray;
			break;
			
			case 'members':
				// 		get all members
				$membersArray = $this->model->getAllMembers();
				$data['members'] = $membersArray;
			break;
			
			case 'member-info':
				$memberId = (int) $_GET['memberId'];
				
				$memberInfoRow = $this->model->getMemberByMemberId($memberId);
				$data['memberInfo'] = $memberInfoRow;
				
// 				Emails
				$memberEmailsArray  = $this->model->getMemberEmailsById($memberId);
				$data['memberEmails'] = $memberEmailsArray;
				
// 				Phones
				$memberPhonesArray	= $this->model->getMemberPhonesById($memberId);
				$data['memberPhones'] = $this->model->getMemberPhonesById($memberId);
				
// 				History
				$memberHistoryArray = $this->model->getMemberHistoryById($memberId);
				$data['memberHistory'] = $memberHistoryArray;
				
// 				Tasks
				$memberTasksArray	= $this->model->getMemberTaskByMemberId($memberId);
				$data['memberTasks'] = $memberTasksArray; 
				
// 				Reservations
				$memberReservationsArray = $this->model->getMemberReservationsByMemberId($memberId);
				$data['memberReservations'] = $memberReservationsArray;
				
			break;
			
			case 'brokers':
				// 		get all members
				$membersArray = $this->model->getAllBrokers();
				$data['brokers'] = $membersArray;
			break;
			
			case 'broker-info':
				$brokerId = (int) $_GET['brokerId'];
			
				$memberInfoRow = $this->model->getBrokerByBrokerId($brokerId);
				$data['memberInfo'] = $memberInfoRow;
			
				// 				Emails
				$memberEmailsArray  = $this->model->getBrokerEmailsById($brokerId);
				$data['memberEmails'] = $memberEmailsArray;
			
				// 				Phones
				$memberPhonesArray	= $this->model->getBrokerPhonesById($brokerId);
				$data['memberPhones'] = $memberPhonesArray;
			
				// 				History
				$memberHistoryArray = $this->model->getBrokerHistoryById($brokerId);
				$data['memberHistory'] = $memberHistoryArray;
			
				// 				Tasks
				$memberTasksArray	= $this->model->getMemberTaskByMemberId($memberId);
				$data['memberTasks'] = $memberTasksArray;
			
			break;
			
			case 'tasks':
				if ($data['userInfo']['type'] == 1)
					$memberTasksArray	= $this->model->getAllMemberTasks();
				else
					$memberTasksArray	= $this->model->getAllTasksByUser();
				
				$data['memberTasks'] = $memberTasksArray;
			break;
			
			case 'email':
				$inbox = $this->email->getMessagesByAccount($data['userInfo']['user_email']);
				$data['email']['inbox'] = $inbox;
			break;
			
			case 'calendar':
				$calendarArray = $this->model->getAllReservations();
				$data['reservations'] = $calendarArray;
			break;
			
			case 'rooms':
				$roomsArray = $this->model->getAllRooms();
				$data['rooms'] = array();
				foreach ($roomsArray as $room)
				{
					$roomInfo = array(
							'room_id' => $room['room_id'],
							'room' => $room['room'],
							'abbr' => $room['abbr']
					);
					$reservations['reservations'] = $this->model->getReservationsByRoomId($room['room_id']);
					array_push($roomInfo, $reservations);
// 					$data['info']['room_id']['room'] = $room['room'];
					
					array_push($data['rooms'], $roomInfo);
				}
// 				$data['rooms'] = $roomsArray;
			break;
			
			case 'agencies':
				$agenciesArray = $this->model->getAgencies();
				$data['agencies'] = $agenciesArray;		
			break; 
						
			case 'reservations':
				$agenciesArray = $this->model->getAgencies();
				$data['agencies'] = $agenciesArray;
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