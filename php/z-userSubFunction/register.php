<?php 
	error_reporting(0);
		include '../userFunction.php';
			$gusername		= $_POST['txtusername'];
			$guseremail 	= $_POST['txtuseremail'];
			$guserpass 		= $_POST['txtuserpass'];
			$gusergender 	= $_POST['slctGender'];
			$gverification 	= $_POST['verificationCode'];
			$result = addUser($gusername, $guseremail, $guserpass, $gusergender, $gverification);
	 			echo $result;
?>

