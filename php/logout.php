<?php  
	error_reporting(0);
		include 'databaseConnection.php';
			$chome->updateLoginHistory();
				session_start();
				session_destroy();
		header('location:../index.html');
?>