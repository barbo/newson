<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);
$permissionId = $_GET['id'];

//Check if selected permission level exists
if(!permissionIdExists($permissionId)){
	header("Location: admin_permissions.php"); die();	
}

$permissionDetails = fetchPermissionDetails($permissionId); //Fetch information specific to permission level

//Forms posted
if(!empty($_POST)){
	
	//Delete selected permission level
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deletePermission($deletions)){
		$errors[] = $_SESSION['lang']["PERMISSION_DELETIONS_SUCCESSFUL"];
		}
		else {
			$errors[] = $_SESSION['lang']["SQL_ERROR"];	
		}
	}
	else
	{
		//Update permission level name
		if($permissionDetails['name'] != $_POST['name']) {
			$permission = trim($_POST['name']);
			
			//Validate new name
			if (permissionNameExists($permission)){
				$errors[] = $_SESSION['lang']["ACCOUNT_PERMISSIONNAME_IN_USE"];
			}
			elseif (minMaxRange(1, 50, $permission)){
				$errors[] = $_SESSION['lang']["ACCOUNT_PERMISSION_CHAR_LIMIT"];	
			}
			else {
				if (updatePermissionName($permissionId, $permission)){
					$errors[] = $_SESSION['lang']["PERMISSION_NAME_UPDATE"];
				}
				else {
					$errors[] = $_SESSION['lang']["SQL_ERROR"];
				}
			}
		}
		
		//Remove access to pages
		if(!empty($_POST['removePermission'])){
			$remove = $_POST['removePermission'];
			if ($deletion_count = removePermission($permissionId, $remove)) {
				$errors[] = $_SESSION['lang']["PERMISSION_REMOVE_USERS"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
		

		if(!empty($_POST['addPermission'])){
			$add = $_POST['addPermission'];
			if ($addition_count = addPermission($permissionId, $add)) {
				$errors[] = $_SESSION['lang']["PERMISSION_ADD_USERS"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
		
		//Remove access to pages
		if(!empty($_POST['removePage'])){
			$remove = $_POST['removePage'];
			if ($deletion_count = removePage($remove, $permissionId)) {
				$errors[] = $_SESSION['lang']["PERMISSION_REMOVE_PAGES"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
		
		//Add access to pages
		if(!empty($_POST['addPage'])){
			$add = $_POST['addPage'];
			if ($addition_count = addPage($add, $permissionId)) {
				$errors[] = $_SESSION['lang']["PERMISSION_ADD_PAGES"];
			}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
			$permissionDetails = fetchPermissionDetails($permissionId);
	}
}

$pagePermissions = fetchPermissionPages($permissionId); //Retrieve list of accessible pages
$permissionUsers = fetchPermissionUsers($permissionId); //Retrieve list of users with membership
$userData = fetchAllUsers(); //Fetch all users
$pageData = fetchAllPages(); //Fetch all pages

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>My Form</h1>
<h2>".$_SESSION['lang']['Admin_permission']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<form name='adminPermission' action='".$_SERVER['PHP_SELF']."?id=".$permissionId."' method='post'>
<table class='admin'>
<tr><td>
<h3>".$_SESSION['lang']['Permission_Information']."</h3>
<div id='regbox'>
<p>
<label>".$_SESSION['lang']['ID_:']."</label>
".$permissionDetails['id']."
</p>
<p>
<label>".$_SESSION['lang']['NAME_:']."</label>
<input type='text' name='name' value='".$permissionDetails['name']."' />
</p>
<label>".$_SESSION['lang']['DELETE_:']."</label>
<input type='checkbox' name='delete[".$permissionDetails['id']."]' id='delete[".$permissionDetails['id']."]' value='".$permissionDetails['id']."'>
</p>
</div></td><td>
<h3>".$_SESSION['lang']['Permission_Membership']."</h3>
<div id='regbox'>
<p>
".$_SESSION['lang']['Remove_Members']."";


foreach ($userData as $v1) {
	if(isset($permissionUsers[$v1['id']])){
		echo "<br><input type='checkbox' name='removePermission[".$v1['id']."]' id='removePermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['display_name'];
	}
}

echo"
</p><p>Add Members:";

foreach ($userData as $v1) {
	if(!isset($permissionUsers[$v1['id']])){
		echo "<br><input type='checkbox' name='addPermission[".$v1['id']."]' id='addPermission[".$v1['id']."]' value='".$v1['id']."'> ".$v1['display_name'];
	}
}

echo"
</p>
</div>
</td>
<td>
<h3>".$_SESSION['lang']['Permission_Access']."</h3>
<div id='regbox'>
<p>
".$_SESSION['lang']['Public_access']."";

foreach ($pageData as $v1) {
	if($v1['private'] != 1){
		echo "<br>".$v1['page'];
	}
}

echo"
</p>
<p>
".$_SESSION['lang']['remove_access']."";


foreach ($pageData as $v1) {
	if(isset($pagePermissions[$v1['id']]) AND $v1['private'] == 1){
		echo "<br><input type='checkbox' name='removePage[".$v1['id']."]' id='removePage[".$v1['id']."]' value='".$v1['id']."'> ".$v1['page'];
	}
}

echo"
</p><p>".$_SESSION['lang']['add_access']."";

foreach ($pageData as $v1) {
	if(!isset($pagePermissions[$v1['id']]) AND $v1['private'] == 1){
		echo "<br><input type='checkbox' name='addPage[".$v1['id']."]' id='addPage[".$v1['id']."]' value='".$v1['id']."'> ".$v1['page'];
	}
}

echo"
</p>
</div>
</td>
</tr>
</table>
<p>
<label>&nbsp;</label>
<input type='submit' value='Update' class='submit' />
</p>
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
