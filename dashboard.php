<?php
//	error_reporting(E_ALL);
//	ini_set("display_errors", 1);
// var_dump($_GET);

	$root = $_SERVER['DOCUMENT_ROOT']."/";
	
	require_once $root.'backends/admin-backend.php';
	require_once $root.'/'.'views/Layout_View.php';
	
	if ($_GET['promoted'])
	{
		$option = "promoted";
	} 
	elseif ($_GET['categoryId'])
	{
		$option = 'byCategory';
	} else {
		$option = 'companies';
	}
	
	$data 	= $backend->loadBackend();
	
	$view 	= new Layout_View($data, 'Dashboard');
	
	echo $view->printHTMLPage('dashboard');
	
	