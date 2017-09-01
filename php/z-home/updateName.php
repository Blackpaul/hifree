<?php  
	include '../databaseConnection.php';
		$receiveFname = $_POST['fnametxt'];
		$receiveMname = $_POST['mnametxt'];
		$receiveLname = $_POST['lnametxt'];
		$receiveUserId = $_POST['sendUserId'];
		$updateOn = "fullName";
			echo $chome->updateUserInfo($receiveFname,$receiveMname,$receiveLname,$receiveUserId,$updateOn);

?>