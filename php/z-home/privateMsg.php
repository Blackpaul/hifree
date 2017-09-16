<?php  
	include '../databaseConnection.php';
		$getOnlineId = $_POST['sendOnlineId'];
		$getUsereId = $_POST['sendUserId'];
		$getSendMsg = $_POST['sendMsg'];
			echo $chome->privateMsg($getOnlineId,$getUsereId,$getSendMsg);
?>