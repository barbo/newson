<?php 

require_once("models/config.php");

securePage($_SERVER['PHP_SELF']);


if(isUserLoggedIn()) { header("Location: account.php"); die(); }


if(!empty($_POST))
{
	$errors = array();
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	$displayname = trim($_POST["displayname"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);
	$captcha = md5($_POST["captcha"]);

	
	
	if ($captcha != $_SESSION['captcha'])
	{
		$errors[] = $_SESSION['lang']["CAPTCHA_FAIL"];
	}
	if(minMaxRange(5,25,$username))
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_USER_CHAR_LIMIT"];
	}
	if(!ctype_alnum($username)){
		$errors[] = $_SESSION['lang']["ACCOUNT_USER_INVALID_CHARACTERS"];
	}
	if(minMaxRange(5,25,$displayname))
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_DISPLAY_CHAR_LIMIT"];
	}
	if(!ctype_alnum($displayname)){
		$errors[] = $_SESSION['lang']["ACCOUNT_DISPLAY_INVALID_CHARACTERS"];
	}
	if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_PASS_CHAR_LIMIT"];
	}
	else if($password != $confirm_pass)
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_PASS_MISMATCH"];
	}
	if(!isValidEmail($email))
	{
		$errors[] = $_SESSION['lang']["ACCOUNT_INVALID_EMAIL"];
	}
	

	//End data validation
	if(count($errors) == 0)
	{	
		//Construct a user object
		$user = new User($username,$displayname,$password,$email);
		
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = $_SESSION['lang']["ACCOUNT_USERNAME_IN_USE"];
			if($user->displayname_taken) $errors[] = $_SESSION['lang']["ACCOUNT_DISPLAYNAME_IN_USE"];
			if($user->email_taken) 	  $errors[] = $_SESSION['lang']["ACCOUNT_EMAIL_IN_USE"];		
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			if(!$user->AddUser())
			{
				if($user->mail_failure) $errors[] = $_SESSION['lang']["MAIL_ERROR"];
				if($user->sql_failure)  $errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
	}
	if(count($errors) == 0) {
		$successes[] = $user->success;
	}
}

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'></div></div>
<div id='content'>
<h2>".$_SESSION['lang']['REGISTER_']."</h2>

<div id='left-nav'>";
include("left-nav.php");
echo "
</div>

<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<div id='regbox'>
<form name='newUser' action='".$_SERVER['PHP_SELF']."' method='post' enctype='multipart/form-data'>
<table width='500' border='0' cellpadding='1' cellspacing='1'>

  <tr>
    <td>".$_SESSION['lang']['USERNAME_:']."</td>
	  <td align='center'></td>
    <td><input type='text' name='username'/> *</td>
  </tr>
    <tr>
    <td>"$_SESSION['lang']['Displayname_:']."</td>
	  <td align='center'></td>
    <td><input type='text' name='displayname'/> *</td>
  </tr>
  <tr>
    <td>".$_SESSION['lang']['PASSWORD_:']."</td>
	  <td align='center'></td>
    <td><input type='password' name='password'/> *</td>
  </tr>  
   <tr>
    <td>".$_SESSION['lang']['CPASS_:']."</td>
	  <td align='center'></td>
    <td><input type='password' name='passwordc'/> *</td>
  </tr> 
   <tr>
    <td>".$_SESSION['lang']['email']."</td>
	  <td align='center'></td>
    <td><input type='text' name='email'/> *</td>
  </tr>  
  <tr>
	<td>".$_SESSION['lang']['SEC_CODE_:']."</td>
	  <td align='center'></td>
    <td><img src='models/captcha.php'/></td>
  </tr> 
    <tr>
	<td>".$_SESSION['lang']['ENTER_SEC_CODE_:']."</td>
	  <td align='center'></td>
    <td><input type='text' name='captcha' id='captcha'></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
	  <td align='center'></td>
    <td><input type='submit' value='Register'/></td>
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
