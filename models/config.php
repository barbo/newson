<?php


require_once("db-settings.php"); 



$stmt = $mysqli->prepare("SELECT id, name, value FROM ".$db_table_prefix."configuration");	
$stmt->execute();
$stmt->bind_result($id, $name, $value );

while ($stmt->fetch()){
	$settings[$name] = array('id' => $id, 'name' => $name, 'value' => $value );
}
$stmt->close();

$emailActivation = $settings['activation']['value'];;
$mail_templates_dir = "models/mail-templates/";
$websiteName = $settings['website_name']['value'];
$websiteUrl = $settings['website_url']['value'];
$emailAddress = $settings['email']['value'];
$resend_activation_threshold = $settings['resend_activation_threshold']['value'];
$emailDate = date('dmy');

//$template = $settings['template']['value'];

$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
$default_replace = array($websiteName,$websiteUrl,$emailDate);



require_once("class.mail.php");
require_once("class.user.php");
require_once("class.newuser.php");
require_once("class.avatars.php");
require_once("funcs.php");

session_start();


if(isset($_SESSION["bkuser"]) && is_object($_SESSION["bkuser"]))
{
	$loggedInUser = $_SESSION["bkuser"];
}

?>
