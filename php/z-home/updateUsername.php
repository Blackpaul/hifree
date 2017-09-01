<?php
	include '../databaseConnection.php';
		$getUsername = $_POST['sendUsername'];
		$getuserid = $_POST['sendUserId'];
			echo $chome->updateUsername($getUsername,$getuserid);
?>