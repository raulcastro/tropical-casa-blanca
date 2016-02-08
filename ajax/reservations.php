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
	// Returns a list of available rooms depending on check-in & check-out
	case 1:	 
		if ($rooms = $model->searchRooms($_POST))
		{
			echo Layout_View::getRoomsList($rooms);
		}
	break;
	
	// Add a new member when a reservation take place
	case 2:
		if ($memberId = $model->addMemberFromReservation($_POST))
		{
			echo $memberId;
		}
	break;
	
	// Add the reservation at the same time that the member is added
	case 3: 
		$model->addReservation($_POST);
	break;
	
	// Add the reservation when the member is already created
	case 4: 
		if ($model->addReservation($_POST))
		{
			echo 1;
		}
	break;
	
	// Update the status of the reservation, or cancel the reservation (which is a kind of status too)
	case 5:
		if ($_POST['optRes'] != 5)
		{
			if ($model->uptadeSingleReservation($_POST))
				echo '1';
			else
				echo '0';
		} 
		else 
		{
			if ($model->addCancelation($_POST))
				echo '1';
			else
				echo '0';
		}
	break;
	
// 	Add payment
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
		{
			echo '$ '.$grandTotal;
		}
		else 
		{
			echo '$ 0';
		}
	break;
			
	case 9:
		if ($grandTotal = $model->getReservationUnpaidByReservationId($_POST['reservationId']))
		{
			echo '$ '.$grandTotal;
		}
		else 
		{
			echo '$ 0';
		}
		
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
			<option selected value="<?php echo $currentRoom['room_id']; ?>"><?php echo $currentRoom['room']; ?></option>
			<?php 
		}
		
// 		if the needed check-out is bigger than the current check out but the needed check in is in the current range
// 		--------
// 			-------

		if ($neededMax > $currentMax)
		{
			if ($neededMin >= $currentMin && $neededMin < $currentMax)
			{
				$info = array('roomId' => $roomId, 'checkIn'=>$currentCheckOut, 'checkOut'=>$checkOut);
				if ($currentRoom = $model->searchSingleRoom($info))
				{
				?>
				<option selected value="<?php echo $currentRoom['room_id']; ?>"><?php echo $currentRoom['room']; ?></option>
				<?php 					
				}
			}
		}
		
		if ($neededmin < $currentMin && $neededMax <= $currentMin)
		{
			if ($neededMin <= $currentMin && $neededMax < $currentMax)
			{
				$info = array('roomId' => $roomId, 'checkIn'=>$checkIn, 'checkOut'=>$currentCheckIn);
				if ($currentRoom = $model->searchSingleRoom($info))
				{
				?>
				<option selected value="<?php echo $currentRoom['room_id']; ?>"><?php echo $currentRoom['room']; ?></option>
				<?php 					
				}
			}
		}
		
		if ($neededmin < $currentMin && $neededMax <= $currentMax)
		{
			if ($neededMin <= $currentMin && $neededMax <= $currentMax)
			{
				$info = array('roomId' => $roomId, 'checkIn'=>$checkIn, 'checkOut'=>$currentCheckIn);
				if ($currentRoom = $model->searchSingleRoom($info))
				{
				?>
				<option selected value="<?php echo $currentRoom['room_id']; ?>"><?php echo $currentRoom['room']; ?></option>
				<?php 					
				}
			}
		}
		
		
		
		if ($neededmin < $currentMin && $neededMax > $currentMin)
		{
			$info = array('roomId' => $roomId, 'checkIn'=>$currentCheckOut, 'checkOut'=>$checkOut);
			$left = $model->searchSingleRoom($info);
			
			$info = array('roomId' => $roomId, 'checkIn'=>$checkIn, 'checkOut'=>$currentCheckIn);
			$rigth = $model->searchSingleRoom($info);
			
			if ($left && $rigth)
			{
				echo 'here';
				if ($currentRoom = $model->getSingleRoomById($roomId))
				{
				?>
				<option selected value="<?php echo $currentRoom['room_id']; ?>"><?php echo $currentRoom['room']; ?></option>
				<?php 					
				}
			}
		}
		
		if ($rooms = $model->searchRooms($_POST))
		{
			foreach ($rooms as $room)
   			{
	   			?>
	   			<option value="<?php echo $room['room_id']; ?>"><?php echo $room['room']; ?></option>
	   			<?php
	   		}
		}
	break;
	
	case 15:
		if ($model->updateReservation($_POST))
			echo '1';
		else
			echo '0';
	break;
	
	case 16:
		if ($paidStaying		= $model->getReservationStayingCostPaid($_POST['reservationId']))
		{	
			echo '$ '.$paidStaying;
		}
		else 
		{
			echo '$ 0';
		}
	break;
	
	case 17:
		if ($pendingStaying		= $model->getReservationStayingPending($_POST['reservationId']))
		{
			echo '$ '.$pendingStaying;
		}
		else
		{
			echo '$ 0';
		}
	break;
	
	case 18:
		if ($grandTotalStaying		= $model->getReservationStayingCostTotal($_POST['reservationId']))
		{
			echo '$ '.$grandTotalStaying;
		}
		else
		{
			echo '$ 0';
		}
	break;

	default:
	break;
}