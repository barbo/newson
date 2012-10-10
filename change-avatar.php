<?php


	require_once("models/config.php");
	
	//Prevent the user visiting the logged in page if he/she is not logged in
	if(!isUserLoggedIn()) { header("Location: login.php"); die(); }
	 
	 $avatar = new InsertAvatarCore;

//Forms posted
if(!empty($_POST))
{	
	$insercion = new InsertAvatarCore;

	// Array with the fields to check
	$fields = array('nombre');

	// Check for fields
	if($insercion->checkFields($fields) === false)
	$errors[] = $_SESSION['lang']['UNABLE_AVATAR_SET'];
		


	$imagen = $insercion->saveAvatarImage($_FILES['avatar'], $_POST['nombre'], 'layout/');


	// Save everything in MySQL
	// Parameter 1: Name of avatar
	if($insercion->saveAvatarMySQL($imagen) === false)
		$errors[] = $_SESSION['lang']['ERR_OCCURRED'];
	else
		$successes[] = $_SESSION['lang']['CORRECT_REC'];
}
?>
 <?php include("models/header.php"); ?>
<body>
<div id="wrapper">

	<div id="content">
    
        <div id="left-nav">
       
            <div class="clear"></div>
        </div>


		<div id="main">
        
        <h1><?php echo $_SESSION['lang']['Change_avatar'];?></h1>

		<?php if(!empty($_POST)) { if(count($errors) > 0) { ?>
		<div class="success">
		<?php echo resultBlock($errors,$successes); ?>
		</div>     
		<?php } } ?> 

		

    	<div id="regbox">
            <form name="changePass" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
	
            <img src="layout/<?php echo $avatar->getAvatarIMG($loggedInUser->user_id); ?>"  /> 
                <p>
                    <label><?php echo $_SESSION['lang']['avatar_:'];?></label>
                    <input type="file" id="avatar" name="avatar" class="text" />
					<input type="hidden" id="nombre" name="nombre" class="text" value="<?php echo $loggedInUser->displayname; ?>" />
                </p>
                
        		<p>
                    <label>&nbsp;</label>
                    <input type="submit" value="Update Avatar" class="submit" />
               </p>
			   <p>
                    <label>&nbsp;</label>
                    <a href="user_settings.php"><?php echo $_SESSION['lang']['GERI_'];?>&raquo;</a>
					 	
               </p>
			  
                    
            </form>
    
   			<div class="clear"></div>
    	</div>
        
        
        </div>
    </div>
</div>
</body>
</html>