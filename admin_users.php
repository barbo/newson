<?php 

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);


if(!empty($_POST))
{
	$deletions = $_POST['delete'];
	if ($deletion_count = deleteUsers($deletions)){
		$successes[] = $_SESSION['lang']["ACCOUNT_DELETIONS_SUCCESSFUL"];
	}
	else {
		$errors[] = $_SESSION['lang']["SQL_ERROR"];
	}
}

$userData = fetchAllUsers(); 

require_once("models/header.php");
echo "
<body>
<div id='wrapper'>
<div id='top'><div id='logo'></div></div>
<div id='content'>
<h1>My Form</h1>
<h2>".$_SESSION['lang']['Admin_Users']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>
<div id='main'>";

echo resultBlock($errors,$successes);

echo "
<form name='adminUsers' action='".$_SERVER['PHP_SELF']."' method='post'>
<table class='admin'>
<tr>
<th>".$_SESSION['lang']['DELETE_']."</th><th>".$_SESSION['lang']['USERNAME_']."</th><th>".$_SESSION['lang']['Displayname_']."</th><th>Title</th><th>".$_SESSION['lang']['Last_sign_in_']."</th>
</tr>";


foreach ($userData as $v1) {
	
	echo "
	<tr>
	<td><input type='checkbox' name='delete[".$v1['id']."]' id='delete[".$v1['id']."]' value='".$v1['id']."'></td>
	<td><a href='admin_user.php?id=".$v1['id']."'>".$v1['user_name']."</a></td>
	<td>".$v1['display_name']."</td>
	<td>".$v1['title']."</td>
	<td>
	";
	

	if ($v1['last_sign_in_stamp'] == '0'){
		echo "Never";	
	}
	else {
		echo date("j M, Y", $v1['last_sign_in_stamp']);
	}
	echo "
	</td>
	</tr>";
}

echo "
</table>
<input type='submit' name='Submit' value='Delete' />
</form>
</div>
<div id='bottom'></div>
</div>
</body>
</html>";

?>
