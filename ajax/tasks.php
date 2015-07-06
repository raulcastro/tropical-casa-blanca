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
		if ($tasks = $model->getPendingTasksByUser())
		{
			echo Layout_View::listTasks($tasks);
		}
		else
		{
			?>
			<div class="alert alert-dismissible alert-info">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Great! </strong>  There are no pending tasks!
			</div>
			<?php
		}
	break;
	
	case 2:
		if ($tasks = $model->getTodayTasksByUser())
		{
			echo Layout_View::listTasks($tasks);
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
	
	case 3:
		if ($tasks = $model->getFutureTasksByUser())
		{
			echo Layout_View::listTasks($tasks);
		}
		else
		{
			?>
			<div class="alert alert-dismissible alert-info">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Great! </strong>  The tasks are up to date!
			</div>
			<?php
		}
	break;
	
	case 4:
		if ($tasks = $model->getCompletedTasksByUser())
		{
			echo Layout_View::listTasks($tasks);
		}
		else
		{
			?>
			<div class="alert alert-dismissible alert-warning">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong>Oops! </strong>  Not a single task completed!
			</div>
			<?php
		}
	break;
	
	case 5:
		if ($model->completeTask($_POST['tid'])) 
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