<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root.'/models/back/Layout_Model.php');
require_once $root.'/backends/admin-backend.php';
require_once $root.'/Framework/Tools.php';

switch ($_POST['opt'])
{
// 	Get States
	case 1:
		$model	= new Layout_Model();
		if (!empty($_POST))
		{
			$states		= $model->getAllStatesByCountry($_POST['country']);
					
			if ($states != '')
			{
				?>
					<option value="0">Select State</option>
				<?php
				foreach($states as $s)
				{
				?>
					<option value="<?php echo $s['District']; ?>"><?php echo $s['District']; ?></option>
				<?php
				}
			}
			else
			{
				echo 0;
			}
		}
	break;
	
	case 2:
		$model	= new Layout_Model();
		if (!empty($_POST))
		{
			$cities		= $model->getCitiesByEstate($_POST['state']);
	
			if ($cities != '')
			{
				?>
					<option value="0">Select City</option>
				<?php
				foreach($cities as $c)
				{
				?>
					<option value="<?php echo $c['Name']; ?>"><?php echo $c['Name']; ?></option>
				<?php
				}
			}
			else
			{
				echo 0;
			}
		}
	break;
	
	default:
	break;
}