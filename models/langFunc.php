<?php

require_once('langarray.php');

if (empty ($_SESSION ['lang'])){
	$_SESSION ['lang'] = $lang ['ua'];
}
if (isset ($_POST ['lang'])){

	if ($_POST ['lang'] == 'Ua'){
		$_SESSION ['lang'] = $lang ['ua'];
	}
	
	elseif ($_POST ['lang'] == 'Eng'){
		$_SESSION ['lang'] = $lang ['eng'];
	}
		
}

?>