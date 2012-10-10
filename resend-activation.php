<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);

//Forms posted
if(!empty($_POST) && $emailActivation)
{
	$email = $_POST["email"];
	$username = $_POST["username"];
	
	//Perform some validation
	//Feel free to edit / change as required
	if(trim($email) == "")
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_SPECIFY_EMAIL"];
	}
	//Check to ensure email is in the correct format / in the db
	else if(!isValidEmail($email) || !emailExists($email))
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_INVALID_EMAIL"];
	}
	
	if(trim($username) == "")
	{
		$_SESSION['lang']["ACCOUNT_SPECIFY_USERNAME"];
	}
	else if(!usernameExists($username))
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_INVALID_USERNAME"];
	}
	
	if(count($errors) == 0)
	{
		//Check that the username / email are associated to the same account
		if(!emailUsernameLinked($email,$username))
		{
			$errors[] = $_SESSION['lang']["ACCOUNT_USER_OR_EMAIL_INVALID"];
		}
		else
		{
			$userdetails = fetchUserDetails($username);
			
			//See if the user's account is activation
			if($userdetails["active"]==1)
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_ALREADY_ACTIVE"];
			}
			else
			{
				if ($resend_activation_threshold == 0) {
					$hours_diff = 0;
				}
				else {
					$last_request = $userdetails["last_activation_request"];
					$hours_diff = round((time()-$last_request) / (3600*$resend_activation_threshold),0);
				}
				
				if($resend_activation_threshold!=0 && $hours_diff <= $resend_activation_threshold)
				{
					$errors[] = $_SESSION['lang']["ACCOUNT_LINK_ALREADY_SENT"];
				}
				else
				{
					//For security create a new activation url;
					$new_activation_token = generateActivationToken();
					
					if(!updateLastActivationRequest($new_activation_token,$username,$email))
					{
						$errors[] = $_SESSION['lang']["SQL_ERROR"];
					}
					else
					{
						$mail = new bkmail();
						
						$activation_url = $websiteUrl."activate-account.php?token=".$new_activation_token;
						
						//Setup our custom hooks
						$hooks = array(
							"searchStrs" => array("#ACTIVATION-URL","#USERNAME#"),
							"subjectStrs" => array($activation_url,$userdetails["display_name"])
							);
						
						if(!$mail->newTemplateMsg("resend-activation.txt",$hooks))
						{
							$errors[] = $_SESSION['lang']["MAIL_TEMPLATE_BUILD_ERROR"];
						}
						else
						{
							if(!$mail->sendMail($userdetails["email"],"Activate your ".$websiteName." Account"))
							{
								$errors[] = $_SESSION['lang']["MAIL_ERROR"];
							}
							else
							{
								//Success, user details have been updated in the db now mail this information out.
							    $errors[] = $_SESSION['lang']["ACCOUNT_NEW_ACTIVATION_SENT"];
							}
						}
					}
				}
			}
		}
	}
}

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>My Form</h1>
<h2>".$_SESSION['lang']['Resend_Activation']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";


echo resultBlock($languas,$successes);

echo "<div id='regbox'>";

//Show disabled if email activation not required
if(!$emailActivation)
{ 
        echo $_SESSION['lang']["FEATURE_DISABLED"];
}
else
{
	echo "<form name='resendActivation' action='".$_SERVER['PHP_SELF']."' method='post'>
	<p>
	<label>".$_SESSION['lang']['USERNAME_:']."</label>
	<input type='text' name='username' />
        </p>     
        <p>
        <label>".$_SESSION['lang']['email']."</label>
        <input type='text' name='email' />
        </p>    
        <p>
        <label>&nbsp;</label>
        <input type='submit' value='Submit' class='submit' />
        </p>
        </form>";
}

echo "
</div>           
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
