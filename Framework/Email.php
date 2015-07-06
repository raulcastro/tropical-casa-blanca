<?php
/*
 * Created on Oct 29, 2013
 *
 *All these shitty e-mail functions, moi Diu!
 */
 
class Email
{
	private $host; 
    private $port;
    private $mode 		= 'imap';
    private $security;
	private $username; 
	private $password;
	private $stream;
	private $provider;
	private $archive;
	private $dump;
	private $currentMessageId;
	private $fromAddr;
	private $content 	= array();
	private $providers 	= array(
    		'gmail' 	=> '/imap/ssl',
// 			'gmail' 	=> '/pop3/ssl/novalidate-cert',
    		'yahoo' 	=> '/imap/ssl',
    		'aol'		=> '/imap/ssl',
    		'gator'		=> '/novalidate-cert',
    		'none'		=> ''
    	);

    function __construct($host, $port = 25, $security = 'none', $username, 
    		$password, $provider = 'none', $archive, $dump) 
    { 
        $this->host 	= $host;
        $this->port 	= $port;
        $this->security = $security;
        $this->username = $username;
        $this->password = $password;
        $this->provider	= $provider;
        $this->archive	= $archive;
        $this->dump 	= $dump;
    }
    
    function open()
    {
		echo "\nConnecting to $this->host:$this->port as $this->username \n";
		
		$currentProv =  $this->providers[$this->provider];
		
		$ref = '{' . "$this->host:$this->port$currentProv}INBOX";
	
		$this->stream = imap_open ($ref, $this->username, $this->password);
		
		$this->check_error(
				$this->stream, 
				$this->stream !== false, 
				"Unable to connect to $this->host:$this->port as $this->username.", 
				3
			);

		echo "Connection OK\n";
    }
    
    function getMessages()
	{
		$messages = imap_search($this->stream, 'UNSEEN', SE_UID);

		if (is_array($messages))
		{
			$all 			= count($messages);
			$current 		= 0;
			$status 		= '';
			$last_percent	= -1;
	
			$message_id 	= 0;
		
			echo 'Messages found total : '.count($messages)."\n\n";
		
			foreach ($messages as $uid)
			{
				$message_id ++;
				
				echo 'Message: '.$message_id.'/'.$all."\n";
			
				$msg 		= imap_fetchbody($this->stream, $uid, '', FT_UID | FT_PEEK);
			
				$header 	= imap_header($this->stream, $this->getMessageNumber($uid));
			
				$fromInfo 	= $header->from[0];
				$replyInfo 	= $header->reply_to[0];
			
				if ($header->message_id)
				{
					$this->currentMessageId = $header->message_id;
				}
				else
				{
					$this->currentMessageId = mt_rand()."---".$fromInfo->mailbox . "@" . $fromInfo->host;
				}
			
				$this->fromAddr = (isset($fromInfo->mailbox) && isset($fromInfo->host))
						? $fromInfo->mailbox . "@" . $fromInfo->host : "";
			
				$details 	= array(
					"messageId" => $this->getCurrentMessageId(),
				
					"date" 		=> $header->date,
			
					"fromAddr" 	=> (isset($fromInfo->mailbox) && isset($fromInfo->host))
						? $fromInfo->mailbox . "@" . $fromInfo->host : "",
					
					"fromName" 	=> (isset($fromInfo->personal))
						? $fromInfo->personal : "",
					
					"replyAddr"	=> (isset($replyInfo->mailbox) && isset($replyInfo->host))
						? $replyInfo->mailbox . "@" . $replyInfo->host : "",
					
					"replyName" => (isset($replyTo->personal))
						? $replyto->personal : "",
					
					"subject" 	=> (isset($header->subject))
						? $header->subject : "",
					
					"udate" 	=> (isset($header->udate))
						? $header->udate : "",
					
					"body"		=> '',
				
					"totalAttachments" => 0,
				
					"attachmentsDetail" => array()
				);
			
				$details['body'] = $this->getBody($uid);
		        
				echo 'Date: '.$details['date']."\n";
				echo 'Message id: '.$details['messageId']."\n";
				echo 'From: '.$details['fromAddr']."\n";
				echo 'Name: '.$details['fromName']."\n";
				echo 'Reply add: '.$details['replyAddr']."\n";
				echo 'Reply name: '.$details['replyName']."\n";
				echo 'Subject: '.$details['subject']."\n";
				echo 'Body: '.substr($details['body'],0,100)."\n";
				echo "\n\n";
				$attachFlags =  $this->hasAttachments($uid);
				echo "\n\n";
				if ($attachFlags['attachmentsTotal'] > 0)
				{
					$details['totalAttachments'] = $attachFlags['attachmentsTotal'];
					$details['attachmentsDetail'] = $attachFlags;
				}
			
				echo 'Total attachments: '.$details['totalAttachments']."\n\n";
			
				array_push($this->content, $details);
// 				Uncomment this line for archive the messages on the given folder
// 				$this->archiveMessage($uid);
		
			}//end foreach
		
			return $this->content;
	
		}else{
			//fputs(STDERR, "\nError retrieving list of messages in folder: $folder_name. SKIPPED\n" . imap_last_error() . "\n");
			echo "No messages were found \n\n\n";
		}
	}
	
