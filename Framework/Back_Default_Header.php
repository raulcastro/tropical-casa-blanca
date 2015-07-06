<?php 
date_default_timezone_set('America/Bogota');

$root = $_SERVER['DOCUMENT_ROOT'];
require_once ($root . '/Framework/sessionControl.php');
require_once ($root . '/Framework/Connection_Data.php');
require_once ($root . '/Framework/Mysqli_Tool.php');

$typesPages = array(1=>"dashboard/", 2=>"dashboard/");

$control = new sessionControl($db,
		'system_users',
		'user',
		'password',
		'type',
		$typesPages,
		'/sign-out/',
		1);