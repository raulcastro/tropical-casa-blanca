<?php
	require_once("Tools.php");

	/**
	 * Access control for restricted sections
	 *
	 * @package    Reservation System
	 * @subpackage Tropical Casa Blanca Hotel
	 * @license    http://opensource.org/licenses/gpl-license.php  GNU Public License
	 * @author     Raul Castro <rd.castro.silva@gmail.com>
	 */

	class sessionControl
	{
		private $table;
		private $user;
		private $password;
		private $db;
		private $error;
		
		/**
		 * Access control for restricted sections
		 * @param object 	$database 
		 * @param string 	$table 			table where it's storaged the system users
		 * @param string 	$userField 		name of the field on the table
		 * @param string 	$passwordField 	name of the field of the password on the table
		 * @param string 	$returnField 	field where we storage the type of the user
		 * @param array 	$loginPage 		like this array(n=>"document n.html", n+1=>"document n+1.html");
		 * @param string 	$noSessionPage 	expired document
		 * @param int 		$currentType  	type of user than can access to the current document
		 */
		public function __construct($database, $table, $userField, $passwordField, $returnField, $loginPage, 
				$noSessionPage, $currentType)
		{
			session_start();
			$this->db 			= $database;
			$this->table 		= $table;
			$this->user 		= $userField;
			$this->password 	= $passwordField;
			$this->returnField 	= $returnField;
			
			if ($_POST["submitButton"]) {
				
				$type = $this->validateLogin();
				if($type != 0)
				{
					$_SESSION["loginUser"] = $_POST["loginUser"];
					$_SESSION["loginType"] = $type;
// 					echo $loginPage[$type];
// 					exit();
					header("Location: $loginPage[$type]");
					exit();
				}
			}
			else
			{
				if (is_array($currentType))
				{
					if (!in_array($_SESSION["loginType"], $currentType))
					{
						header("Location: $noSessionPage");
						exit();
					}
				}
// 				else
// 				{
// 					if($currentType != 0)
// 					{
// 						if($_SESSION["loginType"] != $currentType)
// 						{
// 							header("Location: $noSessionPage");
// 							exit();
// 						}
// 					}
// 				}
			}
		}

		/**
		 * It validates the form actually...
		 * 
		 * @return string, false in case of success or fail
		 */
		private function validateLogin()
		{
			if (!Tools::validateText($_POST["loginUser"], 30, 5) || !Tools::validateText($_POST["loginPassword"], 30, 5))
			{
				$this->error = true;
				return 0;
			}
			else
			{
				$query = "SELECT u.user_id, d.name, u.{$this->returnField} " .
						"FROM ".$this->table." u " .
						"LEFT JOIN user_detail d ON u.user_id = d.user_id " .
						"WHERE ".$this->user." = binary '".$_POST["loginUser"]."' 
						&& ".$this->password." = SHA1('".$_POST["loginPassword"]."')";
				
				$type = $this->db->getRow($query);
				
				if($type)
				{
					$this->error = false;
					$_SESSION["userId"] = $type["user_id"];
					$_SESSION['name'] 	= $type['name'];
					return $type[$this->returnField];
				}
				else
				{
					$this->error = true;
					return false;
				}
			}
		}
		
		/**
		 * It destroy the session, it helps like in the log-out sections
		 */
		public function eraseSession()
		{
			$_SESSION = array();
			
			if (isset($_COOKIE[session_name()]))
			{
    			setcookie(session_name(), '', time()-42000, '/');
			}

			session_destroy();
		}
	}
?>
