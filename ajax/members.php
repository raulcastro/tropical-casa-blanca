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
		if ($memberId > 0)
		{
			
		} 
		else 
		{
			if ($newMember = $model->addMember($_POST))
				echo str_pad($newMember, 4, 0, STR_PAD_LEFT);
			else 
				echo 0;
		}	
	break;
	
// 	E-mails
	case 2:
		if ($_POST['memberId'])
		{
			$model->addMemberEmail($_POST);
		}
	break;
	
	// 	Phones
	case 3:
		if ($_POST['memberId'])
		{
			$model->addMemberPhone($_POST);
		}
	break;
	
	// Add History
	case 4:
		if ($_POST['memberId'])
		{
			if ($model->addHistory($_POST))
				echo 1;
			else 
				echo 0;
		}
	break;
	
	case 5:
		if ($_POST['memberId'])
		{
			if ($historyArray = $model->getHistoryEntries($_POST['memberId']))
			{
				foreach ($historyArray as $history)
				{
					?>
					<li>
						<div class="header"><?php echo $history['name']; ?> | <?php echo Tools::formatMYSQLToFront($history['date']).'  '.Tools::formatHourMYSQLToFront($history['time']); ?></div>
						<div>
							<i class="glyphicon glyphicon-option-vertical"></i>
							<div class="history-title">
								<span class="task-title-sp">
									<?php echo $history['history']; ?>
								</span>
							</div>
						</div>
					</li>
					<?php
				}
			}
		}
	break;
	
	case 6:
		if ($_POST['memberId'])
		{
			if ($model->addMemberTask($_POST))
				echo 1;
			else 
				echo 0;
		}
	break;
	
	case 7:
		if ($_POST['memberId'])
		{
			if ($memberTasksArray	= $model->getMemberTaskByMemberId($_POST['memberId']))
			{
				echo Layout_View::listTasks($memberTasksArray);
			}
		}
	break;
	
	default:
	break;
}