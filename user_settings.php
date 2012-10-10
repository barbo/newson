<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);


if(!isUserLoggedIn()) { header("Location: login.php"); die(); }

 $avatar = new InsertAvatarCore;

if(!empty($_POST))
{
	$errors = array();
	$successes = array();
	$password = $_POST["password"];
	$password_new = $_POST["passwordc"];
	$password_confirm = $_POST["passwordcheck"];
	$email = $_POST["email"];

	
	

		

	$entered_pass = generateHash($password,$loggedInUser->hash_pw);
	
	if (trim($password) == ""){
		 $errors[] = $_SESSION['lang']["ACCOUNT_SPECIFY_PASSWORD"];
	}
	else if($entered_pass != $loggedInUser->hash_pw)
	{
	
		echo $_SESSION['lang']["ACCOUNT_PASSWORD_INVALID"];
	}	
	if($email != $loggedInUser->email)
	{
		if(trim($email) == "")
		{
			$errors[] = $_SESSION['lang']["ACCOUNT_SPECIFY_EMAIL"];
		}
		else if(!isValidEmail($email))
		{
			$errors[] = $_SESSION['lang']["ACCOUNT_INVALID_EMAIL"];
		}
		else if(emailExists($email))
		{
			$errors[] = $_SESSION['lang']["ACCOUNT_EMAIL_IN_USE"];	
		}

	
		if(count($errors) == 0)
		{
			$loggedInUser->updateEmail($email);
			$successes [] = $_SESSION['lang']["ACCOUNT_EMAIL_UPDATED"];
		}
	}
	
	if ($password_new != "" OR $password_confirm != "")
	{
		if(trim($password_new) == "")
		{
			$errors[] =  $_SESSION['lang']["ACCOUNT_SPECIFY_NEW_PASSWORD"];
		}
		else if(trim($password_confirm) == "")
		{
			$errors[] =  $_SESSION['lang']["ACCOUNT_SPECIFY_CONFIRM_PASSWORD"];
		}
		else if(minMaxRange(8,50,$password_new))
		{	
		$errors[] = $_SESSION['lang']["ACCOUNT_NEW_PASSWORD_LENGTH"];
		}
		else if($password_new != $password_confirm)
		{
			$errors[] = $_SESSION['lang']["ACCOUNT_PASS_MISMATCH"];
		}
		
		
		if(count($errors) == 0)
		{
		
			$entered_pass_new = generateHash($password_new,$loggedInUser->hash_pw);
			
			if($entered_pass_new == $loggedInUser->hash_pw)
			{
			
				$successes[] = $_SESSION['lang']["ACCOUNT_PASSWORD_NOTHING_TO_UPDATE"];
			}
			else
			{
				
				$loggedInUser->updatePassword($password_new);
				$successes[] = $_SESSION['lang']["ACCOUNT_PASSWORD_UPDATED"];
			}
		}
	}
	if(count($errors) == 0 AND count($successes) == 0){
		$successes[] = $_SESSION['lang']["NOTHING_TO_UPDATE"];
	}


}

require_once("models/header.php");




echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>

<h2>".$_SESSION['lang']['User_settings']."</h2>
<div id='left-nav'>";
include("left-nav.php");


echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "

<form name='updateAccount' action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data' id='uploadaccount'>  
<table width='500' border='0'>
	<tr>
		<td>
			<img src='layout/".$avatar->getAvatarIMG($loggedInUser->user_id)."'  /> 
		</td>
	</tr>
  <tr>
	<td><a href='change-avatar.php'>".$_SESSION['lang']['Change_avatar']."</a></td>
  </tr>
  <tr>
    <td>".$_SESSION['lang']['PASSWORD_:']."</td>
    <td><input type='password' name='password' id='password' /></td>
  </tr>
  <tr>
    <td>".$_SESSION['lang']['email']."</td>
   <td><input type='text' name='email' value='".$loggedInUser->email."' /></td>
  </tr>
    <tr>
    <td>".$_SESSION['lang']['NEW_PASS_:']."</td>
    <td><input type='password' name='passwordc' id='passwordc'/></td>
  </tr>
    <tr>
    <td>".$_SESSION['lang']['CPASS_:']."</td>
    <td><input type='password' name='passwordcheck' id='passwordcheck'/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type='submit' name='upload' id='upload' value='Update' class='submit' /></td>
  </tr>
<tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
  </table>

</form>

</div>
<div id='bottom'></div>
</div>
</body>
</html>";



?>


