<?php
	include '../databaseConnection.php';
		$getNewCon = $_POST['sendNewCon'];
		$getsaveTo = $_POST['saveTo'];
		$getuserid = $_POST['sendUserId'];
			echo $chome->updateUserContact($getNewCon,$getsaveTo,$getuserid);
?>