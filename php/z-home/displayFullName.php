<?php  
	include '../databaseConnection.php';
		$getUserId = $_POST['sendUserId'];
			echo $chome->displayFullName($getUserId);
?>