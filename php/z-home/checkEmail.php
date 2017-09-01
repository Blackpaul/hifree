<?php  
	session_start();
		error_reporting(0);
			include '../databaseConnection.php';
				$getEmail = $_POST['sendEmail'];
				$getSessionEmail = $_SESSION['loginuseremail'];
					echo $chome->checkEmail($getEmail,$getSessionEmail);
?>