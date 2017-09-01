<?php
	include '../databaseConnection.php';

		
		$getNewCon = $_POST['sendNewCon'];
		$gettoMake = $_POST['sendtoMake'];
		$getIdentifier = $_POST['sendIdentifier'];
		$getTableCount = $_POST['sendTableCount'];
		$getContactId = $_POST['sendContactId'];
		$getuserid = $_POST['sendUserId'];

		//echo $gettoMake. ' ' .$getIdentifier. ' ' .$getTableCount. ' ' .$getContactId. ' ' .$getuserid;
			echo $chome->updateContactInfo($getNewCon,$gettoMake,$getIdentifier,$getTableCount,$getContactId,$getuserid);
?>
