<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root.'/Framework/Tools.php';

class Layout_View
{
	private $data;
	private $title;
	private $section;
	
	public function __construct($data, $title)
	{
		$this->data = $data;
		$this->title = $title;
	}    
	
	/**
	 * function printHTMLPage
	 * 
	 * Prints the content of the whole website
	 * 
	 * @param head 		(string) Is the head of the HTML structure
	 * @param header 	(string) Is the menu and logo section
	 * @param bodyType	(string) Is for CSS purposes
	 * @param body		(string) Content of the website
	 * 
	 */
	
	public function printHTMLPage($section)
    {
    	$this->section = $section;
    ?>
	<!DOCTYPE html>
	<html class='no-js' lang='<?php echo $this->data['appInfo']['lang']; ?>'>
		<head>
			<!--[if IE]> <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> <![endif]-->
			<meta charset="utf-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
    		<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="shortcut icon" href="favicon.ico" />
			<link rel="icon" type="image/gif" href="favicon.ico" />
			<title><?php echo $this->title; ?> - <?php echo $this->data['appInfo']['title']; ?></title>
			<meta name="keywords" content="<?php echo $this->data['appInfo']['keywords']; ?>" />
			<meta name="description" content="<?php echo $this->data['appInfo']['description']; ?>" />
			<meta property="og:type" content="website" /> 
			<meta property="og:url" content="<?php echo $this->data['appInfo']['url']; ?>" />
			<meta property="og:site_name" content="<?php echo $this->data['appInfo']['siteName']; ?> />
			<link rel='canonical' href="<?php echo $this->data['appInfo']['url']; ?>" />
			<?php echo self::getCommonDocuments(); ?>			
			<?php 
			switch ($section) {
				case 'sign-in':
 					echo self :: getSignInHead();
				break;
			
				case 'add-member':
					echo self :: getMembersHead();
				break;
				
				case 'add-broker':
					echo self :: getBrokersHead();
				break;
				
				case 'tasks':
					echo self :: getTasksHead();
				break;
				
				case 'email':
					echo self :: getEmailsHead();
				break;
				
				case 'edit-company-events':
					echo self :: getEventsListHead();
				break;
				
				case 'calendar':
					echo self :: getCalendarHead();
				break;
				
				case 'reservations':
					echo self :: getReservationsHead();
				break;
				
				case 'rooms':
					echo self :: getRoomsHead();
				break;
				
				case 'agencies':
					echo self::getAgenciesHead();
				break;
			}
			?>
		</head>
		<body id="<?php echo $section; ?>">
			<?php 
 			echo self :: getHeader();
 
			if ($section != 'sign-in' && $section != 'sign-out')
			{
			?>
			<div class="container-fluid">
				<div class="row">
					<?php echo self::getSidebar(); ?>
					<div class="col-sm-10 col-sm-offset-2 main">
						<h1 class="page-header"><?php echo $this->title; ?></h1>
						<?php 
						echo self :: getDashboardIcons();
						switch ($section) {
							case 'dashboard':
								echo self :: getRecentMembers();
// 								echo self :: getRecentBrokers();
							break;
							
							case 'add-member':
								echo self :: getAddMember();
							break;
							
							case 'members':
								echo self :: getAllMembers();
							break;
							
							case 'tasks':
								echo self :: getAllTasks();
							break;
							
							case 'reservations':
								echo self :: getReservations();
							break;
							
							case 'email':
								echo self :: getEmail();
							break;
							
							case 'brokers':
								echo self :: getAllBrokers();
							break;
							
							case 'add-broker':
								echo self :: getAddBroker();
							break;
							
							case 'calendar':
								echo self :: getCalendar();
							break;
							
							case 'rooms':
								echo self :: getRooms();
							break;
							
							case 'agencies':
								echo self :: getAgencies();
							break;
							
							default:
							break;
						}
						?>
					</div>
				</div>
			</div>
			<?php
			}else {
				switch ($section) {
					case 'sign-in':
						echo self :: getSignInContent();
					break;
				
					case 'sign-out':
						echo self :: getSignOutContent();
					break;
					
					default:
						break;
				}
			}
			 
 			//echo self :: getFooter(); 
			?>
			<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		   
		    <!-- Include all compiled plugins (below), or include individual files as needed -->
		    
		</body>
	</html>
    <?php
    }
    
    /**
     * getCommonDocuments
     * 
     * returns the common css and js that are in all the web documents
     * 
     * @return string
     */
    public function getCommonDocuments()
    {
    	ob_start();
    	?>
    	<script src="/js/jquery-2.1.3.min.js"></script>
    	<!-- Bootstrap -->
	    <link href="/css/bootstrap.min.css" rel="stylesheet">
	    <script src="/js/bootstrap.min.js"></script>
	
	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
       	<link href="/css/style.css" media="screen" rel="stylesheet" type="text/css" />
       	
    	<script src="/js/scripts.js"></script>
       	<?php 
       	$documents = ob_get_contents();
       	ob_end_clean();
       	return $documents; 
    }
    
    /**
     * getHeader
     *
     * it's the top and main navigation menu
     *
     * @return string
     */
    public function getHeader()
    {
    	ob_start();
    	$active='class="active"';
    	?>  		
    	<header class="navigation navbar navbar-fixed-top main-menu-holder">
			<?php 
			if ($this->section == 'sign-in')
			{
				?>
			<nav class="nav navbar-nav navbar-fixed-top">
				<ul class="nav navbar-nav main-menu">
					<li class="active"><a href="/">Sign In</a></li>
					<li><a href="/">Sign Up</a></li>
				</ul>
			</nav>
				<?php 
			}
			else
			{
				?>
			<nav id='nav navbar-nav navbar-fixed-top'>
				<ul class="nav navbar-nav main-menu">
					<li><a <?php if ($_GET['section'] == 1) echo $active; ?> href="/dashboard/"><b><?php echo $this->data['userInfo']['name']; ?></b></a></li>
					<li><a <?php if ($_GET['section'] == 5) echo $active; ?> href="#">Settings</a></li>					
					<li><a <?php if ($_GET['section'] == 10) echo $active; ?> href="/sign-out/" class="sign-out">Sign Out</a></li>
				</ul>
			</nav>
				<?php 
			}
			?>
		</header>
    	<?php
    	$header = ob_get_contents();
    	ob_end_clean();
    	return $header;
    }
    
    /**
     * getSignInHead 
     * 
     * it is the head that works for the sign in section, aparently isn't getting 
     * any parameter, I just left it here for future cases
     * 
     * @return string
     */
    public function getSignInHead()
    {
    	ob_start();
    	?>
    	<script type="text/javascript">
		</script>
    	<?php
    	$signIn = ob_get_contents();
    	ob_end_clean();
    	return $signIn;
    }
    
    public function getTasksHead()
    {
    	ob_start();
    	?>
       	<script src="/js/tasks.js"></script>
        <script>
    	</script>
        <?php
        $signIn = ob_get_contents();
        ob_end_clean();
        return $signIn;
    }
    
    public function getEmailsHead()
    {
    	ob_start();
    	?>
           	<script src="/js/emails.js"></script>
            
        <?php
        $signIn = ob_get_contents();
        ob_end_clean();
        return $signIn;
    }
    
    public function getBrokersHead()
    {
    	ob_start();
    	?>
		<link rel="stylesheet" href="/css/jquery-ui.css">
		<script src="/js/jquery-ui.js"></script>
		<script src="/js/brokers.js"></script>
		<script src="/js/broker-history.js"></script>
		<script src="/js/tasks.js"></script>
		<script>
		$(function() {
			$( "#task-date" ).datepicker();
		});
		</script>
		<?php
		$signIn = ob_get_contents();
		ob_end_clean();
		return $signIn;
	}
    
    /**
     * getSignInContent
     * 
     * the sign-in box
     * 
     * @return string
     */
    public function getSignInContent()
    {
    	ob_start();
    	?>
    	<div class="login-box" id="sign-in">
			<div class="col-md-4 col-md-offset-4">
	    		<div class="panel panel-default">
				  	<div class="panel-heading">
				    	<h3 class="panel-title">Please sign in</h3>
				 	</div>
				  	<div class="panel-body">
				    	<form accept-charset="UTF-8" role="form" method='post' 
								action="<?php echo $_SERVER['REQUEST_URI']; ?>" 
								id="slick-login">
		                    <fieldset>
					    	  	<div class="form-group">
					    		    <input class="form-control" 
					    		    		placeholder="E-mail" 
					    		    		type="text" 
					    		    		name='loginUser'>
					    		</div>
					    		<div class="form-group">
					    			<input class="form-control" 
					    					placeholder="Password" 
					    					type="password" 
					    					value="" 
					    					name='loginPassword'>
					    		</div>
					    		<div class="checkbox">
					    	    	<label>
					    	    		<input name="remember" type="checkbox" value="Remember Me"> Remember Me
					    	    	</label>
					    	    </div>
					    	    <input type="hidden" name="submitButton" value="1">
					    		<input class="btn btn-lg btn-success btn-block" type="submit" value="Login" id="login">
					    	</fieldset>
				      	</form>
				    </div>
				</div>
			</div>
		</div>
        <?php
        $wideBody = ob_get_contents();
        ob_end_clean();
        return $wideBody;
    }
    
