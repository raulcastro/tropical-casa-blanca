<?php
/**
 * The controler for the member profile
 *
 * @package    Reservation System
 * @subpackage Tropical Casa Blanca Hotel
 * @category 	interfaz
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Raul Castro <rd.castro.silva@gmail.com>
 */
// 	error_reporting(E_ALL);
// 	ini_set("display_errors", 1);
// var_dump($_GET);

	$root = $_SERVER['DOCUMENT_ROOT']."/";
	
	require_once $root.'backends/admin-backend.php';
	require_once $root.'/'.'views/Layout_View.php';
	
	$section = 'members';
	
	$data 	= $backend->loadBackend($section);
	
	$view 	= new Layout_View($data, 'Guests');
	
	echo $view->printHTMLPage('members');