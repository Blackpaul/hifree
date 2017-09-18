<?php  
error_reporting(0);

	include '../databaseConnection.php';

	$_SESSION['onlineId'] = $_POST['sendOnlineId'];
	$_SESSION['UserId'] = $_POST['sendUserId'];
	$getOnlineId = $_POST['sendOnlineId'];
	$getUsereId = $_POST['sendUserId'];
		
			echo $chome->displayPrivateChat($getOnlineId,$getUsereId);

?>
