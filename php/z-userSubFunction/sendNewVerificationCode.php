<?php  
	error_reporting(0);
		include '../userFunction.php';
			$gusername		= $_POST['getusername'];
			$guseremail 	= $_POST['getuseremail'];
			$gverification 	= $_POST['newVerificationCode'];
				$sendNewcode = sendVerificationCode($gusername, $guseremail, $gverification, $checker = '');
				$updateUserVcode = newVerificationUpdate($guseremail, $gverification);
?>