	function getBody($uid) 
	{
		$body = $this->get_part($uid, "TEXT/HTML");
		// if HTML body is empty, try getting text body
		if ($body == "") 
		{
		    $body = $this->get_part($uid, "TEXT/PLAIN");
		}
		
		return $body;
	}
	
	function hasAttachments($uid)
	{
		$attachmentsDetails = array(
			'attachmentsTotal' => 0
		);
		
		$filenamesarray = array();
		
		$structure 		= imap_fetchstructure($this->stream, $uid, FT_UID);
			
		$attachments 	= $this->getAttachments($uid, $structure, "");
		
		if (count($attachments) > 0)
		{
			$attachmentsDetails['attachmentsTotal'] = count($attachments);
			$attachmentsDetails['attachments'] 		= $attachments;
			
			$mboxdir = $this->dump.$this->fromAddr.'/'.$this->getCurrentMessageId().'/';
			$attachmentsDetails['mboxdir'] 		= $mboxdir;
			
			echo $mboxdir." \n";
			
			if(!file_exists($mboxdir))
			{
				mkdir($mboxdir, 0777, true);
				chmod($mboxdir, 0777);
			}
			
			$tmp_id = 0;
			foreach ($attachments as $attachment)
			{
				if(isset($attachment['name']))
				{
					$filename 	= $this->cleanupName($attachment['name']);
					
					$fdata 		= $this->saveAttachment(
							$uid, 
							$attachment["partNum"], 
							$attachment["enc"], 
							$filename
						);
					
					if (!file_exists($mboxdir.$filename)) 
					{
	#					echo $mboxdir.$filename;
					
						file_put_contents($mboxdir.$filename, $fdata);
						chmod($mboxdir.$filename, 0777);
	#					touch($mboxdir.$filename, $message->udate);
					}
					
					if (file_exists($mboxdir.$filename)) 
					{
						echo "  Attachment: ".$filename." Ok! \n";
					}
					
				}
				else
				{
					$attachmentsDetails['attachmentsTotal'] = count($attachments[0]);
			
					$tmp_id += 1;
					foreach($attachment as $a)
					{
						$filename 	= $this->cleanupName($a['name']);
						if($filename =='' && is_array($a)){
							$filenamesarray[] = $tmp_id.'_'.$this->getValueByKey($a, 'name', $filenamesarray);
							$filename = $tmp_id.'_'.$this->getValueByKey($a, 'name', $filenamesarray);
						}else{
							$filename	= $tmp_id.'_'.$filename;
						}
						
						$a['name']	= $filename;
						
						$fdata 		= $this->saveAttachment(
								$uid, 
								$a["partNum"], 
								$a["enc"], 
								$filename
							);
						
						if (!file_exists($mboxdir.$filename)) 
						{
						
							file_put_contents($mboxdir.$filename, $fdata);
							chmod($mboxdir.$filename, 0777);
						}
						
						if (file_exists($mboxdir.$filename)) 
						{
							echo "  Attachment: ".$filename." Ok! \n";
						}
					}
				}
			}
		}
		
		return $attachmentsDetails;
	}
    
