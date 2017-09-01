<?php
	include '../databaseConnection.php';
		$getNewCon = $_POST['sendNewCon'];
		$getTodo = $_POST['sendTodo'];
		$getsaveTo = $_POST['saveTo'];
		$getuserid = $_POST['sendUserId'];
			echo $chome->updateUserContactPrimSec($getNewCon,$getTodo,$getsaveTo,$getuserid);
?>