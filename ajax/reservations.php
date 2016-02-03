<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root.'/models/back/Layout_Model.php');
require_once($root.'/views/Layout_View.php');
require_once $root.'/backends/admin-backend.php';
require_once $root.'/Framework/Tools.php';
$model	= new Layout_Model();

$memberId = (int) $_POST['memberId'];

switch ($_POST['opt'])
{
	
	case 1:	 
		if ($rooms = $model->searchRooms($_POST))
		{
			echo Layout_View::getRoomsList($rooms);
		}
	break;
	
	case 2:
		if ($memberId = $model->addMemberFromReservation($_POST) )
		{
			echo $memberId;
		}
		else
		{
			?>
			<div class="alert alert-dismissible alert-info">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Great! </strong>  There are no tasks for today!
			</div>
			<?php
		}
	break;
	
	case 3: // Add the reservation at the same time that the member is added
		$model->addReservation($_POST);
	break;
	
	case 4: // Add the reservation when the member is already created
		if ($model->addReservation($_POST))
		{
			$memberReservationsArray 	= $model->getMemberReservationsByMemberId($_POST['memberId']);
			
			$data['memberReservations'] = array();
			if ($memberReservationsArray)
			{
				foreach ($memberReservationsArray as $reservation)
				{
					$grandTotal = $model->getReservationGrandTotalByReservationId($reservation['reservation_id']);
					$paid 		= $model->getReservationPaidByReservationId($reservation['reservation_id']);
					$unpaid 	= $model->getReservationUnpaidByReservationId($reservation['reservation_id']);
						
					$reservationInfo = array(
							'reservation_id'	=> $reservation['reservation_id'],
							'room_id' 			=> $reservation['room_id'],
							'date'				=> $reservation['date'],
							'check_in' 			=> $reservation['check_in'],
							'check_out' 		=> $reservation['check_out'],
							'check_mask' 		=> $reservation['check_mask'],
							'room' 				=> $reservation['room'],
							'room_type' 		=> $reservation['room_type'],
							'adults' 			=> $reservation['adults'],
							'children' 			=> $reservation['children'],
							'agency' 			=> $reservation['agency'],
							'external_id' 		=> $reservation['external_id'],
							'status' 			=> $reservation['status'],
							'grandTotal' 		=> $grandTotal,
							'paid' 				=> $paid,
							'unpaid' 			=> $unpaid
					);
						
					$payments['payments'] = $model->getPaymentsByReservationId($reservation['reservation_id']);
					array_push($reservationInfo, $payments);
					array_push($data['memberReservations'], $reservationInfo);
				}
				
				foreach ($data['memberReservations'] as $reservation)
				{
					echo Layout_View::getMemberReservationItem($reservation);
				}
			}
		}
	break;
	
	case 5:
		if ($_POST['optRes'] != 5)
		{
			if ($model->uptadeSingleReservation($_POST))
			{
				echo '1';
			}
			else
			{
				echo '0';
			}	
		} 
		else 
		{
			if ($model->addCancelation($_POST))
			{
				echo '1';
			}
			else
			{
				echo '0';
			}
		}
	break;
	
	case 6:
		if ($model->addPayment($_POST))
		{
			echo '1';
		}
		else 
		{
			echo '0';
		}	
		
	break;
	
	// Reservation grand total
	case 7:
		if ($grandTotal = $model->getReservationGrandTotalByReservationId($_POST['reservationId']))
			echo $grandTotal;
	break;
	
	
	case 8:
		if ($grandTotal = $model->getReservationPaidByReservationId($_POST['reservationId']))
			echo '$ '.$grandTotal;
	break;
			
	case 9:
		$grandTotal = $model->getReservationUnpaidByReservationId($_POST['reservationId']);
		echo '$ '.$grandTotal;
	break;
	
	case 10:
		if ($payments = $model->getPaymentsByReservationId($_POST['reservationId']))
		{
			echo Layout_View::getPaymentItems($payments);
		}
		else
		{
			echo '0';
		}
	break;
	
	case 11:
		if ($model->setPaymentStatus($_POST['paymentId']))
			echo '1';
		else 
			echo '0';
	break;
	
	case 12:
		if ($model->setPaymentType($_POST))
			echo '1';
		else
			echo '0';
	break;
	
	case 13:
		if ($model->unActivePayment($_POST['paymentId']))
			echo '1';
		else
			echo '0';
	break;
	
	case 14: // Get the available rooms according to a date-range and returns an html <options> elements
		$roomId 			= $_POST['roomId'];
		$currentCheckIn 	= Tools::formatToMYSQL($_POST['currentCheckIn']);
		$currentCheckOut 	= Tools::formatToMYSQL($_POST['currentCheckOut']);
		$checkIn 			= Tools::formatToMYSQL($_POST['checkIn']);
		$checkOut 			= Tools::formatToMYSQL($_POST['checkOut']);
		
		$current 	= array($currentCheckIn, $currentCheckOut);
		$needed 	= array($checkIn, $checkOut);
		
		$currentMin = new DateTime(min($current));
		$currentMax = new DateTime(max($current));
		
		$neededMin = new DateTime(min($needed));
		$neededMax = new DateTime(max($needed));
		
// 		into the current date, they want a minumun range than current
// 		---------
// 		 -------
		if ($neededMin >= $currentMin && $neededMax <= $currentMax)
		{
			$currentRoom = $model->getSingleRoomById($roomId);
			?>
			<option selected><?php echo $currentRoom['room']; ?></option>
			<?php 
		}
		
// 		if the needed check-out is bigger than the current check out but the needed check in is in the current range
// 		--------
// 			-------

		if ($neededMax > $currentMax)
		{
			if ($neededMin >= $currentMin && $neededMin <= $currentMax)
			{
				$info = array('roomId' => $roomId, 'checkIn'=>$_POST['currentCheckOut'], 'checkOut'=>$checkOut);
				var_dump($info);
				$currentRoom;
			}
		}
		
		if ($rooms = $model->searchRooms($_POST))
		{
			foreach ($rooms as $room)
   			{
	   			?>
	   			<option><?php echo $room['room']; ?></option>
	   			<?php
	   		}
		}
		

	break;
	// 		var_dump($_POST);
	
	// 		if ($rooms = $model->searchRooms($_POST))
		// 		{
		// 			foreach ($rooms as $room)
			//    			{
	//    			?>
	   				<option><?php echo $room['room']; ?></option>
	   			<?php
	//    			}
	// 		}
	default:
	break;
}