<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$successes = array();
	$username = sanitize(trim($_POST["username"]));
	$password = trim($_POST["password"]);
	
	//Perform some validation
	//Feel free to edit / change as required
	if($username == "")
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_SPECIFY_USERNAME"];
	}
	if($password == "")
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_SPECIFY_PASSWORD"];
	}

	if(count($errors) == 0)
	{
		//A security note here, never tell the user which credential was incorrect
		if(!usernameExists($username))
		{
			$errors[] = $_SESSION['lang']["ACCOUNT_USER_OR_PASS_INVALID"];
		}
		else
		{
			$userdetails = fetchUserDetails($username);
			//See if the user's account is activated
			if($userdetails["active"]==0)
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_INACTIVE"];
			}
			else
			{
				//Hash the password and use the salt from the database to compare the password.
				$entered_pass = generateHash($password,$userdetails["password"]);
				
				if($entered_pass != $userdetails["password"])
				{
					//Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
					$errors[] = $_SESSION['lang']["ACCOUNT_USER_OR_PASS_INVALID"];
				}
				else
				{

					$loggedInUser = new loggedInUser();
					$loggedInUser->email = $userdetails["email"];
					$loggedInUser->user_id = $userdetails["id"];
					$loggedInUser->hash_pw = $userdetails["password"];
					$loggedInUser->title = $userdetails["title"];
					$loggedInUser->displayname = $userdetails["display_name"];
					$loggedInUser->username = $userdetails["user_name"];
					$loggedInUser->avatar = $userdetails["Avatar_URL"];

					$loggedInUser->updateLastSignIn();
					$_SESSION["bkuser"] = $loggedInUser;
					
				
					header("Location: account.php");
					die();
				}
			}
		}
	}
}

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'></div>
<div id='content'>
<h2>".$_SESSION['lang']['LOGIN_']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<div id='regbox'>
<form name='login' action='".$_SERVER['PHP_SELF']."' method='post'>
<table width='300' border='0' cellpadding='1' cellspacing='1'>
  <tr>
    <td>".$_SESSION['lang']['USERNAME_:']."</td>
	  <td align='center'></td>
    <td><input type='text' name='username' /> *</td>
  </tr>
  <tr>
    <td>".$_SESSION['lang']['PASSWORD_:']."</td>
	  <td align='center'></td>
    <td><input type='password' name='password'/> *</td>
  </tr>  
  <tr>
    <td>&nbsp;</td>
	  <td align='center'></td>
    <td><input type='submit' value='Login' /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </table>
</form>
</div>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
