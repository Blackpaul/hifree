<?php  
error_reporting(0);
	include '../databaseConnection.php';
		$getOnlineId = 8;
		$getUsereId = 7;

			echo $chome->displayPrivateChat($getOnlineId,$getUsereId);
?>