    public function getSignOutContent()
    {
    	ob_start();
    	?>
       	<div class="row login-box" id="sign-in">
    		<div class="col-md-4 col-md-offset-4">
    			<h3 class="text-center">You've been logged out successfully</h3>
    			<br />
    	    	<div class="panel panel-default">
					<div class="panel-body">
						<a href="/" class="btn btn-lg btn-success btn-block">Login</a>
					</div>
    			</div>
    		</div>
    	</div>
		<?php
		$wideBody = ob_get_contents();
		ob_end_clean();
		return $wideBody;
     }
   	
   	/**
   	 * getSearchBar
   	 * 
   	 * it's the search bar, actually it doesn't work
   	 * 
   	 * @return string
   	 */
    
   	public static function getSearchBar()
   	{
   		ob_start();
   		?>
   		<!-- /search bar -->
   		<div class='filter-bar hide search-bar' id='x-search'>
   			<div class='inside cf'>
   				<form class='search-bar' href='#'>
   					<input name='search' id='input-search' placeholder='Type here to search, for example: La fe' type='text'>
   				</form>
   	   				<ul class='toggle-nav'>
   					<li>
   						<a class='action search' href='#' onclick="showTopBar(); return false;" id='x-hide-search'></a>
   					</li>
   				</ul>
   			</div><!-- /inside cf -->
   		</div>
   		<?php
   		$searchBar = ob_get_contents();
   		ob_end_clean();
   		return $searchBar;
   	}
   	
   	public function getSidebar()
   	{
   		ob_start();
   		$active = 'class="active"';
   		?>
   		<div class="col-sm-2 col-md-2 sidebar">
			<ul class="nav nav-sidebar">
				<li <?php if ($_GET['section'] == 1) echo $active; ?>><a href="/dashboard/">Dashboard</a></li>
				<li <?php if ($_GET['section'] == 12) echo $active; ?>><a href="/reservations/">Reservations</a></li>
				<!-- <li <?php if ($_GET['section'] == 2) echo $active; ?>><a href="/add-guest/">Add Guest</a></li> -->
				<li <?php if ($_GET['section'] == 9) echo $active; ?>><a href="/add-broker/">Add Broker</a></li>
			</ul>
			
			<ul class="nav nav-sidebar">
				<li <?php if ($_GET['section'] == 13) echo $active; ?>><a href="/rooms/">Rooms</a></li>
				<li <?php if ($_GET['section'] == 11) echo $active; ?>><a href="/calendar/">Calendar</a></li>
				<li <?php if ($_GET['section'] == 5) echo $active; ?>><a href="/agencies/">Agencies</a></li>
			</ul>
		</div>
   		<?php
   		$sideBar = ob_get_contents();
   		ob_end_clean();
   		return $sideBar;	
   	}   	
   	
   	public function getDashboardIcons() 
   	{
   		ob_start();
   		?>
   		<div class="row placeholders dashboard-icons">
			<div class="col-xs-6 col-sm-3 placeholder">
				<a href="/guests/">
					<i class="glyphicon glyphicon-th"></i>
					<h4>Guests</h4>
					<span class="text-muted">
					<?php 
					if ($this->data['recentMembers'] > 0)
						echo $this->data['recentMembers'];
					else 
						echo 'No';
					?>
					 recent guests
					</span>
				</a>
			</div>
			<div class="col-xs-6 col-sm-3 placeholder">
				<a href="/tasks/">
					<i class="glyphicon glyphicon-th-list"></i>
					<h4>Tasks</h4>
					<span class="text-muted">
						<strong><?php echo $this->data['taskInfo']['today']; ?></strong> tasks for today, 
						<strong><?php echo $this->data['taskInfo']['pending']; ?></strong> pending
					</span>
				</a>
			</div>
			<!-- <div class="col-xs-6 col-sm-3 placeholder">
				<a href="/email/">
					<i class="glyphicon glyphicon-envelope"></i>
					<h4>E-Mail</h4>
					<span class="text-muted"><?php echo $this->data['userInfo']['inbox']; ?> inbox</span>
				</a>
			</div> -->
			<div class="col-xs-6 col-sm-3 placeholder">
				<a href="/brokers/">
					<i class="glyphicon glyphicon-road"></i>
					<h4>Brokers</h4>
					<span class="text-muted">
					<?php 
					if ($this->data['recentBrokers'] > 0)
						echo $this->data['recentBrokers'];
					else 
						echo 'No';
					?> 
					recent brokers
					</span>
				</a>
			</div>
		</div>
   		<?php
   		$dashboardIcons = ob_get_contents();
   		ob_end_clean();
   		return $dashboardIcons;
   	}
   	
