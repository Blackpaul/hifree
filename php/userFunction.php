<?php
include 'databaseConnection.php';

	function login($loginEmail, $loginPass){
		global $connect;
			$login_query = $connect->prepare('SELECT * from tbl_user where userEmail = :useremail and userPass = :userpass');
			$login_query->execute(
					array(
						':useremail' => $loginEmail,
						':userpass'  => $loginPass
					)
				);
			$row = $login_query->rowcount();
			if($row > 0){
				while($row = $login_query->fetch(PDO::FETCH_OBJ)){
					$vcode = $row->verification;
					if($vcode == "verified"){
						$_SESSION['loginuseremail'] = $loginEmail;
						$_SESSION['loginusername'] = $row->userName;
						$_SESSION['loginuserid'] = $row->userId;
						$msg = "success";
							$inserToHistory = insertToLoginHistory($row->userId);
					}else{
						$msg = json_encode(array(
							'username' 	=> $row->userName, 
							'useremail' => $row->userEmail
						));
					}
				}
			}else{
				$msg = "Invalid";
			}

		return $msg;
	}

	function isLogin(){
		if(isset($_SESSION['loginuseremail'])){
			return true;
		}else {
			header('location:../index.html');
		}
	}
	
	function isLoginBackEvent(){
		if(isset($_SESSION['loginuseremail'])){
			header('location:page/home.html');
		}
	}

	function addUser($gusername, $guseremail, $guserpass, $gusergender, $gverification){
		global $connect;
			$check_query = $connect->prepare('SELECT userEmail from tbl_user where userEmail = ?');
		    $check_query->execute(array($guseremail));
		    $rows = $check_query->fetchAll();
		    $num_rows = count($rows);
			    if($num_rows > 0 ){
			        $msg = 'accountExist';
			    } else {
			        $insert_query = $connect->prepare('INSERT into tbl_user(userName, userEmail, userPass, userGender, verification) values (:username, :useremail, :userpass, :usergender, :verification)');
					$insert_query->execute(
						array(
							':username' 	=> $gusername,
							':useremail' 	=> $guseremail,
							':userpass' 	=> $guserpass,
							':usergender' 	=> $gusergender,
							':verification' => $gverification
						)
					);
					$sendCode = sendVerificationCode($gusername, $guseremail, $gverification, $checker = '');
					$msg = 'success';
			    }
		return $msg;
	}

	function verifyAccount($gemail, $gvcode){
		global $connect;
			$check_vcode = $connect->prepare('SELECT * from tbl_user where userEmail = :email');
			$check_vcode->execute(
				array(
					':email' => $gemail
				)
			);
			while($row = $check_vcode->fetch(PDO::FETCH_OBJ)){
				$unverified = $row->verification;
				if ($unverified == $gvcode){
						$verify_query = $connect->prepare('UPDATE tbl_user set verification = :getVerification where userEmail = :useremail');
						$verify_query->execute(
							array(
								':getVerification' => 'verified',
								':useremail'	   => $gemail
							)
						);
					$_SESSION['loginuseremail'] = $row->userEmail;
					$_SESSION['loginusername'] = $row->userName;
					$_SESSION['loginuserid'] = $row->userId;
						$primEmail = "yes";
						$addToContact = addToContactsAsPrimary($row->userEmail,$primEmail,$row->userId);
						$inserToHistory = insertToLoginHistory($row->userId);
					$msg = "verified";
				}else {
					$msg = "Invalid verification code! Check your email address.";
				}
				return $msg;
			}
	}
	function addToContactsAsPrimary($conUserUmail,$primaryEmail,$conUserId){
		global $connect;
			$addToConQuery = $connect->prepare('INSERT into tbl_userContacts(contactEmail,primaryCon,userId) values (:getConEmail,:primaryEmail,:getUuserId)');
			$addToConQuery->execute(
				array(
					':getConEmail'		=>	$conUserUmail,
					':primaryEmail'		=>	$primaryEmail,
					':getUuserId'		=>	$conUserId
				)
			);
	}

	function newVerificationUpdate($guseremail, $gverification){
		global $connect;
			$updateVerification = $connect->prepare('UPDATE tbl_user set verification = :getNewVerification where userEmail = :useremail');
			$updateVerification->execute(
				array(
					':getNewVerification'	=> $gverification,
					':useremail'			=> $guseremail
				)
			);
	}

	function generateNewPassword($length) {
  		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  		$charLength = strlen($characters);
 		$randomChar = '';
  			for ($i = 0; $i < $length; $i++) {
      			$randomChar .= $characters[rand(0, $charLength - 1)];
  			}
  		return $randomChar;
	}

	function sendNewPassword($email){
		global $connect;
			$check_email = $connect->prepare('SELECT * from tbl_user where userEmail = :useremail');
			$check_email->execute(array(
					':useremail'	=> $email
				)
			);
			$row = $check_email->rowcount();
			if($row > 0){
				while($row = $check_email->fetch(PDO::FETCH_OBJ)){
					$username = $row->userName;
				}

				$newPass = generateNewPassword(10);
				$update_pass = $connect->prepare('UPDATE tbl_user set userPass = :newpassword where userEmail = :getforgotEmail');
				$update_pass->execute(array(
					':newpassword'		=> $newPass,
					':getforgotEmail'	=> $email
					)
				);
				$sendCode = sendVerificationCode($username, $email, $newPass, $checker = 'forgot');
				echo "A new password has been send to your email address.";
			}else{
				echo "Invalid email address.";
			}
	}

	function sendVerificationCode($gusername, $guseremail, $gverification, $checker){
		require 'PHPMailer/PHPMailerAutoload.php';
			$mail = new PHPMailer(); 
	  
	  		$subject    		= "HiFree Community";
			$username   		= $gusername;
			$reciever			= $guseremail;
	   		$vcodeOrNewPass		= $gverification;
	   		$checkerWhat 		= $checker;

	   		if ($checkerWhat != "forgot"){
	   			$text0 = "Congratulations!";
	   			$text1 = "you're nearly there!";
	   			$text2 = "We just need to verify you in order to complete your account.";
	   			$text3 = "Kindly enter this code";
	   		}else{
	   			$text0 = "Reset password!";
	   			$text1 = "you're password has been reseted";
	   			$text2 = "Kindly change your password after, for your own satisfaction.";
	   			$text3 = "New password ";
	   		}
	   		
	      
	   		// HTML email starts here
	   		$message  = "<html><body>";
			$message .= "<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
	   		$message .= "<tr><td>";
	   		$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='
	   					max-width:650px; 
	   					background-color:#fff; 
	   					font-family:Verdana, Geneva, sans-serif;'>";
	    	$message .= "<thead>
	      					<tr>
	       						<th colspan='4'  align='left' style='
	       							padding:15px;
	       							border-bottom:solid 1px #bdbdbd;
	       							background-color:#00BFFF;  
	       							color:#fff; 
	       							font-size:24px;'>
	       								<p style='margin: 0 0 0 15px;'>".$text0."</p>
	       						</th>
	      					</tr>
	      				</thead>";
	   		$message .= "<tbody>
	      					<tr>
	       						<td colspan='4' style='padding:30px;'>
	        						<p style='
	        							margin: 30px 0 0 0;
	        							font-size: 13px;
	        							color:#696969;'>Hi <b>".$username."</b>, ".$text1."</p>
	        						<p style='
	        							margin: 15px 0 0 0;
	        							font-size: 13px;
	        							color:#696969;'>".$text2."</p>
	        						<p style='
	        							padding-bottom:50px;
	        							font-size: 13px;
	        							color:#696969;'>".$text3.": <b>".$vcodeOrNewPass."</b> </p>
	       						</td>
	      					</tr>
	      					<tr>
	       						<td colspan='4' align='center' style='
	       							padding:23px;
	       							background-color:#00BFFF; 
	       							color:#fff; 
	       						font-size:10px;'>
	        						<p style='margin: 0 15px 0 0;'>Powered by HiFree community 2017.</p>
	       						</td>
	      					</tr>
	      				</tbody>";
	    
		   $message .= "</table>";
		   $message .= "</td></tr>";
		   $message .= "</table>";
		   $message .= "</body></html>";
		   // HTML email ends here
		   
		   
		    $mail->IsSMTP(); 
		    $mail->isHTML(true);
		    $mail->SMTPDebug  = 0;                     
		    $mail->SMTPAuth   = true;                  
		    $mail->SMTPSecure = "ssl";                 
		    $mail->Host       = "smtp.gmail.com";      
		    $mail->Port       = 465;             
		    $mail->AddAddress($reciever);
		    $mail->Username   =	"sample.email.free@gmail.com";  
		                         $mail->Password   ="sajulan143";            
		                         $mail->SetFrom('sample.email.free@gmail.com','Hifree');
		                         $mail->AddReplyTo("sample.email.free@gmail.com","Hifree");
		    $mail->Subject    = $subject;
		    $mail->Body    = $message;
		    $mail->AltBody    = $message;
	   		$mail->Send();
	}

	function checkEmail(){
		global $connect;
			$email = $_POST['txtuseremail'];
		    $check_query = $connect->prepare('SELECT userEmail from tbl_user where userEmail = ?');
		    $check_query->execute(array($email));
		    $rows = $check_query->fetchAll();
		    $num_rows = count($rows);
			    if($num_rows > 0 ){
			        echo 'false';
			    } else {
			        echo 'true';
			    }
	}
	function checkUsername(){
		global $connect;
			$username = $_POST['txtusername'];
			$check_query = $connect->prepare('SELECT userName from tbl_user where userName = ?');
			$check_query->execute(array($username));
			$rows = $check_query->fetchAll();
			$num_rows = count($rows);
				if($num_rows > 0){
					echo "false";
				} else {
					echo "true";
				}
	}

	function insertToLoginHistory($getUuserId){
		global $connect;
			$checkExistingHistory = $connect->prepare('SELECT * from tbl_loginHistory where userId = :getId');
			$checkExistingHistory->execute(
				array(
					':getId'	=>	$getUuserId
				)
			);
			$row = $checkExistingHistory->rowcount();
			if($row > 0){
				$updateToLoginHistory = $connect->prepare('UPDATE tbl_loginHistory set loginStatus = :getStatus where userId = :getUserId');
				$updateToLoginHistory->execute(
					array(
						':getStatus' => 'online', 
						':getUserId' => $getUuserId
					)
				);
			}else{
				$insertToLoginHistory = $connect->prepare('INSERT into tbl_loginHistory(loginStatus,userId) values (:getStatus,:getUserId)');
				$insertToLoginHistory->execute(
					array(
						':getStatus' => 'online', 
						':getUserId' => $getUuserId
					)
				);
			}

	}
	

?>
