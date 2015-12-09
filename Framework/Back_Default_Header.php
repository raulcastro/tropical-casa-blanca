<?php 
/**
 * This file the one that controls the sessions on the application
 *
 * @package    Reservation System
 * @subpackage Tropical Casa Blanca Hotel
 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
 * @author     Raul Castro <rd.castro.silva@gmail.com>
 */

date_default_timezone_set('America/Bogota');

$root = $_SERVER['DOCUMENT_ROOT'];
require_once ($root . '/Framework/sessionControl.php');
require_once ($root . '/Framework/Connection_Data.php');
require_once ($root . '/Framework/Mysqli_Tool.php');

$typesPages = array(1=>"dashboard/", 2=>"dashboard/");

/**
 * Creates a session, and redirect the site according to the array listed above
 */
$control = new sessionControl($db,
		'system_users',
		'user',
		'password',
		'type',
		$typesPages,
		'/sign-out/',
		1);