   	public function getRecentMembers()
   	{
   		ob_start();
   		?>
   		<h2 class="sub-header">Recent Guests</h2>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Member ID</th>
						<th>Name</th>
						<?php 
						if ($_SESSION['loginType'] == 1)
						{
							?>
							<th>Added by</th>
							<?php 
							} else {
							?>
							<th>Address</th>
							 <?php 
							}
						?>
						<th>City</th>
						<th>State</th>
						<th>Country</th>
						<th>Active</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ($this->data['lastMembers'] as $member)
					{
						?>
					<tr>
						<td>
							<a href="/<?php echo $member['member_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/">
								<?php echo $member['member_id']; ?>
							</a>
						</td>
						<td>
							<a href="/<?php echo $member['member_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/" class="member-link">
								<?php echo $member['name'].' '.$member['last_name']; ?>
							</a>
						</td>
						<?php 
						if ($_SESSION['loginType'] == 1)
						{
							?>
							<td><?php echo $member['user_name']; ?></td>
							<?php 
						} 
						else 
						{
							?>
							<td><?php echo $member['address']; ?></td>
							 <?php 
						}
						?>
						<td><?php echo $member['city']; ?></td>
						<td><?php echo $member['state']; ?></td>
						<td><?php echo $member['country']; ?></td>
						<td>
							<?php 
							if ($member['active'] == 1)
							{
								?>
							<i class="glyphicon glyphicon-ok"></i>
								<?php 
							} else {
								?>
							<i class="glyphicon glyphicon-remove"></i>
								<?php 
							}
							?>
						</td>
					</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
   		<?php
   		$membersRecent = ob_get_contents();
   		ob_end_clean();
   		return $membersRecent;
   	}
   	
   	public function getRecentBrokers()
   	{
   		ob_start();
   		?>
   	   	<h2 class="sub-header">Recent brokers</h2>
   		<div class="table-responsive">
   			<table class="table table-striped">
   				<thead>
   					<tr>
   						<th>Member ID</th>
   						<th>Name</th>
   						<?php 
   						if ($_SESSION['loginType'] == 1)
   						{
   							?>
   							<th>Added by</th>
   							<?php 
   							} else {
   							?>
   							<th>Address</th>
   								<?php 
   							}
   						?>
   						<th>City</th>
   						<th>State</th>
   						<th>Country</th>
   						<th>Active</th>
   					</tr>
   				</thead>
   				<tbody>
   					<?php 
   					foreach ($this->data['lastBrokers'] as $member)
   					{
   						?>
   					<tr>
   						<td>
   							<a href="/broker/<?php echo $member['broker_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/">
   								<?php echo $member['broker_id']; ?>
   							</a>
   						</td>
   						<td>
   							<a href="/broker/<?php echo $member['member_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/" class="member-link">
   								<?php echo $member['name'].' '.$member['last_name']; ?>
   							</a>
   						</td>
   						<?php 
   						if ($_SESSION['loginType'] == 1)
   						{
   							?>
   							<td><?php echo $member['user_name']; ?></td>
   							<?php 
   						} 
   						else 
   						{
   							?>
   							<td><?php echo $member['address']; ?></td>
   								<?php 
   						}
   						?>
   						<td><?php echo $member['city']; ?></td>
   						<td><?php echo $member['state']; ?></td>
   						<td><?php echo $member['country']; ?></td>
   						<td>
   							<?php 
   							if ($member['active'] == 1)
   							{
   								?>
   							<i class="glyphicon glyphicon-ok"></i>
   								<?php 
   							} else {
   								?>
   							<i class="glyphicon glyphicon-remove"></i>
   								<?php 
   							}
   							?>
   						</td>
   					</tr>
   						<?php
   						}
   					?>
   				</tbody>
   			</table>
   		</div>
   	   	<?php
   	   	$membersRecent = ob_get_contents();
   	   	ob_end_clean();
   	   	return $membersRecent;
   	}
   	
   	public function getAllMembers()
   	{
   		ob_start();
   		?>
   		<div class="table-responsive">
   			<table class="table table-striped">
   				<thead>
   					<tr>
   						<th>Member ID</th>
   						<th>Name</th>
   						<?php 
   						if ($_SESSION['loginType'] == 1)
   						{
   							?>
   							<th>Added by</th>
   							<?php 
   							} else {
   							?>
   							<th>Address</th>
   							 <?php 
   							}
   						?>
   						<th>City</th>
   						<th>State</th>
   						<th>Country</th>
   						<th>Active</th>
   					</tr>
   				</thead>
   				<tbody>
   					<?php 
   					foreach ($this->data['members'] as $member)
   					{
   					?>
   					<tr>
   						<td>
   							<a href="/<?php echo $member['member_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/">
   							<?php echo $member['member_id']; ?>
   							</a>
   						</td>
   						<td>
   							<a href="/<?php echo $member['member_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/" class="member-link">
   								<?php echo $member['name'].' '.$member['last_name']; ?>
   							</a>
   						</td>
   						<?php 
   						if ($_SESSION['loginType'] == 1)
   						{
   							?>
   							<td><?php echo $member['user_name']; ?></td>
   							<?php 
   							} else {
   							?>
   							<td><?php echo $member['address']; ?></td>
   							 <?php 
   							}
   						?>
   						
   						<td><?php echo $member['city']; ?></td>
   						<td><?php echo $member['state']; ?></td>
   						<td><?php echo $member['country']; ?></td>
   						<td>
   							<?php 
   							if ($member['active'] == 1)
   							{
   								?>
   							<i class="glyphicon glyphicon-ok"></i>
   								<?php 
   							} else {
   								?>
   							<i class="glyphicon glyphicon-remove"></i>
   								<?php 
   							}
   							?>
   						</td>
   					</tr>
   						<?php
   					}
   					?>
   				</tbody>
   			</table>
   		</div>
   	   	<?php
   	   	$membersRecent = ob_get_contents();
   	   	ob_end_clean();
   	   	return $membersRecent;
   	}
   	
   	public function getAllBrokers()
   	{
   		ob_start();
   		?>
   	   		<div class="table-responsive">
   	   			<table class="table table-striped">
   	   				<thead>
   	   					<tr>
   	   						<th>Member ID</th>
   	   						<th>Name</th>
   	   						<?php 
   	   						if ($_SESSION['loginType'] == 1)
   	   						{
   	   							?>
   	   							<th>Added by</th>
   	   							<?php 
   	   							} else {
   	   							?>
   	   							<th>Address</th>
   	   							 <?php 
   	   							}
   	   						?>
   	   						
   	   						<!-- <th>City</th>
   	   						<th>State</th> -->
   	   						<th>Added at</th>
   	   						<th>Country</th>
   	   						<th>Active</th>
   	   					</tr>
   	   				</thead>
   	   				<tbody>
   	   					<?php 
   	   					foreach ($this->data['brokers'] as $member)
   	   					{
   	   					?>
   	   					<tr>
   	   						<td>
   	   							<a href="/broker/<?php echo $member['broker_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/">
   	   							<?php echo $member['broker_id']; ?>
   	   							</a>
   	   						</td>
   	   						<td>
   	   							<a href="/broker/<?php echo $member['broker_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/" class="member-link">
   	   								<?php echo $member['name'].' '.$member['last_name']; ?>
   	   							</a>
   	   						</td>
   	   						<?php 
   	   						if ($_SESSION['loginType'] == 1)
   	   						{
   	   							?>
   	   							<td><?php echo $member['user_name']; ?></td>
   	   							<?php 
   	   							} else {
   	   							?>
   	   							<td><?php echo $member['address']; ?></td>
   	   							 <?php 
   	   							}
   	   						?>
   	   						
   	   						<!-- <td><?php echo $member['city']; ?></td>
   	   						<td><?php echo $member['state']; ?></td> -->
   	   						<td><?php echo Tools::formatMYSQLToFront($member['date']); ?></td>
   	   						<td><?php echo $member['country']; ?></td>
   	   						<td>
   	   							<?php 
   	   							if ($member['active'] == 1)
   	   							{
   	   								?>
   	   							<i class="glyphicon glyphicon-ok"></i>
   	   								<?php 
   	   							} else {
   	   								?>
   	   							<i class="glyphicon glyphicon-remove"></i>
   	   								<?php 
   	   							}
   	   							?>
   	   						</td>
   	   					</tr>
   	   						<?php
   	   					}
   	   					?>
   	   				</tbody>
   	   			</table>
   	   		</div>
   	   	   	<?php
   	   	   	$membersRecent = ob_get_contents();
   	   	   	ob_end_clean();
   	   	   	return $membersRecent;
   	   	}
   	
   	public static function listTasks($tasks)
   	{
   		ob_start();
   		
   		if ($tasks)
   		{
   			foreach ($tasks as $task)
   			{
   				$date = Tools::formatMYSQLToFront($task['date']);
   				$time = Tools::formatHourMYSQLToFront($task['time']);
   				?>
				<li
   				<?php 
   				if( $task['status'] == 1)
   					echo 'class="completed"';
   				
   				if( strtotime($date) == strtotime(@date('d-M-Y', strtotime('now'))))
   					echo 'class="today"';
   							
   				if( strtotime($date) < strtotime('now'))
   					echo 'class="pending"';
   							
   				if( strtotime($date) > strtotime('now'))
   					echo 'class="future"';
   				
   				
   				?>
   				>
   					<div class="header">
   						<div class="info">
   							<strong><?php echo $task['assigned_to']; ?> </strong>
   							<span class="text-primary"><?php echo $date.' '.$time; ?></span>
   							<span class="text-muted"><?php echo $task['assigned_by']; ?></span>
   						</div>
   						<?php 
	                    if ($task['status'] == 0)
	                    {
	                    ?>
	                    	<a href="javascript: void(0);" class="completeTask" tid="<?php echo $task['task_id']; ?>"><i class="glyphicon glyphicon-check icon" ></i></a>
	                    <?php 
	                    }
	                    
   						if ($task['member_id'])
   						{
   						?>
   						<div class="member">
   							<a href="/<?php echo $task['member_id']; ?>/<?php echo Tools::slugify($task['name'].' '.$task['last_name']); ?>/">
   								<?php echo $task['name'].' '.$task['last_name']; ?>
   							</a>
   						</div>
   						<?php
   						}
   						?>
   						<div class="clear"></div>
   					</div>
   					<div class="clear"></div>
   					<div>
   						<i class="glyphicon glyphicon-option-vertical"></i>
   						<div class="history-title">
   							<span class="task-title-sp"><?php echo $task['content']; ?></span>
   						</div>
   					</div>
   					<div class="clear"></div>
   				</li>
   				<?php
   				}
   			}
   		$tasks = ob_get_contents();
   		ob_end_clean();
   		return $tasks;
   	}
   	
   	public function getHistoryPanel()
   	{
   		ob_start();
   		?>
   		<div class="col-sm-12 history-member-panel">
			<div class="row text-right">
				<a href="javascript:void(0);" class="btn btn-info btn-xs display-add-history">add history</a>
			</div>
			
			<div class="row history-member-box">
				<textarea rows="2" cols="" class="form-control" placeholder="history" id="history-entry"></textarea>
				<a href="javascript:void(0);" class="btn btn-info btn-xs" id="add-history">save</a>
			</div>
			
			<div class="row history-content">
				<ul class="history-list">
					<?php
					if ($this->data['memberHistory'])
					{
						foreach ($this->data['memberHistory'] as $history)
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
					?>
				</ul>
			</div>
		</div>
   		<?php
   		$historyPanel = ob_get_contents();
   		ob_end_clean();
   		return $historyPanel;
   	}
   	
   	public function getAllTasks()
   	{
   		ob_start();
   		?>
		<div class="col-sm-12 task-member-panel">
			<div class="row main-menu-tasks text-center">
				<ul class="nav nav-pills">
					<li>
						<a href="#" class="text-danger" id="get-pending-tasks">
							Pending
							<?php if ($this->data['taskInfo']['pending'] > 0) {?><span class="badge"><?php echo $this->data['taskInfo']['pending']; ?></span><?php } ?>
						</a>
					</li>
					<li>
						<a href="#" class="text-primary" id="get-today-tasks">
							Today 
							<?php if ($this->data['taskInfo']['today'] > 0) {?><span class="badge"><?php echo $this->data['taskInfo']['today']; ?></span><?php } ?>
						</a>
					</li>
					<li>
						<a href="#" class="text-warning" id="get-future-tasks">
							Future 
							<?php if ($this->data['taskInfo']['future'] > 0) {?><span class="badge"><?php echo $this->data['taskInfo']['future']; ?></span><?php } ?>
						</a>
					</li>
					<li>
						<a href="#" class="text-success" id="get-completed-tasks">
							Completed
						</a>
					</li>
				</ul>
			</div>
			<div class="row task-content">
				<ul class="task-list">
				<?php 
				echo $this->listTasks($this->data['memberTasks']);
				?>
				</ul>
			</div>
		</div>
   		<?php
   		$tasks = ob_get_contents();
   		ob_end_clean();
   		return $tasks; 
   	}
   	
   	public function getDocuments()
   	{
   		ob_start();
   		?>
   		<div class="table-responsive">
			<!-- <table class="table table-striped documents">
				<thead>
					<tr>
						<th></th>
						<th>Document</th>
						<th>Description</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>
							<a href="/files/documents/001-call-center-project.pdf" target="_blank">Call Center Project Pitch</a>
						</td>
						<td>Pitch for the call center</td>
						<td>
							<a href="/files/documents/001-call-center-project.pdf" download="001-call-center-project.pdf">
								<i class="glyphicon glyphicon-download text-success"></i>
							</a>
						</td>
					</tr>
					
					<tr>
						<td>2</td>
						<td>
							<a href="/files/documents/002-e-marketing-project.pdf" target="_blank">E-Marketing Project e-mail</a>
						</td>
						<td>E-mail for send to the e-marketing database</td>
						<td>
							<a href="/files/documents/002-e-marketing-project.pdf" download="002-e-marketing-project.pdf">
								<i class="glyphicon glyphicon-download text-success"></i>
							</a>
						</td>
					</tr>
				</tbody>
			</table> -->
		</div>
   	   	<?php
   	   	$tasks = ob_get_contents();
   	   	ob_end_clean();
   	   	return $tasks; 
   	}
   	
   	public function getCalendarHead()
   	{
   		ob_start();
   		?>
<!-- 		<script src="/js/calendar.js"></script> -->
   				<link href='/js/calendar/fullcalendar.css' rel='stylesheet' />
   				<link href='/js/calendar/fullcalendar.print.css' rel='stylesheet' media='print' />
   				<script src='/js/calendar/lib/moment.min.js'></script>
   	<!-- 			<script src='../lib/jquery.min.js'></script> -->
   				<script src='/js/calendar/fullcalendar.js'></script>
   				<script>
   				$(document).ready(function() {
   					
   					$('#fullcalendar').fullCalendar({
   						header: {
   							left: 'prev,next today',
   							center: 'title',
   							right: 'month,agendaWeek,agendaDay'
   						},
//    						defaultDate: '2015-02-12',
   						editable: true,
   						eventLimit: true, // allow "more" link when too many events
   						events: [
   		   					<?php
   		   					$c = 0;
   		   					
   		   					foreach ($this->data['reservations'] as $reservation)
   		   					{
   		   						$c++;
   		   						?>
	   		   					{
		   		   					id: <?php echo $reservation['reservation_id']?>,
	   								title: '<?php echo $reservation['abbr'].' '.$reservation['room'].' '.$reservation['name'].' '.$reservation['last_name']; ?>',
	   								start: '<?php echo $reservation['check_in']; ?>',
	   								end: '<?php echo $reservation['check_out']; ?>'
	   							}
   		   						<?php
   		   						if ($c < sizeof($this->data['reservations']))
   		   							echo ',';
   		   					}
   		   					?>
   						]
   					});
   					
   				});
   			
		</script>
		<?php		
	   	$signIn = ob_get_contents();
		ob_end_clean();
		return $signIn;
	}
   	
   	public function getCalendar()
   	{
   		ob_start();
   		?>
   	   	<div id='fullcalendar'></div>
		<?php
   	   	$tasks = ob_get_contents();
		ob_end_clean();
		return $tasks; 
	}
   	
   	public function getInbox()
   	{
   		ob_start();
   		?>
   		<div class="inbox-head">
			<h3>Inbox</h3>
		</div>
		<div class="inbox-body">
<!-- 					<div class="mail-option"> -->
<!-- 						<div class="btn-group"> -->
<!-- 							<a class="btn mini tooltips" href="#" data-toggle="dropdown" data-placement="top" data-original-title="Refresh"> -->
<!-- 								<i class=" glyphicon glyphicon-refresh"></i> -->
<!-- 							</a> -->
<!-- 						</div> -->
<!-- 					</div> -->
			<table class="table table-inbox table-hover">
				<tbody>
					<?php 
					foreach ($this->data['email']['inbox'] as $message)
					{
						$personalName = '';
						
						if ($message['member_name'])
							$personalName = $message['member_name'];
						else 
							$personalName = utf8_decode(imap_utf8($message['personal_name']));
								
						$status = '';
						if ($message['inbox'] == 1)
							$status = 'unread';
						?>
					<tr  class="<?php echo $status; ?> get-message-not-member" mid="<?php echo $message['message_id']; ?>">
<!-- 								<td class="inbox-small-cells"> -->
<!-- 									<input type="checkbox" class="mail-checkbox"> -->
<!-- 								</td> -->
						<td class="inbox-small-cells">
							<?php 
							if ($message['member_id'])
							{
								?>
							<i class="glyphicon glyphicon-user"></i>
								<?php
							}
							?>
						</td>
						<td class="view-message dont-show"><?php echo $personalName; ?></td>
						<td class="view-message"><?php echo substr($message['subject'], 0, 30); ?></td>
						<td class="view-message inbox-small-cells">
						<?php 
						if ($message['attachment'])
						{
							?>
							<i class="glyphicon glyphicon-paperclip"></i>
							<?php 
						}
						?>
						</td>
						<td class="view-message text-right"><?php echo $message['date']; ?></td>
					</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
   		<?php
   		$inbox = ob_get_contents();
   		ob_end_clean();
   		return $inbox;
   	}
   	
   	public function getViewMail($message)
   	{
   		ob_start();
   		?>
		<div class="inbox-head">
			<h3>View Mail</h3>
		</div>
		<div class="inbox-body view-body">
			<div class="heading-inbox row">
				<div class="col-md-8">
					<div class="compose-btn">
						<a class="btn btn-xs btn-info" href="mail_compose.html"><i class="glyphicon glyphicon-share-alt"></i> Reply</a>
					</div>
				</div>
				<div class="col-md-4 text-right">
					<p class="date"> <?php echo $message['hour']; ?> <?php echo $message['date']; ?></p>
				</div>
				<div class="col-md-12">
					<h4><?php echo $message['subject']; ?></h4>
				</div>
			</div>
			<div class="sender-info">
				<div class="row">
					<div class="col-md-12">
						<strong>
						<?php 
						$personalName = '';
						
						if ($message['member_name'])
							$personalName = $message['member_name'];
						else 
							$personalName = utf8_decode(imap_utf8($message['personal_name']));
						
						echo $personalName;
						?>
						</strong>
						<span>[<?php echo $message['from_email']; ?>]</span>
						to
						<strong>me</strong>
					</div>
				</div>
			</div>
			<div class="view-mail">
				<?php echo $message['message']; ?>
			</div>
			<div class="attachment-mail">
				<p>
					<span><i class="glyphicon glyphicon-paperclip"></i> 3 attachments — </span>
					<a href="#">Download all attachments</a>
				</p>
				<ul>
					<li>
						<a href="#" class="atch-thumb">
							<img src="img/pro-ac-1.png">
						</a>
						
						<div class="file-name">
							img_name.jpg
						</div>
						<span>30KB</span>
						<div class="links">
							<a href="#">View</a> -
							<a href="#">Download</a>
						</div>
					</li>
				</ul>
			</div>
			<div class="compose-btn pull-left">
				<a class="btn btn-xs btn-info" href="mail_compose.html"><i class="fa fa-reply"></i> Reply</a>
				<button class="btn btn-xs "><i class="glyphicon glyphicon-arrow-right"></i> Forward</button>
			</div>
		</div>
   		<?php
   		$viewMail = ob_get_contents();
   		ob_end_clean();
   		return $viewMail;
   	}
   	
   	public function getEmail()
   	{
   		ob_start();
   		?>
		<div class="mail-box">
			<aside class="sm-side">
				<div class="user-head">
					<div class="user-name">
						<h5><a href="#"><?php echo $this->data['userInfo']['name']; ?></a></h5>
						<span><a href="#"><?php echo $this->data['userInfo']['user_email']; ?></a></span>
					</div>
				</div>
<!-- 				<div class="inbox-body"> -->
<!-- 					<a class="btn btn-compose" href="#myModal"> -->
<!-- 						Compose -->
<!-- 					</a> -->
<!-- 				</div> -->
				<ul class="inbox-nav inbox-divider">
					<li class="active">
						<a href="#">
							<i class="glyphicon glyphicon-inbox"></i> 
							Inbox 
							<?php 
							if ($this->data['userInfo']['inbox'] > 0)
							{
								?>
								<span class="label label-danger pull-right"><?php echo $this->data['userInfo']['inbox']; ?></span>
								<?php
							}
							?>
						</a>
					</li>
					<li>
					<a href="#"><i class="glyphicon glyphicon-envelope"></i> Sent Mail</a>
					</li>
				</ul>
			</aside>
			<aside class="lg-side" id="email-content">
				<?php echo Layout_View::getInbox(); ?>
				<?php //echo Layout_View::getViewMail(); ?>
			</aside>
		</div>
   	   	<?php
   	   	$tasks = ob_get_contents();
   	   	ob_end_clean();
   	   	return $tasks; 
   	}
   	
   	public function getTaskPanel()
   	{
   		ob_start();
   		?>
   		<div class="col-sm-12 task-member-panel">
			<div class="row text-right">
				<a href="javascript:void(0);" class="btn btn-info btn-xs display-add-task">add task</a>
			</div>
			
			<div class="row task-member-box">
				<div class="create-task-box" id="create-task-box">
					<div class="top">
						<?php 
						if ($this->data['userInfo']['type'] == 1)
						{
						?>
						<div class="to">
							<label>To</label>
							<select id="task_to">
							<?php 
							if ($this->data['usersActive'])
							{
								foreach ($this->data['usersActive'] as $user) 
								{
									?>
								<option value="<?php echo $user['user_id']; ?>"><?php echo $user['name']; ?></option>
									<?php
								}
							}
							?>
							</select>
						</div>
						<?php 
						}
						else
						{
							?>
						<input type="hidden" id="task_to" value="<?php echo $this->data['userInfo']['user_id']; ?>">
							<?php
						}
						?>
						
						<div class="date">
							<label>Date</label>
							<input type="text" id="task-date" />
						</div>
						
						<div class="hour">
							<label>Time</label>
							<select id="task_hour">
								<option value="8:00">8:00</option>
								<option value="8:30">8:30</option>
								<option value="9:00">9:00</option>
								<option value="9:30">9:30</option>
								<option value="10:00">10:00</option>
								<option value="10:30">10:30</option>
								<option value="11:00">11:00</option>
								<option value="11:30">11:30</option>
								<option value="12:00">12:00</option>
								<option value="12:30">12:30</option>
								<option value="13:00">13:00</option>
								<option value="13:30">13:30</option>
								<option value="14:00">14:00</option>
								<option value="14:30">14:30</option>
								<option value="15:00">15:00</option>
								<option value="15:30">15:30</option>
								<option value="16:00">16:00</option>
								<option value="15:30">16:30</option>
								<option value="17:00">17:00</option>
								<option value="17:30">17:30</option>
								<option value="18:00">18:00</option>
								<option value="18:30">18:30</option>
								<option value="19:00">19:00</option>
								<option value="19:30">19:30</option>
								<option value="20:00">20:00</option>
								<option value="20:30">20:30</option>
							</select>
						</div>
						<div class="clear"></div>
					</div><!--  /top -->
					<div class="middle">
						<textarea rows="" cols="" id="task_content" class="form-control" placeholder="new task"></textarea>
						<a href="javascript:void(0);" class="btn btn-info btn-xs" id="add-task">save</a>
					</div>
				</div>
			</div>
			
			<div class="row task-content">
				<ul class="task-list">
					<?php
					echo $this->listTasks($this->data['memberTasks']);
					?>
				</ul>
			</div>
		</div>
   		<?php
   		$taskPanel = ob_get_contents();
   		ob_end_clean();
   		return $taskPanel;
   	}
   	
   	public function getMembersHead()
   	{
   		ob_start();
   		?>
		<link rel="stylesheet" href="/css/jquery-ui.css">
		<script src="/js/jquery-ui.js"></script>
		<script src="/js/members.js"></script>
		<script src="/js/history.js"></script>
		<script src="/js/tasks.js"></script>
		<script src="/js/reservations.js"></script>
		<script>
		$(function() {
			$( "#task-date, #checkIn, #checkOut" ).datepicker();
			});
		</script>
		<?php
		$signIn = ob_get_contents();
		ob_end_clean();
		return $signIn;
	}
   	
   	public function getAddMember()
   	{
   		ob_start();
   		if ($this->data['memberInfo']['member_id'])
   		{
   			$memberId = $this->data['memberInfo']['member_id'];
   			$memberId = str_pad($memberId, 4, '0', STR_PAD_LEFT);
   		}
   		else
   		{
   			$memberId = '0000';
   		}
   		?>
		<div class="row">
			<div class="col-md-6">
				<form class="form-horizontal" role="form">
					<fieldset>
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput"><b>Guest#</b></label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $memberId; ?>" class="form-control" id="member-id" readonly="readonly">
							</div>
						</div>
					
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Name</label>
							<div class="col-sm-10">
								<input type="text" placeholder="Name" class="form-control" id="member-name" value="<?php echo $this->data['memberInfo']['name']; ?>">
							</div>
						</div>
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Last Name</label>
							<div class="col-sm-10">
								<input type="text" placeholder="Last Name" class="form-control" id="member-last-name" value="<?php echo $this->data['memberInfo']['last_name']; ?>">
							</div>
						</div>
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Address</label>
							<div class="col-sm-10">
								<input type="text" placeholder="Address" class="form-control" id="member-address" value="<?php echo $this->data['memberInfo']['address']; ?>">
							</div>
						</div>
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Country</label>
							<div class="col-sm-10">
								<select id="country_list" onchange="selCountry(this);" class="form-control">
									<option value="0">Select Country</option>
									<?php 
									if ($this->data['memberInfo']['country'])
									{
										?>
									<option value="<?php echo $this->data['memberInfo']['country_code']; ?>" selected><?php echo $this->data['memberInfo']['country']; ?></option>
										<?php
									}
									else 
									{
										foreach ($this->data['countries'] as $cl)
										{
											?>
									<option value="<?php echo $cl['Code']; ?>"><?php echo $cl['Name']; ?></option>
											<?php	
										}
									}
									?>
								</select>
							</div>
						</div>
						<input type="hidden" id="country" value="" />
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">State</label>
							<div class="col-sm-10">
								<select id="state_list" onchange="selState(this);" class="form-control">
									<option value="0">Select State</option>
									<?php 
									if ($this->data['memberInfo']['state'])
									{
										?>
									<option selected><?php echo $this->data['memberInfo']['state']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<input type="hidden" id="mState" value="" />
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">City</label>
							<div class="col-sm-10">
								<select id="city_list" onchange="selCity(this);" class="form-control">
									<option value="0">Select City</option>
									<?php 
									if ($this->data['memberInfo']['city'])
									{
										?>
									<option selected><?php echo $this->data['memberInfo']['city']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<input type="hidden" id="city" value="" />
					
					</fieldset>
				</form>
			</div><!-- /.col-lg-12 -->
			
			<div class="col-md-6">
				<form class="form-horizontal" role="form">
					<fieldset>
						<!-- Text input-->
						<div id="memberEmails">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Email</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Email" class="form-control memberEmail" eid="0">
								</div>
								<a href="javascript:void(0);" id="addEmailField" class="text-success col-sm-1 control-label">
									<i class="glyphicon glyphicon-plus"></i>
								</a>
							</div>
							<?php 
							if ($this->data['memberEmails'])
							{
								foreach ($this->data['memberEmails'] as $email)
								{
								?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Email</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Email" class="form-control memberEmail" eid="0" value="<?php echo $email['email']; ?>">
								</div>
							</div>	
								<?php
								}
							}
							?>
						</div>

						<!-- Text input-->
						<div id="memberPhones">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Phone</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Phone" class="form-control memberPhone" pid="0">
								</div>
								<a href="javascript:void(0);" class="text-success col-sm-1 control-label" id="addPhoneField" >
									<i class="glyphicon glyphicon-plus"></i>
								</a>
							</div>
							<?php 
							if ($this->data['memberPhones'])
							{
								foreach ($this->data['memberPhones'] as $phone)
								{
								?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Phone</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Phone" class="form-control memberPhone" pid="0" value="<?php echo $phone['phone']; ?>">
								</div>
							</div>	
								<?php
								}
							}
							?>
						</div>
					</fieldset>
					
					<fieldset>
						<!-- Form Name -->
						<legend>Notes</legend>
						<!-- Textarea input-->
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
								<textarea rows="6" cols="" class="form-control" placeholder="notes" id="notes"><?php echo $this->data['memberInfo']['notes']; ?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
							<div class="pull-right">
								<button type="submit" class="btn btn-primary" id="memberSave">Save</button>
							</div>
							</div>
						</div>
					</fieldset>
				</form>
			</div><!-- /.col-lg-12 -->
		</div><!-- /.row -->

		<div class="row">
			<div class="alert alert-success alert-autocloseable-success">
        		<i class="glyphicon glyphicon-ok"></i> Guest saved
			</div>
		</div>

		<div class="row utilities">
			<div class="col-md-12">
	
				<div class="tabbable-panel">
					<div class="tabbable-line">
						<ul class="nav nav-tabs ">
							<li class="active">
								<a href="#tab_default_1" data-toggle="tab">Reservations</a>
							</li>
							<li class="">
								<a href="#tab_default_2" data-toggle="tab">History </a>
							</li>
							<li>
								<a href="#tab_default_3" data-toggle="tab">Tasks</a>
							</li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_default_1">
								<?php echo $this->getMemberReservations(); ?>
							</div>
							<div class="tab-pane" id="tab_default_2">
								<?php echo $this->getHistoryPanel(); ?>
							</div>
							<div class="tab-pane" id="tab_default_3">
								<?php echo $this->getTaskPanel(); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$membersRecent = ob_get_contents();
		ob_end_clean();
		return $membersRecent;
	}
   	
	public static function getRoomsList($rooms)
	{
		ob_start();
		?>
		<?php 
		if ($rooms)
		{
			?>
			<ul class="roomTypeList">
				
			<?php
			$roomType = 0;
			$c = 0;
			foreach ($rooms as $room)
			{
				if ($c == 0 && $roomType != $room['room_type_id']) 
				{
					?>
				<li class="row">
					<div class="roomType row bg-primary">
						<div class="title col-sm-8"><strong><?php echo $room['room_type']; ?></strong></div>
<!-- 						<div class="price col-sm-4"><strong>$550 USD</strong></div> -->
					</div>
					<ul class="roomList">
					<?php
					$roomType = $room['room_type_id'];
				}
				?>
						<li class="row bg-success">
							<div class="title col-sm-8">
								<strong><?php echo $room['room']; ?></strong>
							</div>
							<div class="operator col-sm-4">
								<a href="javascript:void (0);" rn="<?php echo $room['room']; ?>" ri="<?php echo $room['room_id']; ?>">book now</a>
							</div>
						</li>
				<?php
				if ($roomType != $room['room_type_id'] )
				{
					?>
					</ul>
				</li>
				<li class="row">
					<div class="roomType row bg-primary">
						<div class="title col-sm-8"><strong><?php echo $room['room_type']; ?></strong></div>
<!-- 						<div class="price col-sm-4"><strong>$550 USD</strong></div> -->
					</div>
					<ul class="roomList">	
					<?php
					$roomType = $room['room_type_id'];
				}
				$c++;
			}
			?>
			</ul>
			<?php
		}
		?>
		
		<?php 
		$roomList = ob_get_contents();
		ob_end_clean();
		return $roomList;
	}
	
	public function getReservationPanel()
	{
		ob_start();
		?>
		<div class="col-sm-12 reservation-member-panel">
			<div class="reservationBox" id="reservationBox">
				<div class="searchReservation row">
					<div class="col-sm-3">
						<label>Check In</label>
						<input type="text" class="checkIn" id="checkIn" />
					</div>
					<div class="col-sm-3">
						<label>Check Out</label>
						<input type="text" class="checkOut" id="checkOut" />
					</div>
					<div class="col-sm-2">
						<label>Adults</label>
						<select id="reservationAdults">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
						</select>
					</div>
					<div class="col-sm-2">
						<label>Children</label>
						<select id="reservationChildren">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>
					<div class="col-sm-2">
						<a href="javascript:void(0);" class="btn btn-info btn-xs" id="searchReservation">search</a>
					</div>
				</div>
				<div class="row">
					<div class="reservationResults row col-sm-6" id="reservationResults">
						<?php //echo $this->getRoomsList($rooms); ?>
					</div>
					<div class="row col-sm-6">
						<?php echo $this->getRightSideReservations(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
		$reservationPanel = ob_get_contents();
		ob_end_clean();
		return $reservationPanel;
	}
	
	public function getRightSideReservations()
	{
		ob_start();
		?>
		<div class="row rightSideReservations" id="rightSideReservations">
			<p class="bg-success text-center roomName"><strong id="roomName"></strong></p>
			<p class="text-success text-center">from <span id="checkInReservation"></span> to <span id="checkOutReservation"></span></p>
			<p class="text-info text-center"> <span id="totalDays"></span> nights</p>
			<div class="forms">
				
				<input type="hidden" id="roomId" value='0' />
				<?php 
				if (!$this->data['memberInfo']['member_id'])
				{
					?>
				<input type="hidden" id="memberId" value='0' />
				<div class="row">
					<div class="col-sm-3">
						<label>Name</label>
					</div>
					<div class="col-sm-9">
						<input type="text" id="member-name" />
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-3">
						<label>Last Name</label>
					</div>
					<div class="col-sm-9">
						<input type="text" id="member-last-name" />
					</div>
				</div>
					<?php
				}
				?>
				
				<div class="row">
					<div class="col-sm-3">
						<label>Agency</label>
					</div>
					<div class="col-sm-9">
						<select id="agencyList">
							<?php
							foreach ($this->data['agencies'] as $agency)
							{
								?>
								<option value="<?php echo $agency['agency_id']; ?>"><?php echo $agency['agency']; ?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-3">
						<label>Price per Night</label>
					</div>
					<div class="col-sm-9">
						<input type="text" id="pricePerNight" />
					</div>
				</div>
				
				<div class="row">
					<div class="col-sm-3">
						<label>Total</label>
					</div>
					<div class="col-sm-9">
						<input type="text" id="totalReservation" />
					</div>
				</div>
				
				<?php 
				if (!$this->data['memberInfo']['member_id'])
				{
					?>
				<div class="row text-center">
					<a href="javascript:void(0);" class="btn btn-info btn-xs" id="bookRoom">Book Now</a>
				</div>
				
				<div class="row text-center">
					<a href="javascript:void(0);" class="text-success" id="completeProfileBtn">Complete Profile</a>
				</div>
					<?php
				}
				else 
				{
					?>
				<div class="row text-center">
					<a href="javascript:void(0);" class="btn btn-info btn-xs" id="bookRoomMember">Book Now</a>
				</div>
					<?php
				}
				?>	
			</div>
		</div>
		<?php
		$rightSideReservations = ob_get_contents();
		ob_end_clean();
		return $rightSideReservations;
	}
	
	public function getAddBroker()
	{
		ob_start();
		if ($this->data['memberInfo']['broker_id'])
		{
			$memberId = $this->data['memberInfo']['broker_id'];
			$memberId = str_pad($memberId, 4, '0', STR_PAD_LEFT);
		}
		else
		{
			$memberId = '0000';
		}
		?>
		<div class="row">
			<div class="col-md-6">
				<form class="form-horizontal" role="form">
					<fieldset>
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput"><b>Broker#</b></label>
							<div class="col-sm-10">
								<input type="text" value="<?php echo $memberId; ?>" class="form-control" id="member-id" readonly="readonly">
							</div>
						</div>
					
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Name</label>
							<div class="col-sm-10">
								<input type="text" placeholder="Name" class="form-control" id="member-name" value="<?php echo $this->data['memberInfo']['name']; ?>">
							</div>
						</div>
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Last Name</label>
							<div class="col-sm-10">
								<input type="text" placeholder="Last Name" class="form-control" id="member-last-name" value="<?php echo $this->data['memberInfo']['last_name']; ?>">
							</div>
						</div>
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Address</label>
							<div class="col-sm-10">
								<input type="text" placeholder="Address" class="form-control" id="member-address" value="<?php echo $this->data['memberInfo']['address']; ?>">
							</div>
						</div>

						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">Country</label>
							<div class="col-sm-10">
								<select id="country_list" onchange="selCountry(this);" class="form-control">
									<option value="0">Select Country</option>
									<?php 
									if ($this->data['memberInfo']['country'])
									{
										?>
									<option value="<?php echo $this->data['memberInfo']['country_code']; ?>" selected><?php echo $this->data['memberInfo']['country']; ?></option>
										<?php
									}
									else 
									{
										foreach ($this->data['countries'] as $cl)
										{
											?>
									<option value="<?php echo $cl['Code']; ?>"><?php echo $cl['Name']; ?></option>
											<?php	
										}
									}
									?>
								</select>
							</div>
						</div>
						<input type="hidden" id="country" value="" />
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">State</label>
							<div class="col-sm-10">
								<select id="state_list" onchange="selState(this);" class="form-control">
									<option value="0">Select State</option>
									<?php 
									if ($this->data['memberInfo']['state'])
									{
										?>
									<option selected><?php echo $this->data['memberInfo']['state']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<input type="hidden" id="mState" value="" />
						
						<!-- Text input-->
						<div class="form-group">
							<label class="col-sm-2 control-label" for="textinput">City</label>
							<div class="col-sm-10">
								<select id="city_list" onchange="selCity(this);" class="form-control">
									<option value="0">Select City</option>
									<?php 
									if ($this->data['memberInfo']['city'])
									{
										?>
									<option selected><?php echo $this->data['memberInfo']['city']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<input type="hidden" id="city" value="" />
					
					</fieldset>
				</form>
			</div><!-- /.col-lg-12 -->
			
			<div class="col-md-6">
				<form class="form-horizontal" role="form">
					<fieldset>
						<!-- Text input-->
						<div id="memberEmails">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Email</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Email" class="form-control memberEmail" eid="0">
								</div>
								<a href="javascript:void(0);" id="addEmailField" class="text-success col-sm-1 control-label">
									<i class="glyphicon glyphicon-plus"></i>
								</a>
							</div>
							<?php 
							if ($this->data['memberEmails'])
							{
								foreach ($this->data['memberEmails'] as $email)
								{
								?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Email</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Email" class="form-control memberEmail" eid="0" value="<?php echo $email['email']; ?>">
								</div>
							</div>	
								<?php
								}
							}
							?>
						</div>

						<!-- Text input-->
						<div id="memberPhones">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Phone</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Phone" class="form-control memberPhone" pid="0">
								</div>
								<a href="javascript:void(0);" class="text-success col-sm-1 control-label" id="addPhoneField" >
									<i class="glyphicon glyphicon-plus"></i>
								</a>
							</div>
							<?php 
							if ($this->data['memberPhones'])
							{
								foreach ($this->data['memberPhones'] as $phone)
								{
								?>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="textinput">Phone</label>
								<div class="col-sm-9">
									<input type="text" placeholder="Phone" class="form-control memberPhone" pid="0" value="<?php echo $phone['phone']; ?>">
								</div>
							</div>
								<?php
								}
							}
							?>
						</div>
					</fieldset>
					
					<fieldset>
						<!-- Form Name -->
						<legend>Notes</legend>
						<!-- Textarea input-->
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-2">
								<textarea rows="6" cols="" class="form-control" placeholder="notes" id="notes"><?php echo $this->data['memberInfo']['notes']; ?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
							<div class="pull-right">
								<button type="submit" class="btn btn-primary" id="brokerSave">Save</button>
							</div>
							</div>
						</div>
					</fieldset>
				</form>
			</div><!-- /.col-lg-12 -->
		</div><!-- /.row -->

		<div class="row">
			<div class="alert alert-success alert-autocloseable-success">
        		<i class="glyphicon glyphicon-ok"></i> Broker saved
			</div>
		</div>

		<div class="row utilities">
			<div class="col-md-12">
	
				<div class="tabbable-panel">
					<div class="tabbable-line">
						<ul class="nav nav-tabs ">
							<li class="active">
								<a href="#tab_default_1" data-toggle="tab">History </a>
							</li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_default_1">
								<div class="col-sm-12 history-member-panel">
									<div class="row text-right">
										<a href="javascript:void(0);" class="btn btn-info btn-xs display-add-history">add history</a>
									</div>
									
									<div class="row history-member-box">
										<textarea rows="2" cols="" class="form-control" placeholder="history" id="history-entry"></textarea>
										<a href="javascript:void(0);" class="btn btn-info btn-xs" id="add-history">save</a>
									</div>
									
									<div class="row history-content">
										<ul class="history-list">
											<?php
											if ($this->data['memberHistory'])
											{
												foreach ($this->data['memberHistory'] as $history)
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
											?>
                                  		</ul>
									</div>
								</div>
							</div>
							<div class="tab-pane" id="tab_default_2">
								<div class="col-sm-12 task-member-panel">
									<div class="row text-right">
										<a href="javascript:void(0);" class="btn btn-info btn-xs display-add-task">add task</a>
									</div>
									
									<div class="row task-member-box">
										<div class="create-task-box" id="create-task-box">
											<div class="top">
												<?php 
												if ($this->data['userInfo']['type'] == 1)
												{
												?>
												<div class="to">
													<label>To</label>
													<select id="task_to">
													<?php 
													if ($this->data['usersActive'])
													{
														foreach ($this->data['usersActive'] as $user) 
														{
															?>
														<option value="<?php echo $user['user_id']; ?>"><?php echo $user['name']; ?></option>
															<?php
														}
													}
													?>
													</select>
												</div>
												<?php 
												}
												else
												{
													?>
												<input type="hidden" id="task_to" value="<?php echo $this->data['userInfo']['user_id']; ?>">
													<?php
												}
												?>
												
												<div class="date">
													<label>Date</label>
													<input type="text" id="task-date" />
												</div>
												
												<div class="hour">
													<label>Time</label>
													<select id="task_hour">
														<option value="8:00">8:00</option>
														<option value="8:30">8:30</option>
														<option value="9:00">9:00</option>
														<option value="9:30">9:30</option>
														<option value="10:00">10:00</option>
														<option value="10:30">10:30</option>
														<option value="11:00">11:00</option>
														<option value="11:30">11:30</option>
														<option value="12:00">12:00</option>
														<option value="12:30">12:30</option>
														<option value="13:00">13:00</option>
														<option value="13:30">13:30</option>
														<option value="14:00">14:00</option>
														<option value="14:30">14:30</option>
														<option value="15:00">15:00</option>
														<option value="15:30">15:30</option>
														<option value="16:00">16:00</option>
														<option value="15:30">16:30</option>
														<option value="17:00">17:00</option>
														<option value="17:30">17:30</option>
														<option value="18:00">18:00</option>
														<option value="18:30">18:30</option>
														<option value="19:00">19:00</option>
														<option value="19:30">19:30</option>
														<option value="20:00">20:00</option>
														<option value="20:30">20:30</option>
													</select>
												</div>
												<div class="clear"></div>
											</div><!--  /top -->
											<div class="middle">
												<textarea rows="" cols="" id="task_content" class="form-control" placeholder="new task"></textarea>
												<a href="javascript:void(0);" class="btn btn-info btn-xs" id="add-task">save</a>
											</div>
										</div>
									</div>
									
									<div class="row task-content">
										<ul class="task-list">
											<?php
											echo $this->listTasks($this->data['memberTasks']);
											?>
                                  		</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		$membersRecent = ob_get_contents();
		ob_end_clean();
		return $membersRecent;
	}
	
	public function getReservationsHead()
	{
		ob_start();
		?>
		<link rel="stylesheet" href="/css/jquery-ui.css">
		<script src="/js/jquery-ui.js"></script>
		<script src="/js/reservations.js"></script>
		<script>
		$(function() {
			$( "#checkIn, #checkOut" ).datepicker({
				altFormat: "d M, y"
				});
			});
		</script>
		<?php		
	   	$signIn = ob_get_contents();
		ob_end_clean();
		return $signIn;
	}
	
	public function getReservations()
	{
		ob_start();
		?>
	   	   	<?php echo $this->getReservationPanel(); ?>
   	   	<?php
   	   	$tasks = ob_get_contents();
   	   	ob_end_clean();
   	   	return $tasks; 
   	}
	
   	public function getMemberReservationItem($data)
   	{
   		ob_start();
   		?>
   		<div class="col-sm-12 bg-success reservation-item">
   			<div class="row bg-primary title">
   				<div class="col-sm-2">date</div>
   				<div class="col-sm-2">room</div>
   				<div class="col-sm-2">check-in</div>
   				<div class="col-sm-2">check-out</div>
   				<div class="col-sm-2">price</div>
   				<div class="col-sm-2">status</div>
   			</div>
   				
   			<div class="row info">
   				<div class="col-sm-2"><?php echo Tools::formatMYSQLToFront($data['date']); ?></div>
   				<div class="col-sm-2"><?php echo $data['room']; ?></div>
   				<div class="col-sm-2"><?php echo Tools::formatMYSQLToFront($data['check_in']); ?></div>
   				<div class="col-sm-2"><?php echo Tools::formatMYSQLToFront($data['check_out']); ?></div>
   				<div class="col-sm-2"><strong>$<?php echo $data['price']; ?></strong></div>
   				<div class="col-sm-2">paid</div>
   			</div>
   				
   			<div class="row extra">
   				<div class="col-sm-4">Room Type: <?php echo $data['room_type']; ?></div>
   				<div class="col-sm-4">Adults: <?php echo $data['adults']; ?></div>
   				<div class="col-sm-4">Children: <?php echo $data['children']; ?></div>
   			</div>
   		</div>
   		<?php
   		$item = ob_get_contents();
   		ob_end_clean();
   		return $item;
   	}
   	
   	public function getMemberReservations()
   	{
   		ob_start();
   		?>
   		<div class="row">
   			<?php echo $this->getReservationPanel(); ?>
   		</div>
   		
   		<div class="row memberReservations" id="memberReservations">
   			<?php
   			if ($this->data['memberReservations'])
   				foreach ($this->data['memberReservations'] as $reservation)
   				{
   					echo $this->getMemberReservationItem($reservation);
   				}
   			?>
   		</div>
   		<?php
   		$memberReservation = ob_get_contents();
   		ob_end_clean();
   		return $memberReservation;
   	}
   	
   	public function getRoomsHead()
   	{
   		ob_start();
   		?>
   			<link rel="stylesheet" href="/css/jquery-ui.css">
   			<!-- CSS file -->
			<link type="text/css" rel="stylesheet" href="/js/qtip/jquery.qtip.css" />
			<!-- Include either the minifed or production version, NOT both!! -->
			<script type="text/javascript" src="/js/qtip/jquery.qtip.js"></script>
			<!-- Optional: imagesLoaded script to better support images inside your tooltips -->
			<script type="text/javascript" src="/js/qtip/jquery.imagesloaded.pkg.min.js"></script>
			<script>
			// Grab all elements with the class "hasTooltip"
			$(document).ready(function() {
			$('.hasTooltip').each(function() { // Notice the .each() loop, discussed below
			    $(this).qtip({
			        content: {
			            text: $(this).next('div') // Use the "div" element next to this for the content
			        },
			        hide: {
						fixed: true,
						delay: 300
					}
			    });
			});
			});
			</script>
   		<?php		
   		$roomsHead = ob_get_contents();
   		ob_end_clean();
   		return $roomsHead;
   	}
   		
   	public function getRooms()
   	{
   		ob_start();
   		$curMonth = date('M Y');
		?>
		<!-- <pre><?php echo print_r($this->data['rooms']);?></pre> -->
   			<div class="row col-sm-12 rooms-calendar">
   				<div class="col-sm-2">
   					<div class="select-month">
   						<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
   							<option value="/rooms/">Select a month</option>
   							<?php 
   							for ($i = 0; $i <= 12; $i ++)
   							{
   								$interval = '+'.$i.' month';
   								?>
   								<option value="/rooms/from/<?php echo date('Y-m-d', strtotime($interval, strtotime($curMonth))); ?>/">
   									<?php echo date('M Y', strtotime($interval, strtotime($curMonth))); ?>
   								</option>
   								<?php 
   							}
   							?>
   						</select>
   					</div>
   					<div class="empty-row row"></div>
   					<div class="room-row-box">
   						<?php 
   						foreach ($this->data['rooms'] as $room)
   						{
   							?>
   						<div>
   							<p><?php echo $room['room'].' - '.$room['abbr']; ?></p>
   						</div>
   							<?php
   							
   						}
   						
   						if (!$_GET['from'])
   						{
   							$from = date('Y-m-d', strtotime(' -1 day'));
   							$day['prev'] = date('Y-m-d', strtotime(' -7 day', strtotime($from)));
   							$day['next'] = date('Y-m-d', strtotime(' +7 day', strtotime($from)));
   						}
   						else 
   						{
   							$from = date('Y-m-d', strtotime($_GET['from']));
   							$day['prev'] = date('Y-m-d', strtotime(' -7 day', strtotime($_GET['from'])));
   							$day['next'] = date('Y-m-d', strtotime(' +7 day', strtotime($_GET['from'])));
   						}
   							
   						$day[1]['full'] 	= date('Y-m-d', strtotime($from));
   						$day[1]['dayName'] 	= date('l', strtotime($from));
   						$day[1]['day'] 		= date('M d', strtotime($from));
   						
   						$day[2]['full'] 	= date('Y-m-d', strtotime(' +1 day', strtotime($from)));
   						$day[2]['dayName'] 	= date('l', strtotime(' +1 day', strtotime($from)));
   						$day[2]['day'] 		= date('M d', strtotime(' +1 day', strtotime($from)));
   						
   						$day[3]['full'] 	= date('Y-m-d', strtotime(' +2 day', strtotime($from)));
   						$day[3]['dayName'] 	= date('l', strtotime(' +2 day', strtotime($from)));
   						$day[3]['day'] 		= date('M d', strtotime(' +2 day', strtotime($from)));
   						
   						$day[4]['full'] 	= date('Y-m-d', strtotime(' +3 day', strtotime($from)));
   						$day[4]['dayName'] 	= date('l', strtotime(' +3 day', strtotime($from)));
   						$day[4]['day'] 		= date('M d', strtotime(' +3 day', strtotime($from)));
   						
   						$day[5]['full'] 	= date('Y-m-d', strtotime(' +4 day', strtotime($from)));
   						$day[5]['dayName'] 	= date('l', strtotime(' +4 day', strtotime($from)));
   						$day[5]['day'] 		= date('M d', strtotime(' +4 day', strtotime($from)));
   						
   						$day[6]['full'] 	= date('Y-m-d', strtotime(' +5 day', strtotime($from)));
   						$day[6]['dayName'] 	= date('l', strtotime(' +5 day', strtotime($from)));
   						$day[6]['day'] 		= date('M d', strtotime(' +5 day', strtotime($from)));
   						
   						$day[7]['full'] 	= date('Y-m-d', strtotime(' +6 day', strtotime($from)));
   						$day[7]['dayName'] 	= date('l', strtotime(' +6 day', strtotime($from)));
   						$day[7]['day'] 		= date('M d', strtotime(' +6 day', strtotime($from)));
   						
   						?>
   					</div>
   				</div>
   				<div class="col-sm-10">
   					<div class="row">
   						<div class="row status-bar ">
   							<div class="row col-sm-9"></div>
   							<div class="row col-sm-3">
   								<a href="/rooms/from/<?php echo $day['prev']; ?>/">&laquo; Previus</a>
   								<a href="/rooms/">Today</a>
   								<a href="/rooms/from/<?php echo $day['next']; ?>/">Next &raquo;</a>
   							</div>
   						</div>
   						<div class="row">
   							<div class="days-box">
   								<div class="row-week-day-header">
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['1']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['1']['day']; ?></p>
   									</div>
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['2']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['2']['day']; ?></p>
   									</div>
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['3']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['3']['day']; ?></p>
   									</div>
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['4']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['4']['day']; ?></p>
   									</div>
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['5']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['5']['day']; ?></p>
   									</div>
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['6']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['6']['day']; ?></p>
   									</div>
   									<div class="week-day">
   										<p class="text-center"><small><?php echo $day['7']['dayName']; ?></small></p> 
   										<p class="text-center"><?php echo $day['7']['day']; ?></p>
   									</div>
   								</div>
   								<div>
   								<!-- <pre><?php  print_r($this->data['rooms']);;?></pre> -->
   								<?php
   								
   								
   								foreach ($this->data['rooms'] as $room)
   								{
   								?>
   								
   									<div class="row-week-day">
   									<?php 
   									for ($i = 1; $i <= 7; $i++)
   									{
   										?>
   									
	   									<div class="week-day full">
	   									<?php 
	   									switch ($i)
	   									{
	   										case 1:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['1']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
	   										break;
	   										
	   										case 2:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['2']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
	   										break;
	   										
	   										case 3:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['3']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
   											break;
	   										
	   										case 4:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['4']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
   											break;
	   											
   											case 5:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['5']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
   											break;
	   											
   											case 6:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['6']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
   											break;
   											
   											case 7:
	   											if ($room['0']['reservations'])
	   											{
	   												foreach ($room['0']['reservations'] as $reservation)
	   												{
	   													if (Tools::check_in_range($reservation['check_in'], $reservation['check_out'], $day['7']['full']))
	   													{	?>
	   														<span class="hasTooltip"></span>
	   														<div class="tooltipi"> 
															    <a href="/<?php echo $reservation['member_id'].'/member/'; ?>">
															    	<strong><?php echo $reservation['name'].' '.$reservation['last_name'];?></strong>
															    </a>
															    <p>from <?php echo date('M d', strtotime($reservation['check_in'])).' to '.date('M d', strtotime($reservation['check_out']));?></p>
															    <p><?php echo $reservation['room_type'].' '.$reservation['room']; ?></p>
															</div>
	   														<?php
	   													}
	   												}
	   											}
   											break;
	   									}
	   									?>
	   									</div>
   										<?php
   									}
   									?>
   									</div>
   								<?php 
   								}
   								?>
   								</div>
   							</div>
   						</div>
   					</div>
   				</div>
   			</div>
   	   	<?php
   	   	$rooms = ob_get_contents();
   	   	ob_end_clean();
   	   	return $rooms; 
   	}
   	   	
	public function getAgenciesHead()
	{
		ob_start();
		?>
		<script src="/js/agencies.js"></script>
   		<?php		
		$agenciesHead = ob_get_contents();
		ob_end_clean();
		return $agenciesHead;
	}
   		
	public function getAgencies()
	{
		ob_start();
		?>
		<div class="row agencyForm">
			<div class="col-sm-3">
				<input type="text" class="" placeholder="agency" id="agency">
			</div>
			
			<div class="col-sm-2">
				<a href="javascript:void(0);" class="btn btn-info btn-xs" id="addAgency">add</a>
			</div>
		</div>
				
		<div class="table-responsive">
   		   	<table class="table table-striped">
   				<thead>
   					<tr>
   						<th>Agency</th>
   						<th></th>
   					</tr>
   				</thead>
   				<tbody id="agenciesList">
   					<?php echo Layout_View::listAgencies($this->data['agencies']); ?>
   				</tbody>
   			</table>
		</div>
		<?php
		$agencies = ob_get_contents();
		ob_end_clean();
		return $agencies; 
	}
	
	public static function listAgencies($agencies)
	{
		ob_start();
		if ($agencies)
		{
			foreach ($agencies as $agency)
			{
				?>
				<tr>
					<td>
	   					<?php echo $agency['agency']; ?>
	   				</td>
					<td>
	   					<a href="/<?php echo $member['member_id']; ?>/<?php echo Tools::slugify($member['name'].' '.$member['last_name']); ?>/">
	   						<i class="glyphicon glyphicon-remove"></i>
	   					</a>
	   				</td>
   				</tr>
				<?php
			}	
		}
		
		$agencies = ob_get_contents();
		ob_end_clean();
		return $agencies;
	}
   	
    public function getFooter()
    {
    	ob_start();
    	?>
		<footer>
			<nav class="row navbar col-lg-8">
				<ul class='nav navbar-nav main-menu'>
					<li><a href="../contact-us/">Contact</a></li>
					<li><a href="#">API &amp; Hacks</a></li>
					<li><a href="#">FAQ</a></li>
					<li><a href="#">Privacy Policy</a></li>
					<li><a href="#">Terms of Service</a></li>
				</ul>
			</nav>
			<div class="row col-lg-4">
				<p>Copyright &copy; <?php echo date('Y'); ?> <?php echo $this->data['appInfo']['siteName']; ?>. All rights reserved.</p>
			<div>
		</footer>
    	<?php
    	$footer = ob_get_contents();
    	ob_end_clean();
    	return $footer;
	}
}