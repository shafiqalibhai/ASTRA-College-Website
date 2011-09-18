<?php
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Christchurch College of Education	              |
// +------------------------------------------------------------------------+
// | This file is part of Interact.											|
// |																	  	| 
// | This program is free software; you can redistribute it and/or modify 	|
// | it under the terms of the GNU General Public License as published by 	|
// | the Free Software Foundation (version 2)							 	|
// |																	  	|	 
// | This program is distributed in the hope that it will be useful, but  	|
// | WITHOUT ANY WARRANTY; without even the implied warranty of		   		|
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU	 	|
// | General Public License for more details.							 	|
// |																	  	|	 
// | You should have received a copy of the GNU General Public License		|
// | along with this program; if not, you can view it at				  	|
// | http://www.opensource.org/licenses/gpl-license.php				   		|
// +------------------------------------------------------------------------+


/**
* email functions
*
* Contains any functions related to sending email from interact
*
* @package email
* @author Glen Davies <glen.davies@cce.ac.nz>
* @copyright Christchurch College of Education 2003 
* @version $Id: email.inc.php,v 1.29 2007/03/22 04:24:02 glendavies Exp $
* 
*/

/**
* email users
*
* emails a list of users 
*
* @package email
* @param string $subject Subject of email being sent
* @param string $body Body of email being sent
* @param array $member_keys array of user keys to send email to
* @param string $copy_self true if sender wants copy
* @param string $attachment file attachment
* @param string $carbon_copy comma separated string of email addresses to copy to
* @param string $from alternative from address
* @param string $palin_text an alternative plain text version of the message
*/
function email_users($subject, $body, $member_keys, $copyself='', $attachment='', $carbon_copy='',$from='',$plain_text='',$to_addr='') {

    global $CONN, $CONFIG;
	
	//see if we need to strip any magic quotes
	$body = interact_stripslashes($body);
	$subject = interact_stripslashes($subject);
    require_once($CONFIG['INCLUDES_PATH'].'/pear/Mail.php');
	
    $error_messages = ''; 
    
	if ($CONFIG['EMAIL_TYPE']=='sendmail') {
	
	    $params['sendmail_path'] = $CONFIG['EMAIL_SENDMAIL_PATH'];
		$params['sendmail_args'] = $CONFIG['EMAIL_SENDMAIL_ARGS'];
 		
	} else if ($CONFIG['EMAIL_TYPE']=='smtp') {
	
	    $params['host']     = $CONFIG['EMAIL_HOST']; 
        $params['port']     = $CONFIG['EMAIL_PORT'] ; 
        $params['auth']     = $CONFIG['EMAIL_AUTH'];  
        $params['username'] = $CONFIG['EMAIL_USERNAME']; 
        $params['password'] = $CONFIG['EMAIL_PASSWORD'];
		
		
	}
   	
    require_once($CONFIG['INCLUDES_PATH'].'/pear/Mail/mime.php');
	$mime = new Mail_mime("\n");
    if (empty($plain_text)) {
		$text_body = str_replace('<p>','',$body);
		$text_body = str_replace('</p>',"\n\n",$text_body);
		$text_body = str_replace('<br />',"\n",$text_body);
	   	$mime->setTXTBody(strip_tags($text_body));
    } else {
    	$mime->setTXTBody($plain_text);	    	
    }
	if (!eregi('(<p|<br)', $body)) {
		$html_body = nl2br($body);
	} else {
		$html_body = $body;
	}
    $mime->setHTMLBody($html_body);
	if ($attachment['name']!='') {        
		$mime->addAttachment($attachment['tmp_name'], 'application/octet-stream',$attachment['name']);
	}        
	$body_txt = $mime->get();
    $headers = $mime->headers($headers);


    if (empty($from)) {
		if (isset($_SESSION['current_user_email'])) {
		$headers['From']    = '"'.$_SESSION['current_user_firstname'].' '.$_SESSION['current_user_lastname'].'" <'.$_SESSION['current_user_email'].'>';
		} else {
			$headers['From']    = '"No Reply" <'.$CONFIG['NO_REPLY_EMAIL'].'>';
		}
	} else {
		$headers['From']    = '<'.$from.'>';
	}

    $headers['Subject'] = $subject;
	
    $mail_object =& Mail::factory($CONFIG['EMAIL_TYPE'], $params);
    if (!method_exists($mail_object,'send')) {print 'mail error: '.$mail_object->getMessage()."<br />\n";exit;}

	$recipients = '';

	if(!empty($to_addr)) {$headers['To'] = $to_addr;} else {
	
		if (is_array($member_keys)) {
		
			foreach ($member_keys as $value) {
				$rs = $CONN->Execute("SELECT first_name, last_name, email FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$value'");
		
				while (!$rs->EOF) {
						
					$recipients .= $rs->fields[2]."\n";
					$headers['To'] = '"'.$rs->fields[0].' '.$rs->fields[1].'" <'.$rs->fields[2].'>';
					$result = $mail_object->send($rs->fields[2], $headers, $body_txt);
					if (PEAR::isError($result)) {
						$error_messages .= 'mail error for : '.'"'.$rs->fields[0].' '.$rs->fields[1].'" <'.$rs->fields[2].'>'.' '.$result->getMessage()."<br />\n";
					} 
					$rs->MoveNext();
				}
			}
			
		} else {
		
			$rs = $CONN->Execute("SELECT first_name, last_name, email FROM {$CONFIG['DB_PREFIX']}users WHERE user_key='$member_keys'");
			$recipients .= $rs->fields[2]."\n";
			$headers['To'] = '"'.$rs->fields[0].' '.$rs->fields[1].'" <'.$rs->fields[2].'>';
			$result = $mail_object->send($rs->fields[2], $headers, $body_txt);
			if (PEAR::isError($result)) {
				$error_messages .= 'mail error for : '.'"'.$rs->fields[0].' '.$rs->fields[1].'" <'.$rs->fields[2].'>'.' '.$result->getMessage()."<br />\n";
			} 
		}
	}

	if ($carbon_copy) {
	
        $carbon_copy_array=explode(',',$carbon_copy);
        foreach ($carbon_copy_array as $to) {
        
		    $to=trim($to);
            $headers['To'] = $to;
			$recipients .= $to."\n";
            $result = $mail_object->send($to, $headers, $body_txt);
    
	        if (PEAR::isError($result)) {
    
	            $error_messages .= 'mail error for : '.$to.' '.$result->getMessage()."<br />\n";
    
	        } 
			
		}
		
	}
	
    if ($copyself=='true') {
  
        if (empty($plain_text)) {
			$text_body .= "\n\nThis email was sent to: \n".$recipients;
		} else {
	    	$text_body = $plain_text."\n\nThis email was sent to: \n".$recipients;
		}
	    $mime->setTXTBody($text_body);	
       	$mime->setHTMLBody($html_body.'<p>This email was sent to: <br />'.$recipients.'<p>');
       	$body_txt = $mime->get();
       	$mime->setHTMLBody($html_body);
       	$headers = $mime->headers($headers);
		$to = $_SESSION['current_user_email'];
		$headers['To'] = $headers['From'];
		$result = $mail_object->send($to, $headers, $body_txt);
    
	    if (PEAR::isError($result)) {
    
	        $error_messages .= 'mail error for : '.$to.' '.$result->getMessage()."<br />\n";
    
	    } 
		
	}

	if ($error_messages=='') {
		return true;
	} else {
		echo "Your email has been sent, but the following problems were reported.<br/>".$error_messages;
	}					
}
?>