    function getAttachments($mailNum, $part, $partNum) 
	{
		$attachments = array();
		
		if (isset($part->parts)) 
		{
			foreach ($part->parts as $key => $subpart) 
			{			
				if ($partNum != "") 
				{
					$newPartNum = $partNum . "." . ($key + 1);
				}
				else 
				{
					$newPartNum = ($key+1);
				}
				
				$result = $this->getAttachments($mailNum, $subpart,
						$newPartNum);
					
				if (count($result) != 0) 
				{
					array_push($attachments, $result);
				}
			}
		}
		else if (isset($part->disposition)) 
		{
			if (strtolower($part->disposition) == "attachment") 
			{
				$partStruct = imap_bodystruct($this->stream, imap_msgno($this->stream, $mailNum), $partNum);
				
				$attachmentDetails = array(
					"name"    => $part->dparameters[0]->value,
					"partNum" => $partNum,
					"enc"     => $partStruct->encoding
				);
				
				return $attachmentDetails;
			}
			
			if (strtolower($part->disposition) == "inline")
			{
				$partStruct = imap_bodystruct($this->stream, imap_msgno($this->stream, $mailNum), $partNum);
					
				$attachmentDetails = array(
					"name"    => $part->dparameters[0]->value,
					"partNum" => $partNum,
					"enc"     => $partStruct->encoding,
					"id"      => $part->id,
				);
					
				return $attachmentDetails;
			}
		}
		else if (isset($part->ifid) && $part->ifid > 0) 
		{
			if (($part->type == 5)) 
			{
				$partStruct = imap_bodystruct($this->stream, imap_msgno($this->stream, $mailNum), $partNum);
				
                if($part->ifparameters == 1){    
                    $attachmentDetails = array(
                        "name"    => $part->parameters[0]->value,
                        "partNum" => $partNum,
                        "enc"     => $partStruct->encoding,
                        "id"      => $part->id,
                    );
                }
                else
                {
                    $attachmentDetails = array(
                    	"name"    => $partNum.'.'.$part->subtype,
                    	"partNum" => $partNum,
                    	"enc"     => $partStruct->encoding,
                    	"id"      => $part->id,
                    );
                }
				
				
				return $attachmentDetails;
			}
		}
		else if ($part->type == 3) 
			{
				$partStruct = imap_bodystruct($this->stream, imap_msgno($this->stream, $mailNum), $partNum);
				
                if($part->ifparameters == 1){    
                    $attachmentDetails = array(
                        "name"    => $part->parameters[0]->value,
                        "partNum" => $partNum,
                        "enc"     => $partStruct->encoding,
                    );
                }
                else
                {
                    $attachmentDetails = array(
                    	"name"    => $partNum.'.'.$part->subtype,
                    	"partNum" => $partNum,
                    	"enc"     => $partStruct->encoding,
                    );
                }
				
				return $attachmentDetails;
			}
		

		return $attachments;
	
	}
	
    
    function get_part($uid, $mimetype, $structure = false, $partNumber = false) 
	{
		if (!$structure) 
		{
			$structure = imap_fetchstructure($this->stream, $uid, FT_UID);
		}
		
		if ($structure) 
		{
		    if ($mimetype == $this->get_mime_type($structure)) 
		    {
		        if (!$partNumber) 
		        {
		            $partNumber = 1;
		        }
		        
		        $text = imap_fetchbody($this->stream, $uid, $partNumber, FT_UID);
		        
		        switch ($structure->encoding) 
		        {
		            case 0: return $text; // 7BIT
					case 1: return $text; // 8BIT
					case 2: return $text; // BINARY
					case 3: return base64_decode($text); // BASE64
					case 4: return quoted_printable_decode($text); // QUOTED_PRINTABLE
					case 5: return $text; // OTHER
		       }
		   }
		    // multipart 
		    if ($structure->type == 1) 
		    {
		        foreach ($structure->parts as $index => $subStruct) 
		        {
		            $prefix = "";
		            if ($partNumber) 
		            {
		                $prefix = $partNumber . ".";
		            }
		            
		            $data = $this->get_part($uid, $mimetype, $subStruct, $prefix . ($index + 1));
		            
		            if ($data) 
		            {
		                return $data;
		            }
		        }
		    }
			
		}
		return false;
	}
	
