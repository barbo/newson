<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);

//Forms posted
if(!empty($_POST))
{
	//Delete permission levels
	if(!empty($_POST['delete'])){
		$deletions = $_POST['delete'];
		if ($deletion_count = deletePermission($deletions)){
		$successes[] = $_SESSION['lang']["PERMISSION_DELETIONS_SUCCESSFUL"];
		}
		else {
			$errors[] = $_SESSION['lang']["SQL_ERROR"];	
		}
	}
	
	//Create new permission level
	if(!empty($_POST['newPermission'])) {
		$permission = trim($_POST['newPermission']);
		
		//Validate request
		if (permissionNameExists($permission)){
			$errors[] = $_SESSION['lang']["PERMISSION_NAME_IN_USE"];
		}
		elseif (minMaxRange(1, 50, $permission)){
			$errors[] = $_SESSION['lang']["PERMISSION_CHAR_LIMIT"];	
		}
		else{
			if (createPermission($permission)) {
			$successes[] = $_SESSION['lang']["PERMISSION_CREATION_SUCCESSFUL"];
		}
			else {
				$errors[] = $_SESSION['lang']["SQL_ERROR"];
			}
		}
	}
}

$permissionData = fetchAllPermissions(); //Retrieve list of all permission levels

require_once("models/header.php");

echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>

<h2>".$_SESSION['lang']['admin_permissions']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<form name='adminPermissions' action='".$_SERVER['PHP_SELF']."' method='post'>
<table class='admin'>
<tr>
<th>".$_SESSION['lang']['DELETE_']."</th><th>".$_SESSION['lang']['Permission_Name']."</th>
</tr>";


foreach ($permissionData as $v1) {
	echo "
	<tr>
	<td><input type='checkbox' name='delete[".$v1['id']."]' id='delete[".$v1['id']."]' value='".$v1['id']."'></td>
	<td><a href='admin_permission.php?id=".$v1['id']."'>".$v1['name']."</a></td>
	</tr>";
}

echo "
</table>
<p>
<label>".$_SESSION['lang']['Permission_Name_:']."</label>
<input type='text' name='newPermission' />
</p>                                
<input type='submit' name='Submit' value='Submit' />
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
