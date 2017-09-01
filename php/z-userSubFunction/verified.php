<?php  
	error_reporting(0);
		include '../userFunction.php';
	 		$gemail = $_POST['gvemail'];
	 		$gvcode = $_POST['gvcode'];
				$result = verifyAccount($gemail,$gvcode);
				if($result == "verified"){
					echo $result;
				}else {
					echo $result;
				}
?>