<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);
$userId = $_GET['id'];


if(!userIdExists($userId)){
	header("Location: admin_users.php"); die();
}

$userdetails = fetchUserDetails(NULL, NULL, $userId); 

if(!empty($_POST))
{	

	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deleteUsers($deletions)) {
			$successes[] = $_SESSION['lang']["ACCOUNT_DELETIONS_SUCCESSFUL"];
		}
		else {
			$errors[] = $_SESSION['lang']["SQL_ERROR"];
		}
	}
	else
	{
	
		if ($userdetails['display_name'] != $_POST['display']){
			$displayname = trim($_POST['display']);
			
	
			if(displayNameExists($displayname))
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_DISPLAYNAME_IN_USE"];
			}
			elseif(minMaxRange(5,25,$displayname))
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_DISPLAY_CHAR_LIMIT"];
			}
			elseif(!ctype_alnum($displayname)){
				$errors[] = $_SESSION['lang']["ACCOUNT_DISPLAY_INVALID_CHARACTERS"];
			}
			else {
				if (updateDisplayName($userId, $displayname)){
					$successes[] = $_SESSION['lang']["ACCOUNT_DISPLAYNAME_UPDATED"];
				}
				else {
					$errors[] = $_SESSION['lang']["SQL_ERROR"];
				}
			}
			
		}
		else {
			$displayname = $userdetails['display_name'];
		}
		
		if(isset($_POST['activate']) && $_POST['activate'] == "activate"){
			if (setUserActive($userdetails['activation_token'])){
				$successes[] = $_SESSION['lang']["ACCOUNT_MANUALLY_ACTIVATED"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
		

		if ($userdetails['email'] != $_POST['email']){
			$email = trim($_POST["email"]);
			
	
			if(!isValidEmail($email))
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_INVALID_EMAIL"];
			}
			elseif(emailExists($email))
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_EMAIL_IN_USE"];
			}
			else {
				if (updateEmail($userId, $email)){
					$successes[] = $_SESSION['lang']["ACCOUNT_EMAIL_UPDATED"];
				}
				else {
					$errors[] = $_SESSION['lang']["SQL_ERROR"];
				}
			}
		}
		
		
		if ($userdetails['title'] != $_POST['title']){
			$title = trim($_POST['title']);
			
			
			if(minMaxRange(1,50,$title))
			{
				$errors[] = $_SESSION['lang']["ACCOUNT_TITLE_CHAR_LIMIT"];
			}
			else {
				if (updateTitle($userId, $title)){
					$successes[] = $_SESSION['lang']["ACCOUNT_TITLE_UPDATED"];
				}
				else {
					$errors[] = $_SESSION['lang']["SQL_ERROR"];
				}
			}
		}
		

		if(!empty($_POST['removePermission'])){
			$remove = $_POST['removePermission'];
			if ($deletion_count = removePermission($remove, $userId)){
				$successes[] = $_SESSION['lang']["ACCOUNT_PERMISSION_REMOVED"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
		
		if(!empty($_POST['addPermission'])){
			$add = $_POST['addPermission'];
			if ($addition_count = addPermission($add, $userId)){
				$successes[] = $_SESSION['lang']["ACCOUNT_PERMISSION_ADDED"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
		
		$userdetails = fetchUserDetails(NULL, NULL, $userId);
	}
}

$userPermission = fetchUserPermissions($userId);
$permissionData = fetchAllPermissions();

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>My Form</h1>
<h2>".$_SESSION['lang']['Admin_User']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<form name='adminUser' action='".$_SERVER['PHP_SELF']."?id=".$userId."' method='post'>
<table class='admin'><tr><td>
<h3>".$_SESSION['lang']['User_information']."</h3>
<div id='regbox'>
<p>
<label>".$_SESSION['lang']['ID_:']."</label>
".$userdetails['id']."
</p>
<p>
<label>".$_SESSION['lang']['USERNAME_:']."</label>
".$userdetails['user_name']."
</p>
<p>
<label>".$_SESSION['lang']['Displayname_:']."</label>
<input type='text' name='display' value='".$userdetails['display_name']."' />
</p>
<p>
<label>".$_SESSION['lang']['email']."</label>
<input type='text' name='email' value='".$userdetails['email']."' />
</p>
<p>
<label>".$_SESSION['lang']['ACTIVE_:']."</label>";


if ($userdetails['active'] == '1'){
	echo "Yes";	
}
else{
	echo "No
	</p>
	<p>
	<label>".$_SESSION['lang']['ACTIVATE_:']."</label>
	<input type='checkbox' name='activate' id='activate' value='activate'>
	";
}

echo "
</p>
<p>
<label>".$_SESSION['lang']['TITLE_:']."</label>
<input type='text' name='title' value='".$userdetails['title']."' />
</p>
<p>
<label>".$_SESSION['lang']['sign_up_:']."</label>
".date("j M, Y", $userdetails['sign_up_stamp'])."
</p>
<p>
<label>".$_SESSION['lang']['Last_sign_in_:']."</label>";


if ($userdetails['last_sign_in_stamp'] == '0'){
	echo "Never";	
}
else {
	echo date("j M, Y", $userdetails['last_sign_in_stamp']);
}

echo "
</p>
<p>
<label>".$_SESSION['lang']['DELETE_:']."</label>
<input type='checkbox' name='delete[".$userdetails['id']."]' id='delete[".$userdetails['id']."]' value='".$userdetails['id']."'>
</p>
<p>
<label>&nbsp;</label>
<input type='submit' value='Update' class='submit' />
</p>
</div>
</td>
<td>
<h3>".$_SESSION['lang']['Permission_Membership']."</h3>
<div id='regbox'>
<p>".$_SESSION['lang']['Remove_perm']."";


foreach ($permissionData as $v1) {
	if(isset($userPermission[$v1['id']])){
		echo "<br><input type='checkbox' name='removePermission[".$v1['id']."]' id='removePermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
	}
}


echo "</p><p>".$_SESSION['lang']['add_perm']."";
foreach ($permissionData as $v1) {
	if(!isset($userPermission[$v1['id']])){
		echo "<br><input type='checkbox' name='addPermission[".$v1['id']."]' id='addPermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['name'];
	}
}

echo"
</p>
</div>
</td>
</tr>
</table>
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
