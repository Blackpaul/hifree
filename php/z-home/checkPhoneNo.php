<?php  
	include '../databaseConnection.php';
		$getPhoneNo = $_POST['sendPhone'];
			echo $chome->checkPhoneNo($getPhoneNo);
?>