<?php
	include '../databaseConnection.php';
		$getyourpost = $_POST['yourPost'];
		$getuserid = $_POST['userId'];
			$chome->postMsgfunction($getyourpost,$getuserid);
				echo "yes"
?>