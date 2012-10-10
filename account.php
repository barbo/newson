<?php

require_once("models/config.php");
securePage($_SERVER['PHP_SELF']);
require_once("models/header.php");
 $avatar = new InsertAvatarCore;
echo "
<body>
<div id='wrapper'>
<div id='top'></div>
<div id='content'>
<h2>".$_SESSION['lang']['account']."</h2>
<div id='left-nav'>";

include("left-nav.php");

echo "
</div>

<div id='main'>
 <img src='layout/".$avatar->getAvatarIMG($loggedInUser->user_id)."'  />
".$_SESSION['lang']['hi'].", $loggedInUser->displayname. ".$_SESSION['lang']['you']." $loggedInUser->title, ".$_SESSION['lang']['not']."" . date("M d, Y", $loggedInUser->signupTimeStamp()) . ".
</div>
<div id='bottom'></div>
</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div>&nbsp;</div>
</body>
</html>";

?>

