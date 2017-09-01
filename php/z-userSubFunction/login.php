<?php  
	include '../userFunction.php';
			$getLoginEmail = trim($_POST['txtLoginUseremail']);
			$getLoginPass  = trim($_POST['txtLoginUserpass']);
		 			$result = login($getLoginEmail, $getLoginPass);
		 			echo $result;
?>