	function saveAttachment($uid, $partNum, $encoding) 
	{
	    $partStruct = imap_bodystruct($this->stream, $this->getMessageNumber($uid), $partNum);

	    $message 	= imap_fetchbody($this->stream, $uid, $partNum, FT_UID);

	    switch ($encoding) 
	    {
            case 0:
            case 1:
                    $message = imap_8bit($message);
                    break;
            case 2:
                    $message = imap_binary($message);
                    break;
            case 3:
                    $message = imap_base64($message);
                    break;
            case 4:
                    $message = quoted_printable_decode($message);
                    break;
	    }

	    return $message;
	}
    
    function cleanupName($name) 
    {
		$name 			= iconv_mime_decode($name, 0, "ISO-8859-1");
        $name 			= str_replace(array(":", "\\", "/", "<", ">", ":", "\"", "|", "?", "*"), array(""), $name);
		$ch_noAllowed 	= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ");
		$ch_Allowed   	= array("a","e","i","o","u","A","E","I","O","U","n");
		$name 			= utf8_encode($name);
		return str_replace($ch_noAllowed, $ch_Allowed, $name);
    }
    
    function close() 
    {
        if ($this->stream) 
        {
            imap_close($this->stream);
        }
    }

	function listMailboxes() 
	{
		$mailboxes = imap_list($this->stream, '{imap.gmail.com:993/ssl}', "*");
		
		foreach ($mailboxes as &$folder) 
		{
            $folder = str_replace('{imap.gmail.com:993/ssl}', "", imap_utf7_decode($folder));
            echo $folder."\n";
        }
        
        return $mailboxes;
    }

    function countMessages() 
    {
        return imap_num_msg($this->stream);
    }

    function getMessageUid($msgno) 
    {
        return imap_uid($this->stream, $msgno);
    }

    function getMessageNumber($uid) 
    {
        return imap_msgno($this->stream, $uid);
    }
    
    function archiveMessage($uid)
    {
    	switch ($this->provider)
    	{
    		case 'gator':
    			$folder = 'INBOX.'.$this->archive;
    		break;
    		
    		case 'gmail':
    			$folder = $this->archive;
    		break;
    	}
    
		imap_mail_move($this->stream,  $this->getMessageNumber($uid), $folder);

		imap_expunge($this->stream); 
    }
	
    
    function get_mime_type($structure) 
	{
		$primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
	 
		if ($structure->subtype) 
		{
		   return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
		}
		
		return "TEXT/PLAIN";
	}
    
    function check_error($stream, $check_errorion, $message, $code)
	{
		if (!$check_errorion)
		{
			fputs(STDERR, "\n$message\n" . imap_last_error() . "\n");
			
			if ($stream)
				imap_close($stream);
			exit($code);
		}
	}
	
	function getCurrentMessageId()
	{
		$message_system_id = str_replace('<', '', $this->currentMessageId);
		$message_system_id = str_replace('>', '', $message_system_id);

		return $message_system_id;
	}
	
	function getValueByKey($array,$key,$filenamesarray)
	{
		foreach($array as $arr)
		{
			if(is_array($arr) && array_key_exists($key,$arr))
			{
			return $arr[$key];
			}
	
			if(is_array($arr))
			{
				if($return = $this->getValueByKey($arr,$key, $filenamesarray))
				{
					if(!in_array($return, $filenamesarray))
					{
					return $return;
					}
				}
			}
		}
	}

}
?>
