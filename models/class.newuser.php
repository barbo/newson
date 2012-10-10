<?php

class User 
{
	public $user_active = 0;
	private $clean_email;
	public $status = false;
	private $clean_password;
	private $username;
	private $displayname;
	public $sql_failure = false;
	public $mail_failure = false;
	public $email_taken = false;
	public $username_taken = false;
	public $displayname_taken = false;
	public $activation_token = 0;
	public $success = NULL;
	
	function __construct($user,$display,$pass,$email)
	{
 
		$this->displayname = $display;
		
 
		$this->clean_email = sanitize($email);
		$this->clean_password = trim($pass);
		$this->username = sanitize($user);
		
		if(usernameExists($this->username))
		{
			$this->username_taken = true;
		}
		else if(displayNameExists($this->displayname))
		{
			$this->displayname_taken = true;
		}
		else if(emailExists($this->clean_email))
		{
			$this->email_taken = true;
		}
		else
		{
			 
			$this->status = true;
		}
	}
	
	
	public function AddUser()
	{
		global $mysqli,$emailActivation,$websiteUrl,$db_table_prefix;
		
 		if($this->status)
		{
			 
			$secure_pass = generateHash($this->clean_password);
			
 			$this->activation_token = generateActivationToken();
			
		 
			if($emailActivation == "true")
			{
			 
				$this->user_active = 0;
				
				$mail = new bkmail();
				
				 
				$successes[] =  $_SESSION['lang']["ACCOUNT_ACTIVATION_MESSAGE"];
				
				 
				$hooks = array(
					"searchStrs" => array("#ACTIVATION-MESSAGE","#ACTIVATION-KEY","#USERNAME#"),
					"subjectStrs" => array($activation_message,$this->activation_token,$this->displayname)
					);
				
		
				
				if(!$mail->newTemplateMsg("new-registration.txt",$hooks))
				{
					$this->mail_failure = true;
				}
				else
				{
					
					if(!$mail->sendMail($this->clean_email,"New User"))
					{
						$this->mail_failure = true;
					}
				}
				$successes[] = $_SESSION['lang']["ACCOUNT_REGISTRATION_COMPLETE_TYPE2"];
			}
			else
			{
		
				$this->user_active = 1;
				$successes[] = $_SESSION['lang']["ACCOUNT_REGISTRATION_COMPLETE_TYPE1"];
			}	
			
			
			if(!$this->mail_failure)
			{
			
				$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."users (
					user_name,
					display_name,
					password,
					email,
					activation_token,
					last_activation_request,
					lost_password_request, 
					active,
					title,
					sign_up_stamp,
					last_sign_in_stamp,
					Avatar_URL
					)
					VALUES (
					?,
					?,
					?,
					?,
					?,
					'".time()."',
					'0',
					?,
					'New Member',
					'".time()."',
					'0',
					'noavatar.png'
					)");
				
				$stmt->bind_param("sssssis", $this->username, $this->displayname, $secure_pass, $this->clean_email, $this->activation_token, $this->user_active, $this->avatar);
				$stmt->execute();
				$inserted_id = $mysqli->insert_id;
				$stmt->close();
				
			
				$stmt = $mysqli->prepare("INSERT INTO ".$db_table_prefix."user_permission_matches  (
					user_id,
					permission_id
					)
					VALUES (
					?,
					'1'
					)");
				$stmt->bind_param("s", $inserted_id);
				$stmt->execute();
				$stmt->close();
			}
		}
	}
}

?>