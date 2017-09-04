<?php  
	include '../databaseConnection.php';
		$getUserId 		= $_POST['sendUserId'];
		$getUserContact = $_POST['sendUserContact'];

			echo $chome->deleteContacts($getUserId,$getUserContact);

?>