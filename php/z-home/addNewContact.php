<?php  
	include '../databaseConnection.php';
		$getNewCon = $_POST['newCon'];
		$getSaveTo = $_POST['saveTo'];
		$getuserId = $_POST['userId'];
			$chome->addNewContact($getNewCon,$getSaveTo,$getuserId);
				echo "save";
?>