<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$successes= array();
	$cfgId = array();
	$newSettings = $_POST['settings'];
	
	//Validate new site name
	if ($newSettings[1] != $websiteName) {
		$newWebsiteName = $newSettings[1];
		if(minMaxRange(1,150,$newWebsiteName))
		{
			$errors[] = $_SESSION['lang']["CONFIG_NAME_CHAR_LIMIT"];
		}
		else if (count($errors) == 0) {
			$cfgId[] = 1;
			$cfgValue[1] = $newWebsiteName;
			$websiteName = $newWebsiteName;
		}
	}
	
	
	if ($newSettings[2] != $websiteUrl) {
		$newWebsiteUrl = $newSettings[2];
		if(minMaxRange(1,150,$newWebsiteUrl))
		{
			$errors[] = $_SESSION['lang']["CONFIG_URL_CHAR_LIMIT"];
		}
		else if (substr($newWebsiteUrl, -1) != "/"){
			$errors[] = $_SESSION['lang']["CONFIG_INVALID_URL_END"];
		}
		else if (count($errors) == 0) {
			$cfgId[] = 2;
			$cfgValue[2] = $newWebsiteUrl;
			$websiteUrl = $newWebsiteUrl;
		}
	}
	
	//Validate new site email address
	if ($newSettings[3] != $emailAddress) {
		$newEmail = $newSettings[3];
		if(minMaxRange(1,150,$newEmail))
		{
			$errors[] = $_SESSION['lang']["CONFIG_EMAIL_CHAR_LIMIT"];
		}
		elseif(!isValidEmail($newEmail))
		{
			$errors[] = $_SESSION['lang']["CONFIG_EMAIL_INVALID"];
		}
		else if (count($errors) == 0) {
			$cfgId[] = 3;
			$cfgValue[3] = $newEmail;
			$emailAddress = $newEmail;
		}
	}
	
	//Validate email activation selection
	if ($newSettings[4] != $emailActivation) {
		$newActivation = $newSettings[4];
		if($newActivation != "true" AND $newActivation != "false")
		{
			$errors[] = $_SESSION['lang']["CONFIG_ACTIVATION_TRUE_FALSE"];
		}
		else if (count($errors) == 0) {
			$cfgId[] = 4;
			$cfgValue[4] = $newActivation;
			$emailActivation = $newActivation;
		}
	}
	
 
	if ($newSettings[5] != $resend_activation_threshold) {
		$newResend_activation_threshold = $newSettings[5];
		if($newResend_activation_threshold > 72 OR $newResend_activation_threshold < 0)
		{
			$errors[] = $_SESSION['lang']["CONFIG_ACTIVATION_RESEND_RANGE"];
		}
		else if (count($errors) == 0) {
			$cfgId[] = 5;
			$cfgValue[5] = $newResend_activation_threshold;
			$resend_activation_threshold = $newResend_activation_threshold;
		}
	}
	

 
 
	if (count($errors) == 0 AND count($cfgId) > 0) {
		if (updateConfig($cfgId, $cfgValue)) {
			$successes[] = $_SESSION['lang']["CONFIG_UPDATE_SUCCESSFUL"];
		}
		else {
			$errors[] = $_SESSION['lang']["SQL_ERROR"];	
		}
	}
}


$permissionData = fetchAllPermissions();  
require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>My Forum</h1>
<h2> ".$_SESSION['lang']['Admin_config']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<div id='regbox'>
<form name='adminConfiguration' action='".$_SERVER['PHP_SELF']."' method='post'>
<p>
<label>".$_SESSION['lang']['Wsitename']."</label>
<input type='text' name='settings[".$settings['website_name']['id']."]' value='".$websiteName."' />
</p>
<p>
<label>".$_SESSION['lang']['wsiteurl']."</label>
<input type='text' name='settings[".$settings['website_url']['id']."]' value='".$websiteUrl."' />
</p>
<p>
<label>".$_SESSION['lang']['email']."</label>
<input type='text' name='settings[".$settings['email']['id']."]' value='".$emailAddress."' />
</p>
<p>
<label>".$_SESSION['lang']['ActivationThreshold']."</label>
<input type='text' name='settings[".$settings['resend_activation_threshold']['id']."]' value='".$resend_activation_threshold."' />
</p>
<p>
<label>".$_SESSION['lang']['emailactivation']."</label>
<select name='settings[".$settings['activation']['id']."]'>";

 
if ($emailActivation == "true"){
	echo "
	<option value='true' selected>True</option>
	<option value='false'>False</option>
	</select>";
}
else {
	echo "
	<option value='true'>True</option>
	<option value='false' selected>False</option>
	</select>";
}

echo "</p>

<input type='submit' name=".$_SESSION['lang']['submit']." value='Submit' />


</form>
</div>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
