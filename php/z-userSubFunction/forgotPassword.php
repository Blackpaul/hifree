<?php
	include '../userFunction.php';
	$getEmail = $_POST['forgotEmail'];
		$result = sendNewPassword($getEmail);
?>