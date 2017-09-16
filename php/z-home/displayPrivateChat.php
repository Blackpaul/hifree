<?php  
error_reporting(0);
	include '../databaseConnection.php';
		$getOnlineId = $_POST['sendOnlineId'];
		$getUsereId = $_POST['sendUserId'];

			echo $chome->displayPrivateChat($getOnlineId,$getUsereId);
?>
