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
// 			echo "<pre>";
// 			var_dump($rooms);
// 			echo "</pre>";
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
			$reservations = $model->getMemberReservationsByMemberId($_POST['memberId']);
			if ($reservations)
				foreach ($reservations as $reservation)
				{
					echo Layout_View::getMemberReservationItem($reservation);
				}
		}
	break;
	
	case 5:
		if ($model->uptadeSingleReservation($_POST)) 
		{
			echo '1';
		}
		else 
		{
			echo '0';
		}
	break;
	
	default:
	break;
}