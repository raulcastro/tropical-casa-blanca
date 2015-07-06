<?php
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root.'/Framework/Back_Default_Header.php';

class Email_Model
{
    private $db; 
	
	public function __construct()
	{
		$this->db = new Mysqli_Tool(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	}
	
	/*  getEmailAddresses
	*   gets the email address asigned to a system user
	*
	*   @return Array   array with the emails, could be empty or with nodes
    */
	public function getEmailAddresses()
	{
	    try
	    {
	        $query = 'SELECT se.email_id, se.email 
	        		FROM system_emails se
	        		LEFT JOIN users_emails ue ON ue.email_id = se.email_id
	                WHERE ue.user_id = '.$_SESSION['userId'].' 
	                AND se.active  = 1';
	                
	        return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getEmailAccount()
	{
	    try
	    {
	        $query = 'SELECT se.email 
	        		FROM system_emails se
	        		LEFT JOIN users_emails ue ON ue.email_id = se.email_id
	        		WHERE ue.user_id = '.$_SESSION['userId'].' 
	        		AND se.active = 1
	        		LIMIT 1';
	        
	        return $this->db->getValue($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getEmailAttachments($message_system_id)
	{
	    try
	    {
	        $query = 'SELECT * FROM attachments 
	                WHERE message_system_id = "'.$message_system_id.'"';
	                
	        return $this->db->getArray($query);
	    }    
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getEmailAttachmentsFileName($message_system_id)
	{
	    try
	    {
	        $query = 'SELECT attachment_name FROM attachments 
	                WHERE message_system_id = "'.$message_system_id.'"';
	                
	        return $this->db->getArray($query);
	    }    
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function zipFilesAndDownload($message_system_id, $archive_file_name, $file_path)
	{
		$zip = new ZipArchive();
		
		if (($zip->open($file_path.$archive_file_name, ZipArchive::CREATE )) !== TRUE)
		{
			echo 'No pudo open el .zip esta mugre.';
			return false;
		}
		
		$file_names = $this->getEmailAttachmentsFileName($message_system_id);
		
		if($file_names)
		{
			foreach($file_names as $files)
			{
				$fileString = $files['attachment_name'];
				if (!file_exists($file_path.$fileString)) { die($file_path.$fileString.' does not exist'); }
				if (!is_readable($file_path.$fileString)) { die($file_path.$fileString.' not readable'); }
				if (!is_writable($file_path)) { die($file_path.' not writable'); }
				$zip->addFile($file_path.$fileString,$fileString);
			}
		}
		
		$zip->close();
		
		$readFile	= $file_path;
		$readFile	.= $archive_file_name;
		
		return	$readFile;
	}
	
	
	
	public function updateEmailTemplate($email_id, $content, $subject)
	{
		if($email_id && $content)
		{
			try
			{
				
				$query = 'UPDATE template_emails
							SET content_en = ?,
							subject = ?
							WHERE template_id = ?';
	                
				$prepared = $this->db->prepare($query);
				
				$prepared->bind_param('ssi',
						stripslashes($content),
						stripslashes($subject),
						$email_id
						);

				return $prepared->execute();
			}
			catch (Exception $e)
			{
			    return false;
			}
		}else
		{
			return false;
		}
		
	}
	
	public function markAsArchive($message_id)
	{
	    $message_id = (int)$message_id;
	    try
	    {
	        $query = 'UPDATE email_messages
	                SET folder = 5
	                WHERE message_id = '.$message_id;
	                
            return $this->db->run($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	
	/*
	 *
	* date_from, date_to: date range
	* from: email sender
	* to: email recipient
	* member: member id
	* transfer: transfer id
	* folder: can be 'inbox', 'outbox' or 'archived', otherwise can be an array with a combination of them
	* left: left limit
	* right: right limit
	* archived: true (tasks connected to an archived task), false (tasks connected to a transfer task)
	*
	*/
	public function getMessages($data)
	{
		$date_filter 		= '';
		$folder_filter		= '';
		$member_filter		= '';
		$transfer_filter	= '';
		$from_filter		= '';
		$to_filter			= '';
		$completed_filter 	= '';
		$limits				= '';
		
		if(array_key_exists('date_from', $data) && array_key_exists('date_to', $data))
		{
			$date_filter = ' AND em.date BETWEEN "'.$data['date_from'].'" AND "'.$data['date_from'].'"';
		}
		
		if(array_key_exists('from', $data))
		{
			$from_filter = ' AND em.from_email = "'.$data['from'].'"';
		}
		
		if(array_key_exists('to', $data))
		{
			$to_filter = ' AND em.to_email = "'.$data['to'].'"';
		}
		
		if(array_key_exists('member', $data))
		{
			$member_filter = ' AND e.member_id = '.$data['member'];
		}
		
		if(array_key_exists('transfer', $data))
		{
			$transfer_filter = ' AND em.transfer_id = '.$data['transfer'];
		}
		
		if(array_key_exists('folder', $data))
		{
			if(is_array($data['folder']))
			{
				$folders = $data['folder'];
				$folders_count = count($folders);
				foreach($folders as $i => $folder)
				{
					if($i == 0)
						$folder_filter = ' AND (';
						
					switch ($folder)
					{
						case 'inbox':
							$folder_filter .= ' folder = 1';
							break;
						case 'outbox':
							$folder_filter .= ' folder = 2';
							break;
						case 'archived':
							$folder_filter .= ' folder = 5';
							break;
					}
					
					if($i == ($folders_count-1))
					{
						$folder_filter .= ')';
					}
					else //if($i < $folder_count)
					{
						$folder_filter .= ' OR ';
					}
					
				}
			}
			else
			{
				switch ($data['folder'])
				{
					case 'inbox':
						$folder_filter = ' AND em.folder = 1';
						break;
					case 'outbox':
						$folder_filter = ' AND em.folder = 2';
						break;
					case 'completed':
						$folder_filter = ' AND em.folder = 5';
						break;
				}
			}
		}
		
		if(array_key_exists('archived', $data))
		{
			if($data['archived'] == false)
			{
				$completed_filter = ' AND (t.verified IS NULL OR t.verified=0)';
			}
			else
			{
				$completed_filter = ' AND t.verified=1 AND em.date >= t.verified_date';
			}
		}
		//LEFT JOIN emails e ON e.email = em.from_email
		//LEFT JOIN emails e ON e.member_id = em.member_id
		
		try
		{
			//LEFT JOIN emails e ON e.email = em.from_email
			$query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.from_email, em.inbox,
                    em.personal_name, em.subject, em.attachment, em.status,
                    e.member_id, t.transfer_id_formed, em.user_sender
                    FROM email_messages em
                    LEFT JOIN emails e ON e.email = em.from_email || e.member_id = em.member_id
                    LEFT JOIN transfers t ON e.member_id = t.member_id
					WHERE 1=1 '
					.$folder_filter
					.$account_filter
					.$member_filter
					.$transfer_filter
					.$from_filter
					.$to_filter
					.$date_filter
					.$completed_filter					
					.' GROUP BY em.message_id';

			$res = $this->db->getArray($query);

			$resTotal = count($res);
			
			if(array_key_exists('left', $data) && array_key_exists('right', $data))
			{
				$limits = ' ORDER BY em.date DESC, em.hour DESC LIMIT '.$data['left'].','.$data['right'];
			}
			else if(array_key_exists('limit', $data))
			{
				$limits = ' ORDER BY em.date DESC, em.hour DESC LIMIT '.$data['limit'];
			}
			else
			{
				$limits = ' ORDER BY em.date DESC, em.hour DESC';
			}
			
			$query .= $limits;
			
			$res = $this->db->getArray($query);
			
			$result = array();
			$emailInSys = '';
			$queryInSys = '';
			$emailInSys = '';
			$memberInSys = '';
			foreach($res as $rr){
				
				$query = 'SELECT * from emails WHERE email = "'.$rr['from_email'].'"';
				
				$queryInSys = $this->db->getArray($query);
				
				if($queryInSys){
					$emailInSys = 1;
					$memberInSys = $queryInSys[0]['member_id'];
				}else{
					$emailInSys = 0;
					$memberInSys = '';
				}
				
				$rr += array('emailInSys'=>$emailInSys, 'memberInSys'=>$memberInSys);
				$result [] = $rr;
			}
			
			$taskData = array(
					'total' => $resTotal,
					'data' => $result
			);
			
			return $taskData;
		}
		catch (Excepcion $e)
		{
			echo $e->getMessage();
			return false;
		}
	}
	
	/*
	public function getEmailRange($left, $right, $account)
    {
        try
        {
			$query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.from_email, em.inbox,
                    em.personal_name, em.subject, em.attachment, em.status,
                    e.member_id, t.transfer_id_formed, em.user_sender
                    FROM email_messages em
                    LEFT JOIN emails e ON e.email = em.from_email
                    LEFT JOIN transfers t ON e.member_id = t.member_id
                    WHERE em.to_email = "'.$account.'" 
                    AND folder = 1
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					LIMIT '.$left.', '.$right.'
					';

            return $this->db->getArray($query);
        }
        catch (Exception $e)
        {
            return false;
        }
    }
	
	public function getOutboxRange($left, $right, $account)
    {
        try
        {
			$query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.to_email, em.inbox,
                    n.first_name, n.last_name, em.subject, em.attachment, 
                    em.status, e.member_id, t.transfer_id_formed
                    FROM email_messages em
                    LEFT JOIN emails e ON e.member_id = em.member_id
                    LEFT JOIN transfers t ON e.member_id = t.member_id
                    LEFT JOIN names n ON n.member_id = em.member_id
                    WHERE em.from_email = "'.$account.'" 
                    AND folder = 2
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
                    LIMIT '.$left.', '.$right.'
					';

            return $this->db->getArray($query);
        }
        catch (Exception $e)
        {
            return false;
        }
    }
	
	
	public function getArchivedByAccount($account)
	{
	    try
	    {
	        $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.from_email, em.inbox,
                    em.personal_name, em.subject, em.attachment, em.status,
                    e.member_id, t.transfer_id_formed
                    FROM email_messages em
                    LEFT JOIN emails e ON e.email = em.from_email
                    LEFT JOIN transfers t ON e.member_id = t.member_id
                    WHERE em.to_email = "'.$account.'" 
                    AND folder = 5
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					';
			
			return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getArchivedByAccountRange($left = '', $right = '', $account)
	{
	    try
	    {
	        $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.from_email, em.inbox,
                    em.personal_name, em.subject, em.attachment, em.status,
                    e.member_id, t.transfer_id_formed
                    FROM email_messages em
                    LEFT JOIN emails e ON e.email = em.from_email
                    LEFT JOIN transfers t ON e.member_id = t.member_id
                    WHERE em.to_email = "'.$account.'" 
                    AND folder = 5
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					LIMIT '.$left.', '.$right.' ;';
			
			return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getOutboxByAccount($account)
	{
	    try
	    {
	        $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.to_email, em.inbox,
                    n.first_name, n.last_name, em.subject, em.attachment, 
                    em.status, e.member_id, t.transfer_id_formed
                    FROM email_messages em
                    LEFT JOIN emails e ON e.member_id = em.member_id
                    LEFT JOIN transfers t ON e.member_id = t.member_id
                    LEFT JOIN names n ON n.member_id = em.member_id
                    WHERE em.from_email = "'.$account.'" 
                    AND folder = 2
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					';
            
            return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getOutboxByAccountRange($account)
	{
	    try
	    {
	        $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.hour, em.to_email, em.inbox,
                    n.first_name, n.last_name, em.subject, em.attachment, 
                    em.status, e.member_id, t.transfer_id_formed
                    FROM email_messages em
                    LEFT JOIN emails e ON e.member_id = em.member_id
                    LEFT JOIN transfers t ON e.member_id = t.member_id
                    LEFT JOIN names n ON n.member_id = em.member_id
                    WHERE em.from_email = "'.$account.'" 
                    AND folder = 2
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
                    LIMIT 40';
            
            return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}

	public function getInboxByMemberId($member_id)
	{
	    $member_id = (int) $member_id;
	    try
	    {
            $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.subject, em.from_email, em.to_email,
                    em.hour, em.inbox, e.member_id,
                    em.personal_name, em.attachment, em.status
                    FROM email_messages em
                    LEFT JOIN emails e ON em.from_email = e.email
                    WHERE e.member_id = '.$member_id.'
                    AND folder = 1
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					LIMIT 20';
					
            return $this->db->getArray($query);	    
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}

	public function getOutboxByMemberId($member_id)
	{
	    $member_id = (int) $member_id;
	    try
	    {
            $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.subject, em.from_email, em.to_email,
                    em.hour, em.inbox, em.member_id,
                    em.personal_name, em.attachment, em.status
                    FROM email_messages em
                    LEFT JOIN emails e ON em.member_id = e.member_id
                    WHERE em.member_id = '.$member_id.'
                    AND folder = 2
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					LIMIT 20';

            return $this->db->getArray($query);
        	    
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}

	public function getArchiveByMemberId($member_id)
	{
	    $member_id = (int) $member_id;
	    try
	    {
            $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.subject, em.from_email, em.to_email,
                    em.hour, em.inbox, e.member_id,
                    em.personal_name, em.attachment, em.status
                    FROM email_messages em
                    LEFT JOIN emails e ON (em.from_email=e.email OR em.member_id = e.member_id)
                    WHERE e.member_id = '.$member_id.'
                    AND folder = 5
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC
					LIMIT 20;';
					
			return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getArchiveByMemberIdRange($left, $right, $member_id)
	{
	    $member_id = (int) $member_id;
	    try
	    {
			
            $query = 'SELECT em.message_id, 
                    DATE_FORMAT(em.date, "%b %e") AS date, 
                    em.subject, em.from_email, em.to_email,
                    em.hour, em.inbox, e.member_id,
                    em.personal_name, em.attachment, em.status
                    FROM email_messages em
                    LEFT JOIN emails e ON (em.from_email=e.email OR em.member_id = e.member_id)
                    WHERE e.member_id = '.$member_id.'
                    AND folder = 5
                    GROUP BY em.message_id
                    ORDER BY em.date DESC, em.hour DESC 
					LIMIT '.$left.', '.$right.' ;';
					
			return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	*/
    
    
    public function getFrom($company_id)
    {
        $from 	= '';
        $query 	= "SELECT email_sender 
        		FROM companies 
        		WHERE company_id=".$company_id;
        
        return $this->db->getValue($query);
    }

	public function saveOutbox($data, $nAttachments = 0)
	{
	    $message_system_id  = $data['email_key'];
	    $company            = $data['company'];
	    $from               = $this->getFrom($company);
	    $member_id          = $data['member_id'];
	    $userSender			= $data['userSender'];
	    
	    try
	    {
	        $query = 'INSERT INTO email_messages(message_system_id, date, hour, 
	                from_email, to_email, subject, message, folder, attachment, 
	                member_id, user_sender)
	                VALUES(?, CURDATE(), CURTIME(), ?, ?, ?, ?, 2, ?, ?, ?)';
	                
	        $prepared = $this->db->prepare($query);
	        
	        $prepared->bind_param('sssssiis',
                    $message_system_id,
                    $from,
                    $data['to'],
                    $data['subject'],
                    $data['content'],
                    $nAttachments,
                    $member_id,
                    $userSender);
	                
	        if (!$prepared->execute())
            {
                echo $prepared->error;
            }
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function saveOutboxFromTemplate($data, $to, $message_system_id, $transfer_id, 
	        $company, $attachments = 0, $attachmentsName = 0)
	{
	
		$nAttachments =sizeof($attachments);
	
	    $from   = $this->getFrom($company);
	    $name	= $this->getCurrentUser();
	    
	    $user_id = 'NULL';
	    
	    if ($_SESSION['userId'])
	    {
	    	$user_id = $_SESSION['userId'];
	    }

	    $member_id = $this->getMemberIdByTransferId($transfer_id);
	    
	    try
	    {
	        $query = 'INSERT INTO email_messages(message_system_id, date, hour, 
	                from_email, to_email, subject, message, folder, attachment, 
	                member_id, user_sender, transfer_id, template_id, user_id)
	                VALUES(?, CURDATE(), CURTIME(), ?, ?, ?, ?, 2, ?, ?, ?, ?, ?, ?)';
	                
	        $prepared = $this->db->prepare($query);
	                                                                                                                                                                                                                                                       
	        $prepared->bind_param('sssssiisiii',
                    $message_system_id,
                    $from,
                    $to,
                    $data['subject'],
                    $data['content_en'],
                    $nAttachments,
                    $member_id,
                    $name,
                    $transfer_id,
                    $data['template_id'],
                    $user_id);
	                
	        if (!$prepared->execute())
            {
                echo $prepared->error;
            }
            else
            {
		if($attachmentsName > 0)
		$attSending = $attachmentsName;
		else
		$attSending = $attachments;
		
            	for($i = 0; $i < count($attachments); ++$i)
				{
					$this->saveAttachments($message_system_id, $data['company'], $attSending[$i]);
				}
				
				return true;
            }
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function saveEmailSent($template, $to, $message_system_id, $transfer_id, $cronstep){
		
		try
		{
			$query = 'INSERT INTO email_sent VALUES (null, '.$template['template_id'].', "'.$to.'", "'.$message_system_id.'", '.$transfer_id.', '.$cronstep.');';
					
			$result = $this->db->run($query);
			
			return $result;
		}
		catch (Exception $e)
		{
			return false;
		}
		
	}
	
	public function emailSentCheck($template, $to, $transfer_id, $cronstep){
		
		try
		{
			if($cronstep != 0){
				$query = 'SELECT count(*)
				FROM email_sent
				WHERE template_id='.$template['template_id'].'
				AND to_email="'.$to.'"
				AND transfer_id='.$transfer_id.'
				AND step ='.$cronstep;	
			}else{
				return 0;
			}
						
			$result = $this->db->getValue($query);
			
			return $result;
		}
		catch (Exception $e)
		{
			return false;
		}
		
	}
	
	public function getItemsEmails($data)
	{
		try
		{
			$query = 'SELECT * FROM template_emails
					WHERE company = '.$data['company_id'].'
					AND step_template_id = '.$data['step_id'].'
					AND sub_step_template_id = '.$data['sub_step_id'];
					
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getEmailTemplate($template_id)
	{
	    try
	    {
	        $template_id = (int) $template_id;
	        
	        $query = 'SELECT * FROM template_emails 
	                WHERE template_id = '.$template_id;       
	        return $this->db->getRow($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getEmailAddressByMemberId($member_id)
	{
		try
		{
		    $member_id = (int)$member_id;
		    
		    $query = 'SELECT email FROM emails WHERE member_id = '.$member_id.'
		            ORDER BY email_id ASC;';
		    
		    $result = $this->db->getArray($query);
		    
		    $emails = '';
		    
		    foreach ($result as $a)
		    {
		        $emails .= $a['email'].',';
		    }
		    
		    $emails = trim($emails);
		    $emails = substr_replace($emails ,"",-1);
		    
		    return $emails;
		}   
		catch (Excepcion $e)
		{
		    return false;
		}
	}
	
	public function getEmailTemplateAttachments($email_id)
	{
	    try
	    {
	        $email_id = (int)$email_id;
	        
	        $query = 'SELECT * 
	                FROM templates_attachments 
	                WHERE email_id = '.$email_id;
	                
	       return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getEmailTemplateWelcome($email_id)
	{
	    try
	    {
	        $email_id = (int)$email_id;
	        
	        $query = 'SELECT * FROM email_templates
	                WHERE email_id = '.$email_id;
	                
	        return $this->db->getRow($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getTitilesByCompany($company)
	{
	    try
	    {
	        $company = (int)$company;
	        
	        $query = 'SELECT email_id, title 
	                FROM email_templates 
	                WHERE company = '.$company;
	                
	       return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getLastCacheAttachment($member_id, $transfer_id)
	{
	    try
	    {
	        $query = 'SELECT * FROM attachments_cache 
	                WHERE member_id = '.$member_id.' 
	                AND transfer_id = '.$transfer_id.'
	                ORDER BY attachment_id DESC
	                LIMIT 1
	                ';
	                
	        return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getLastCacheAttachmentCover($email_key)
	{
	    try
	    {
	        $query = 'SELECT * FROM attachments_cache 
	                WHERE email_key = "'.$email_key.'"
	                ORDER BY attachment_id DESC
	                LIMIT 1
	                ';
	                
	        return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}
	
	public function getCachedAttachments($email_key)
	{
	    try
	    {
	        $query = 'SELECT * FROM attachments_cache 
	                WHERE email_key = "'.$email_key.'"';
	        
	        return $this->db->getArray($query);
	    }
	    catch (Excepcion $e)
	    {
	        return false;
	    }
	}

	public function moveCacheToAttachment($files)
	{
		$branch = $_SESSION['branch'];
		$root 	= $_SERVER['DOCUMENT_ROOT'];
		$to 	= $_POST['to'];
		$key 	= $_POST['email_key'];
	
		if ($files)
        {
            foreach ($files as $a)
            {
            	$attachments_location	= dirname(__FILE__).'/../attachments/'.$branch.'/'.$to.'/'.$key;		
		
				// create directory if it doesn't exist
				if(!file_exists($attachments_location))
				{
					mkdir($attachments_location, 0777, true);
					chmod($attachments_location, 0777);
				}
		
				chmod($attachments_location, 0777);
            
                $file       = $root.'/attachments_cache/'.$branch.'/'.$a['attachment'];
                $newFile    = $attachments_location.'/'.$a['attachment'];

               	if (!copy($file, $newFile))
               	{
               		echo 'error copying';
               	}

                if (file_exists($newFile))
                {
                    unlink($file);
                }

                $this->saveAttachments($_POST['email_key'], $_POST['company'], 
                        $a['attachment']);
            }
        }
	}

	public function saveAttachments($email_key, $company, $name)
    {
	    $from = $this->getFrom($company);
	    
        try
        {
            $query = 'INSERT INTO attachments(attachment_id, message_system_id, email, attachment_name)
                    VALUES(null, "'.$email_key.'", "'.$from.'", "'.$name.'" )';
                    
            $this->db->run($query);
        }
        catch (Excepcion $e)
        {
            return false;
        }
    }
	public function addTemplate($data)
	{
		try
		{   
			$content = utf8_encode($data['sourceTemplate']);
			
			$query = 'INSERT INTO template_emails(step_template_id,
					sub_step_template_id,
					company,
					name,
					subject,
					content_en)
					VALUES(?, ?, ?, ?, ?, ?)';
			        
	        $prepared = $this->db->prepare($query);
	        
	        $prepared->bind_param('iiisss',
                    $data['choosedStep'],
                    $data['choosedSub'],
                    $data['choosedCompany'],
					$data['subjectTemplate'],
                    $data['subjectTemplate'],
                    $content);
	                
	        if (!$prepared->execute())
            {
                echo $prepared->error;
            }
			else
			{
				return true;
			}
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getTemplateById($template_id)
	{
		try
		{
			$query = "SELECT subject, content_en, content_es FROM template_emails WHERE template_id=".$template_id;
			return $this->db->getRow($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getAllTemplates()
	{
		try
		{
			$query = 'SELECT * FROM template_emails';
			
			return $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getChangeRequest($transfer_id)
	{
		try
		{
			$transfer_id = (int) $transfer_id;
			
			$query = 'SELECT *
					FROM change_request
					WHERE transfer_id = '.$transfer_id;
			
			return $this->db->getRow($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function sendEmailTemplate($template_id, $to)
	{
		$template = $this->getTemplateById($template_id);
		$subject = $template['subject'];
		$message = $template['content_en'];
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		mail($to, $subject, $message, $headers);
	}
	
	public function getAllEmailAccounts()
	{
		try
		{
			$query = 'SELECT * FROM user_emails WHERE active = 1';
	    	return $res = $this->db->getArray($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/*  saveEmailMessages
	 *   Save The inbox messages, it flag folder with 1 for mark as inbox and not
	 *   as outbox
	 *
	 *   @param  string  $message_system_id unique email from the email message
	 *   @param  string  $date   date when the email was sent
	 *   @param  string  $hour   hour when the email was sent
	 *   @param  string  $from_email email address from it comes|
	 *   @param  string  $to_email receiver
	 *   @param  string  $personal_name personal name got it from the message
	 *   @param  string  $subject message subject
	 *   @param  string  $message message body
	 *   @para   string  $attachment no. of attachments
	 *
	 *   @return bool    true on success
	 */
	
	public function saveEmailMessages($message_system_id,
			$date, $hour, $from_email, $to_email, $personal_name,
			$subject, $message, $attachment)
	{
		try
		{
			$message_system_id = str_replace('<', '', $message_system_id);
			$message_system_id = str_replace('>', '', $message_system_id);
				
			$date           = date('Y-m-d', $date);
			$hour           = date('H:i:s', $hour);
				
			mb_internal_encoding('UTF-8');
			$subject = str_replace("_"," ", mb_decode_mimeheader($subject));
	
			$prepared = $this->db->prepare('
                    INSERT INTO
                    email_messages(message_system_id,
                    date,
                    hour,
                    from_email,
                    to_email,
                    personal_name,
                    subject,
                    message,
                    attachment,
                    folder)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
			$prepared->bind_param('ssssssssi',
					$message_system_id,
					$date,
					$hour,
					$from_email,
					$to_email,
					utf8_encode($personal_name),
					utf8_encode($subject),
					utf8_encode($message),
					$attachment);
	
			if ($prepared->execute())
			{
				return true;
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage();
			return false;
		}
	}
	
	public function getOutboxTotalByAccount($email_id)
	{
		try
		{
			$query = 'SELECT outbox FROM system_emails WHERE email = "'.$email_id.'";';
			
			return $this->db->getValue($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getArchivedTotalByAccount($email_id)
	{
		try
		{
			$query = 'SELECT archived FROM system_emails WHERE email = "'.$email_id.'";';
			
			return $this->db->getValue($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function setNumberOfInboxByAccount($email_id, $count)
	{
		try
		{
			$query = 'UPDATE user_emails SET inbox = '.$count.' WHERE email_id = '.$email_id;
			$this->db->run($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function setNumberOfOutboxByAccount($email_id, $count)
	{
		try
		{
			$query = 'UPDATE user_emails SET outbox = '.$count.' WHERE email_id = '.$email_id;
			$this->db->run($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function setNumberOfArchivedByAccount($email_id, $count)
	{
		try
		{
			$query = 'UPDATE user_emails SET archived = '.$count.' WHERE email_id = '.$email_id;
			$this->db->run($query);
		}
		catch (Exception $e)
		{
			return false;
		}
	}
	
	public function getMessagesByAccount($account)
	{
		try
		{
			$query = 'SELECT 	em.message_id,  
			                    DATE_FORMAT(em.date, "%b %e") AS date,
			                    em.hour, em.from_email, em.inbox,
			                    em.personal_name, em.subject, em.attachment, em.status,
								em.user_sender,
								m.member_id,
								CONCAT(m.name, " ", m.last_name) as member_name
			                    FROM email_messages em
			                    LEFT JOIN member_emails me ON me.email = em.`from_email`
			                    LEFT JOIN members m ON m.member_id = me.member_id
								WHERE em.to_email = "'.$account.'"
			                   	AND folder = 1
								GROUP BY em.message_id
								ORDER BY em.date DESC, em.hour DESC';
			return $this->db->getArray($query);
		}
		catch (Excepcion $e)
		{
			return false;
		}
	}
	
	public function getMessageByMessageId($messageId)
	{
		try
		{
			$query = 'SELECT 	em.message_id,
			                    DATE_FORMAT(em.date, "%b %e %Y") AS date,
			                    DATE_FORMAT(em.hour, "%I:%i %p") AS hour, em.from_email, em.inbox,
			                    em.personal_name, em.subject, em.attachment, em.status,
								em.user_sender, em.message,
								m.member_id,
								CONCAT(m.name, " ", m.last_name) as member_name
			                    FROM email_messages em
			                    LEFT JOIN member_emails me ON me.email = em.`from_email`
			                    LEFT JOIN members m ON m.member_id = me.member_id
								WHERE em.message_id = '.$messageId.'
								GROUP BY em.message_id';
			return $this->db->getRow($query);
		}
		catch (Excepcion $e)
		{
			return false;
		}
	}
	
	public function markAsRead($message_id)
	{
		$message_id = (int)$message_id;
		try
		{
			$query = 'UPDATE email_messages
	                SET inbox = 0
	                WHERE message_id = '.$message_id;
			 
			$this->db->run($query);
		}
		catch (Excepcion $e)
		{
			return false;
		}
	}
	
}
