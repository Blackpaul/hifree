<?php  
	include '../databaseConnection.php';
		$getUserContact = $_POST['sendUserContact'];
		$getContactTodo = $_POST['sendContactTodo'];

			echo $chome->displayUserContactId($getUserContact,$getContactTodo);

?>
