<?php

	session_start();




	//try {
	//	$connect = new PDO('mysql:host=localhost;dbname=id1691655_hifree','id1691655_hifree','sajulan143'); /*connect db*/
	//	$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); /*find error exeption*/
	//} catch (PDOException $e) { 
	//	echo $e->getMessage();
	//	die();
	//	/*what to do with the error*/
	//}

	try {
		$connect = new PDO('mysql:host=127.0.0.1;dbname=hifree','root',''); /*connect db*/
		$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); /*find error exeption*/


	} catch (PDOException $e) { 
		echo $e->getMessage();
		die();
		/*what to do with the error*/
		
	}
  
	include_once 'homeFunction.php';

?>