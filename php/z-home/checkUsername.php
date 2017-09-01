<?php  
	session_start();
		error_reporting(0);
			include '../databaseConnection.php';
				$getUserName = $_POST['sendUsername'];
				$getSessionUserName = $_SESSION['loginusername'];
					echo $chome->checkUsername($getUserName,$getSessionUserName);
?>