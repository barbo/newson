<?php

securePage($_SERVER['PHP_SELF']);


if(isUserLoggedIn()) {
	echo "

	<table width='1340' border='0' cellpadding='1' cellspacing='1'>
  <tr>
	  <td align='left'></td>
    <td><a href='account.php'>".$_SESSION['lang']['Account_Home']."</a></td>
  </tr>
    <tr>
	  <td align='left'></td>
    <td><a href='user_settings.php'>".$_SESSION['lang']['User_settings']."</a></td>
  </tr>
  <tr>
	  <td align='left'></td>
    <td><a href='logout.php'>".$_SESSION['lang']['LOGOUT_']."</a></td>
  </tr>  

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </table>";
	

	if ($loggedInUser->checkPermission(array(2))){
	echo "
	<form>
	<table width='1340' border='0' cellpadding='1' cellspacing='1'>
  <tr>
	  <td align='left'></td>
    <td><a href='admin_configuration.php'>".$_SESSION['lang']['Admin_config']."</a></td>
  </tr>
    <tr>
	  <td align='left'></td>
    <td><a href='admin_users.php'>".$_SESSION['lang']['Admin_Users']."</a></td>
  </tr>
  <tr>
	  <td align='left'></td>
    <td><a href='admin_permissions.php'>".$_SESSION['lang']['Admin_permission']."</a></td>
  </tr>  
	<tr>
	  <td align='left'></td>
    <td><a href='admin_pages.php'>".$_SESSION['lang']['admin_pages']."</a></td>
  </tr>  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </table>
  </form>";
	}
} 

else {
	echo "
	<form>
	<table width='1340' border='0' cellpadding='1' cellspacing='1'>
  <tr>
	  <td align='left'></td>
    <td><a href='index.php'>".$_SESSION['lang']['HOME_']."</a></td>
  </tr>
    <tr>
	  <td align='left'></td>
    <td><a href='login.php'>".$_SESSION['lang']['LOGIN_']."</a></td>
  </tr>
   </tr>
 
  <tr>
	  <td align='left'></td>
    <td><a href='register.php'>".$_SESSION['lang']['REGISTER_']."</a></td>
  </tr>  
<tr>
	  <td align='left'></td>
    <td><a href='forgot-password.php'>".$_SESSION['lang']['Forgot_Pass']."</a></td>
  </tr> 
<tr>
	  <td align='left'></td>
    <td><a href='admin_pages.php'>".$_SESSION['lang']['admin_pages']."</a></td>
  </tr>  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </table>
  </form>";
  
	if ($emailActivation)
	{
	echo "
	<form>
	<table width='1225' border='0' cellpadding='1' cellspacing='1'>
	<tr>
	  <td align='left'></td>
    <td><a href='resend-activation.php'>".$_SESSION['lang']['Resend_Activation_Email']."</a></td>
  </tr>
  </table>
  </form>";
	}
	echo "</ul>";
}

?>
