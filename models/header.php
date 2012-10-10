<?php

//require_once ('models/funcs.php');
require_once ('models/langFunc.php');



echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-9' />
<table width='300' border='1' cellpadding='1' cellspacing='1'>
<tr>
<td colspan='0' align='center'>
<table width='1340' border='0'>
</tr>
<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
			<form align = 'right' action = 'index.php' method = 'post'>
				<select name = 'lang'>
					<option value ='Ua'/>Ua</option>
					<option value = 'Eng'>Eng</option>
				</select>
				<input type = 'submit' value = ".$_SESSION['lang']['change_lang'].">
			</form>
		</td>
	</tr>

	<tr>
		<td colspan='0' align='center'><p><a href='http://validator.w3.org/check?uri=referer'><img src='http://www.w3.org/Icons/valid-xhtml11' alt='Valid XHTML 1.1' height='31' width='88' /></a></p>
		<title>".$websiteName."</title>
		<h1>My Forum</h1>
		</td>
	</tr>
</table>
<link href='models/css/style.css' rel='stylesheet' type='text/css' />
</head>";

?>
