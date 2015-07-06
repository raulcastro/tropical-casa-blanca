<?php
// 	error_reporting(E_ALL);
// 	ini_set("display_errors", 1);

	$root = $_SERVER['DOCUMENT_ROOT']."/";
	
	require_once $root.'backends/admin-backend.php';
	require_once $root.'/'.'views/Layout_View.php';

	$section 	= '';
	$title 		= '';
	$data 		= '';
	
	if (!$_GET['brokerId'])
	{
		$section = 'add-broker';
		$title 	 = 'Add Broker';
	}
	else
	{
		$section 	= 'broker-info';
	}
	
	$data 	= $backend->loadBackend($section);
	$title 	.= $data['memberInfo']['name'].' '.$data['memberInfo']['last_name'];
	
	$view 	= new Layout_View($data, $title);
	
	echo $view->printHTMLPage('add-broker');