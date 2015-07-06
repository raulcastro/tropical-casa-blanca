<?php
	ini_set("display_errors", 1);

	ini_set('memory_limit', '128M');
	set_time_limit(0);

    require_once(dirname(__FILE__).'/../Framework/Connection_Data.php');
    require_once(dirname(__FILE__).'/../Framework/Mysqli_Tool.php');
    require_once(dirname(__FILE__).'/../Framework/Email.php');
    require_once(dirname(__FILE__).'/../models/back/Email_Model.cron.php');

    $model  = new Email_Model();
		
		try
		{
			$accounts = $model->getAllEmailAccounts();
		
			foreach ($accounts as $account)
			{
                ob_start();
				if ($account['password'])
				{    		
					$email = new Email(
							$account['host'], 
							$account['port'],
							'none', 
							$account['email'], 
							$account['password'], 
							$account['host_service'], 
							'older',
							'../files/attachments/'
						);
				
					$email->open();
					$content = $email->getMessages();

					if ($content)
					{
						foreach ($content as $details)
						{
								
							if ($details['totalAttachments'] > 0)
							{
								$tmp_id = 0;
								foreach($details['attachmentsDetail']['attachments'] as $att)
								{
									if(isset($att['name']))
									{
										$attName = $email->cleanupName($att['name']);
										
										$model->saveAttachments(
												$details['messageId'],
												$details['fromAddr'], 
												$attName
											);
										
										if($att['id'])
										{
											$id = str_replace(array('<', '>'), '', $att['id']);
											$search	= "src=\"cid:".$id."\"";
											// change www.example.com etc to actual URL
											$replace	= "src=\"".$details['attachmentsDetail']['mboxdir'].$att['name']."\"";
											// now do the replacements
											$details['body'] = str_replace($search, $replace, $details['body']);
										}
									}else
									{
										$tmp_id 	+= 1;
										foreach($att as $a)
										{
											$attName = $email->cleanupName($a['name']);
											if($attName =='' && is_array($a)){
												$filenamesarray[] = $tmp_id.'_'.$email->getValueByKey($a, 'name', $filenamesarray);
												$attName = $tmp_id.'_'.$email->getValueByKey($a, 'name', $filenamesarray);
											}else{
											$attName	= $tmp_id.'_'.$attName;
											}
											$a['name'] 	= $attName;
											
											$model->saveAttachments(
													$details['messageId'],
													$details['fromAddr'], 
													$attName
												);
											
											if($a['id'])
											{
												$id = str_replace(array('<', '>'), '', $a['id']);
												$search	= "src=\"cid:".$id."\"";												// change www.example.com etc to actual URL
												$replace	= "src=\"".$details['attachmentsDetail']['mboxdir'].$a['name']."\"";
												// now do the replacements
												$details['body'] = str_replace($search, $replace, $details['body']);
											}
										}
									}
								}
							}
							
							$model->saveEmailMessages(
											$details['messageId'],
											$details['udate'], 
											$details['udate'], 
											$details['fromAddr'], 
											$account['email'], 
											$details['fromName'], 
											$details['subject'], 
											$details['body'], 
											$details['totalAttachments']
											);

						}
					}
					
					$emailModel  = new Email_Model($branch);
					$countInbox = $emailModel->countInboxByEmailAccount($account['email_id']);
					$emailModel->setNumberOfInboxByAccount($account['email_id'], $countInbox);
					
					$countOutbox = $emailModel->countOutboxByEmailAccount($account['email_id']);
					$emailModel->setNumberOfOutboxByAccount($account['email_id'], $countOutbox);
				
					$countArchived = $emailModel->countArchivedByEmailAccount($account['email_id']);
					$emailModel->setNumberOfArchivedByAccount($account['email_id'], $countArchived);

					$email->close();
				}
                
                $emailAccount = $account['email'];
                
                $info = ob_get_contents();
                ob_end_clean();
                
                echo '[ '.date('H:i:s').' ] - '.$info;
                
                $folder = dirname(__FILE__).'/logs/email/'.$emailAccount;
            
                if(!file_exists($folder))
                    mkdir($folder, 0777);
            
                date_default_timezone_set('America/Mexico_City');
            
                $file 	= $folder.'/'.date('d-M-Y').'.log';
                $handle = fopen($file , 'a') or die('can\'t open file');
                
                $info = '[ '.date('h:i:s').' ] - '.$info;
    
                $fileContents = file_get_contents($file);
                
                file_put_contents($file, $fileContents.$info);
                fclose($handle);
                
			}
		
		}
		catch (Exception $e)
		{
			return false;
		}
