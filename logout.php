<?php

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);

	unset($_SESSION['bkuser']); 
	session_destroy(); 
	echo "<script>location.href='index.php';</script>";
?>

