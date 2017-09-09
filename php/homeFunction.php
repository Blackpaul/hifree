<?php  
	$chome = new chome($connect);

	class chome{
		private $db;

			function __construct($connect){
				$this->db = $connect; 
			}
				//POST SECTION-------------->
				//post message
				public function postMsgfunction($getyourpost,$getuserid){
					$postMsgQuery = $this->db->prepare('INSERT into tbl_userPost(postMsg,userId) values (:savePostMsg, :saveUserId)');
					$postMsgQuery->execute(
						array(
							":savePostMsg"  => $getyourpost,
							":saveUserId"	=> $getuserid
						)
					);
				}


				//display user post
				public function displayPostfunction(){
					$dynamicScriptCall = $this->dynamicScript();

					$displayPostQuery = $this->db->prepare('SELECT * from tbl_userPost order by postId desc');
					$displayPostQuery->execute();
					$i = 0;
					while($row = $displayPostQuery->fetch(PDO::FETCH_OBJ)){
						$getUsername = $this->db->prepare('SELECT userName from tbl_user where userId = :postUserid');
						$getUsername->execute(
							array(
								":postUserid" => $row->userId
							)
						);
						$displayUserPhotoQuery = $this->db->prepare('SELECT photoName from tbl_userPhoto where userId = :getId order by photoId desc limit 1');
						$displayUserPhotoQuery->execute(
							array(
								':getId' => $row->userId,
							)
						);
							 
							$username = $getUsername->fetchColumn();
							$Photo = $displayUserPhotoQuery->fetchColumn();
							$dt = new DateTime($row->postDate);
							//echo $dt->format('M j Y g:i A') . "<br />";
							//echo $row->postMsg . " " . $i++;
								if (substr($Photo, 0, 3) == "../"){
									$divPictureOption = "<div class='div-display-pic-img' style='background-image: none;'></div>";
								}else{
									$divPictureOption = "<div class='div-display-pic-img' ></div>";
								}

						
					
						echo "<div class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 div-display-post'>";
						echo  	"<div class='div-display-action'><i class='pull-right glyphicon glyphicon-chevron-down php-user-arrow' name='".$row->userId."' id='".$row->postId."'></i></div>";
						echo 	"<div class='col-xl-1 col-lg-1 col-md-1 col-sm-1 col-xs-2 div-display-pic'>"; 
						echo 		$divPictureOption;
						echo 		"<div class='div-display-pic-img-main' style='background-image: url(".$Photo.");'></div>";
						echo 	"</div>";
						echo 	"<div class='col-xl-10 col-lg-10 col-md-10 col-sm-10 col-xs-10 div-display-nameDate'>";
						echo  		"<div class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 div-display-name'>&nbsp" . $username ."</div>";			
						echo  		"<div class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 div-display-date'>&nbsp". $dt->format('M j Y g:i A') ."</div>";	
						echo 	"</div>";
						echo  	"<div class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 div-display-msg'>". $row->postMsg ."</div>";
						echo "</div>";
						
					}
				}

				//javascript function
				public function dynamicScript(){
					//if ($_SESSION['clientPostUserId'] == $_SESSION['loginuserid']) {
						$sessLogId = $_SESSION['loginuserid'];
					
					
						echo '<script type="text/javascript">';	//----->start script
						echo '$(document).ready(function(){';	//----->start document ready
						echo 	'$(".php-user-arrow").click(function(){';	//arrow action start
						echo 		'$(".kanitext").val($(this).attr("id"));';
						echo 		'var userId = $(this).attr("name");';
						echo 		'var sessionLogId = '.$sessLogId.';';	
						echo 			'if (userId == sessionLogId){';				
						echo 				'var alist = "<ul><li><a class=hide-your-post-action href=>Hide your post</a></li></ul>";';					
						echo 			'}else{';				
						echo 				'var alist = "<ul><li><a class=unfollow-action href=>Unfollow</a></li><li><a class=hide-post-action href=>Hide post</a></li></ul>";';					
						echo 			'}';							
						echo 			'$(".div-post-action").css({top: event.clientY, left: event.clientX}).show();';					
						echo			'$(".div-post-action").css({"left": "-=120px", "top": "+=15px"});';
						echo			'$(".div-post-action").empty().append(alist);';
						echo 	'});';	//arrow action end
						echo 	'$(".hide-your-post-action").click(function(event){'; //hide your post on arrow action start
						echo 		'event.preventDefault();';			
						echo 			'alert("testing");';				
						echo 	'});';	//hide your post on arrow action end
						echo '});';		  //----->end document ready
						echo '</script>'; //----->end script
				}

				//END POST SECTION-------------->

				public function displayOnlineUser(){
					$displayOnlineUserQuery = $this->db->prepare('SELECT (tbl_user.userName) as username,(tbl_user.userId) as ownId from tbl_user inner join tbl_loginHistory on tbl_user.userId = tbl_loginHistory.userId where tbl_loginHistory.loginStatus = :status and not tbl_user.userId = :ownId order by tbl_user.userName');
					$displayOnlineUserQuery->execute(
						array(
							':status'	=>	'online',
							':ownId'	=>	$_SESSION['loginuserid']
						)
					);
					while($row = $displayOnlineUserQuery->fetch(PDO::FETCH_OBJ)){
						$displayUserPhotoQuery = $this->db->prepare('SELECT photoName from tbl_userPhoto where userId = :getId order by photoId desc limit 1');
						$displayUserPhotoQuery->execute(
							array(
								':getId' => $row->ownId,
							)
						);
						$Photo = $displayUserPhotoQuery->fetchColumn();
							echo "<div class='col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 div-display-user'>";
							echo 	"<div class='div-display-user-img pull-left' style='background-image: url(".$Photo.");'></div>";
							echo 	"<p>".ucfirst($row->username)."</p>";
							echo "</div>";
					}
				}

				//USER INFO SECTION------------->
				//delete contacts
				public function deleteContacts($getUserId,$getUserContact){
					$checkContactQuery = $this->db->prepare('SELECT * from tbl_userContacts where contactEmail = :getContactEmail or contactNo = :getContactNo and userId = :getId');
					$checkContactQuery->execute(
						array(
							':getContactEmail' 	=>  $getUserContact,
							':getContactNo'		=>	$getUserContact,
							':getId'			=>	$getUserId
						)
					);

					while($row = $checkContactQuery->fetch(PDO::FETCH_OBJ)){
						if ($row->primaryCon == 'yes'){
							echo "<p style='color: #ff0000;top:5px;position:relative;'>Err: primary contact can't be remove!</p>";
						}else{
							$deleteContactQuery = $this->db->prepare('DELETE from tbl_userContacts where contactId = :getconId');
							$deleteContactQuery->execute(
								array(
									':getconId' => $row->contactId
								)
							);
							echo "<p style='color: #0ae;top:5px;position:relative;'>Successfully deleted.</p>";
						}
					}
				}
				//add user photo
				public function uploadPhoto($getUserId){
					if($_FILES['file']['name'] != ''){
						$test = explode('.', $_FILES['file']['name']);
						$extention = end($test);
						$newName = rand(100, 999). "." .$extention;
							if (!is_dir('../../image/client_img/'.$getUserId)) {
								mkdir('../../image/client_img/'.$getUserId);	
							}	
								$location = '../../image/client_img/'.$getUserId.'/'.$newName;
								$saveToFormat = '../image/client_img/'.$getUserId.'/'.$newName;
								move_uploaded_file($_FILES['file']['tmp_name'], $location);

						$insertPhotoQuery = $this->db->prepare('INSERT into tbl_userPhoto(photoName,userId) values (:savePhoto, :saveUserId)');
						$insertPhotoQuery->execute(
							array(
								":savePhoto"  	=> $saveToFormat,
								":saveUserId"	=> $getUserId
							)
						);

				   	 	echo "Save changes";
					}else{
						echo "waaa";
					}
				}

				public function displayUserPhoto($getUserId){
					$displayUserPhotoQuery = $this->db->prepare('SELECT * from tbl_userPhoto where userId = :getId order by photoId desc limit 1');
					$displayUserPhotoQuery->execute(
						array(
							':getId' => $getUserId,
						)
					);
					while ($row = $displayUserPhotoQuery->fetch(PDO::FETCH_OBJ)) {
						echo $row->photoName;
					}
				}

				//display all contacts
				public function displayAllContacts($getUserId){
					$displayAllContactsQuery = $this->db->prepare('SELECT * FROM ( SELECT contactEmail FROM tbl_userContacts where userId = :getId and contactEmail is not null UNION ALL SELECT contactNo FROM tbl_userContacts where userId = :getId and contactNo is not null) as val');
					$displayAllContactsQuery->execute(
						array(
							':getId'	=> $getUserId
						)
					);
					$result = $displayAllContactsQuery->fetchAll(PDO::FETCH_NUM);
						echo json_encode($result);
						
				}
				//display all contacts end-------------->

				public function displaySecodaryContact($getUserId){
					$displaySecodaryContactQuery = $this->db->prepare('SELECT * FROM ( SELECT contactEmail as secondary FROM tbl_userContacts where secondaryCon = :getVal and userId = :getId and contactEmail is not null UNION ALL SELECT contactNo FROM tbl_userContacts where secondaryCon = :getVal and userId = :getId and contactNo is not null ) as val');
					$displaySecodaryContactQuery->execute(
						array(
							':getVal' =>  'yes',
							':getId' =>  $getUserId
						)
					);
					while($row = $displaySecodaryContactQuery->fetch(PDO::FETCH_OBJ)){
						echo $row->secondary;
					}
				}

				//update full name
				public function updateUserInfo($receiveFname,$receiveMname,$receiveLname,$receiveUserId,$updateOn){
						if($updateOn == "fullName"){
							$checkExisting = $this->checkExistingUser($receiveUserId);
							if ($checkExisting == "InSert"){
								if ($receiveFname == "" or $receiveMname == "" or $receiveLname == ""){
									echo "<p style='color: #ff0000;top:5px;position:relative;'>Invalid entry, please check responsibly.</p>";
								}else{
									$addNameQuery = $this->db->prepare('INSERT into tbl_userName(userFname,userMname,userLname,userId) values (:saveUserFname, :saveUserMname, :saveUserLname, :saveUserId)');
									$addNameQuery->execute(
										array(
											":saveUserFname" 	=> $receiveFname,
											":saveUserMname" 	=> $receiveMname,
											":saveUserLname" 	=> $receiveLname,
											":saveUserId" 		=> $receiveUserId
										)
									);
									
									echo "<p style='color: #0ae;top:5px;position:relative;'>Changes save.</p>";
								}
							}else {
								if ($receiveFname == "" or $receiveMname == "" or $receiveLname == ""){
									echo "<p style='color: #ff0000;top:5px;position:relative;'>Invalid entry, please check responsibly.</p>";
								}else{
									$updateNameQuery = $this->db->prepare('UPDATE tbl_userName set userFname = :saveUserFname, userMname = :saveUserMname, userLname = :saveUserLname where userId = :getUserId');
									$updateNameQuery->execute(
										array(
											":saveUserFname" 	=> $receiveFname,
											":saveUserMname" 	=> $receiveMname,
											":saveUserLname" 	=> $receiveLname,
											":getUserId" 		=> $receiveUserId
										)
									);
									echo "<p style='color: #0ae;top:5px;position:relative;'>Changes save.</p>";
								}
							}
						}
				}	

				//update full name[checking user]
				public function checkExistingUser($getSessionUserid){
					$checkQuery = $this->db->prepare('SELECT * from tbl_userName where userId = :getId');
					$checkQuery->execute(
						array(
								":getId"	=> $getSessionUserid
							)
					);
					$checkRowExist = $checkQuery->rowcount();
						if($checkRowExist > 0){
							$msg = "UpDate";
						}else {
							$msg = "InSert";
						}
					return $msg;
				}

				//update display full name
				public function displayFullName($getUserId){
					$displayFullNameQuery = $this->db->prepare('SELECT * from tbl_userName where userId = :getId');
					$displayFullNameQuery->execute(
						array(
							":getId"	=>	$getUserId
						)
					);
						while($row = $displayFullNameQuery->fetch(PDO::FETCH_OBJ)){
							$fullName = json_encode(array(
								'firtName' 	=> $row->userFname, 
								'middleName' => $row->userMname,
								'lastName' => $row->userLname
							));
						}
						return $fullName;
				}

				//update checking username availability
				public function checkUsername($getUserName,$getSessionUserName) {
					$checkUsernameQuery = $this->db->prepare('SELECT * from tbl_user where userName = :getName');
					$checkUsernameQuery->execute(
						array(
							":getName"	=>	$getUserName
						)
					);
						$row = $checkUsernameQuery->rowcount();
							if($row > 0){
								if($getSessionUserName == $getUserName){
									$msg =  "0"; //Current username
								}else{
									$msg =  "1"; //Username Already taken
								}
								
							}else {
								if ($getUserName == "") {
									$msg = "2"; //text field empty
								}else{
									$msg = "3"; //Available
								}
							}
						return $msg;
				}

				//update username
				public function updateUsername($getUsername,$getuserid){
					$updateUsernameQuery = $this->db->prepare('UPDATE tbl_user set userName = :getName where userId = :getId');
					$updateUsernameQuery->execute(
						array(
							':getName'	=>	$getUsername,
							':getId'	=>	$getuserid
						)
					);
					$_SESSION['loginusername'] = $getUsername;
					echo "<p style='color: #0ae;top:5px;position:relative;'>Successfully updated.</p>";
				}

				//update checking email availability
				public function checkEmail($getEmail,$getSessionEmail) {
					$checkEmailQuery = $this->db->prepare('SELECT * from tbl_userContacts where contactEmail = :getEmail');
					$checkEmailQuery->execute(
						array(
							":getEmail"	=>	$getEmail
						)
					);
						$row = $checkEmailQuery->rowcount();
							if($row > 0){
								if($getSessionEmail == $getEmail){
									$msg =  "0"; //Current username
								}else{
									$msg =  "1"; //Username Already taken
								}

							}else{
								if ($getEmail == "") {
									$msg = "2"; //text field empty
								}else{
									$msg = "3"; //Available
								}
							}
						return $msg;
				}

				//update checking phone number availability
				public function checkPhoneNo($getPhoneNo) {
					$checkPhoneQuery = $this->db->prepare('SELECT * from tbl_userContacts where contactNo = :getPhone');
					$checkPhoneQuery->execute(
						array(
							":getPhone"	=>	$getPhoneNo
						)
					);
						$row = $checkPhoneQuery->rowcount();
							if($row > 0){
								$msg =  "1"; //Username Already taken
							}else{
								if ($getPhoneNo == "") {
									$msg = "2"; //text field empty
								}else{
									$msg = "3"; //Available
								}
							}
						return $msg;
				}


				//update update user contacts
				public function updateUserContact($getNewCon,$getsaveTo,$getuserid){
					$checkLimitQuery = $this->db->prepare('SELECT count(*) FROM tbl_userContacts WHERE userId = :getId');
					$checkLimitQuery->execute(
						array(
							':getId'	=>$getuserid
						)
					);
						$rowCount = $checkLimitQuery->fetchColumn(0);
							if ($rowCount > 5){
								echo "<p style='color: #ff0000;top:5px;position:relative;'>Err: maximum contact exceed!</p>";
							}else{
								if($getsaveTo == "PhoneNo"){
									$addNewContactQuery = $this->db->prepare('INSERT into tbl_userContacts(contactNo ,userId) values (:newCon, :userId)');
								}else{
									$addNewContactQuery = $this->db->prepare('INSERT into tbl_userContacts(contactEmail ,userId) values (:newCon, :userId)');
								}
								$addNewContactQuery->execute(
									array(
										":newCon"	=> $getNewCon,
										":userId"	=> $getuserid
									)
								);
								echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
							}
				}


				//update update user contacts primary secondary
				public function updateUserContactPrimSec($getNewCon,$getTodo,$getsaveTo,$getuserid){
					$checkLimitQuery = $this->db->prepare('SELECT count(*) FROM tbl_userContacts WHERE userId = :getId');
					$checkLimitQuery->execute(
						array(
							':getId'	=> $getuserid
						)
					);
					$rowCount = $checkLimitQuery->fetchColumn(0);
					if ($rowCount > 5){
						$dataResult = json_encode(array(
							'msg' => '<p style="color: #ff0000;top:5px;position:relative;">Err: maximum contact exceed!</p>'

						));
					}else{
						if($getTodo == 'Update'){
							if($getsaveTo != "PhoneNo"){
								$updateUserContactQuery = $this->db->prepare('UPDATE tbl_user set userEmail = :newConEmail where userId = :getId');
								$updateUserContactQuery->execute(
									array(
										':newConEmail'	=>	$getNewCon,
										':getId'		=>	$getuserid
									)
								);
									if($updateUserContactQuery){
										$checkExistingPrimeCon = $this->db->prepare('SELECT * from tbl_userContacts where primaryCon = :curStatus and userId = :getId');
										$checkExistingPrimeCon->execute(
											array(
												':curStatus'	=>	'yes',
												':getId'		=>	$getuserid
											)
										);
										$row = $checkExistingPrimeCon->rowcount();
											if($row > 0){
												while($row = $checkExistingPrimeCon->fetch(PDO::FETCH_OBJ)){
													$conID = $row->contactId;
													$No = 'prime';
													$toSave = 'contactEmail';
													$whatOrdNo = 'Primary';
													$CallupdateContactQuery = $this->callUpdateQuery($conID,$No);
													$executeQuery = $this->callInsertQuery($getNewCon,$getuserid,$toSave,$whatOrdNo);
												}	
											}	
									}
										$_SESSION['loginuseremail'] = $getNewCon;
										$dataResult = json_encode(array(
											'newEmail' 	=> $getNewCon, 
											'msg' => "<p style='color: #0ae;top:5px;position:relative;'>Successfully updated</p>"
										));
							}
								return $dataResult;
						}else if($getTodo == 'PhonePrime'){
							$dataResult = json_encode(array(
								'msg' => '<p style="color: #ff0000;top:5px;position:relative;">Phone feature is not yet available.</p>' 
							));
						}
						else{
							if($getsaveTo == "PhoneNo"){
								$checkExistingSeconCon = $this->db->prepare('SELECT * from tbl_userContacts where secondaryCon  = :curStatus and userId = :getId');
								$checkExistingSeconCon->execute(
									array(
										':curStatus'	=>	'yes',
										':getId'		=>	$getuserid
									)
								);
									$row = $checkExistingSeconCon->rowcount();
										if ($row > 0){
											while($row = $checkExistingSeconCon->fetch(PDO::FETCH_OBJ)){
												$conID = $row->contactId;
												$No = 'second';
												$toSave = 'Phone';
												$whatOrdNo = 'Secondary';
												$CallupdateContactQuery = $this->callUpdateQuery($conID,$No);
												$executeQuery = $this->callInsertQuery($getNewCon,$getuserid,$toSave,$whatOrdNo);
											}	
												$dataResult = json_encode(array(
													'msg' => 'Done'
												));
										}else{
											$toSave = 'Phone';
											$whatOrdNo = 'Secondary';
											$executeQuery = $this->callInsertQuery($getNewCon,$getuserid,$toSave,$whatOrdNo);
												$dataResult = json_encode(array(
													'msg' => '<p style="color: #0ae;top:5px;position:relative;">Inserted</p>'
												));
										}
							}else{
								$checkExistingSeconCon = $this->db->prepare('SELECT * from tbl_userContacts where secondaryCon  = :curStatus and userId = :getId');
								$checkExistingSeconCon->execute(
									array(
										':curStatus'	=>	'yes',
										':getId'		=>	$getuserid
									)
								);
									$row = $checkExistingSeconCon->rowcount();
										if ($row > 0){
											while($row = $checkExistingSeconCon->fetch(PDO::FETCH_OBJ)){
												$conID = $row->contactId;
												$No = 'second';
												$toSave = 'contactEmail';
												$whatOrdNo = 'Secondary';
												$CallupdateContactQuery = $this->callUpdateQuery($conID,$No);
												$executeQuery = $this->callInsertQuery($getNewCon,$getuserid,$toSave,$whatOrdNo);
											}	
												$dataResult = json_encode(array(
													'msg' => 'Done'
												));
										}else{
											$toSave = 'contactEmail';
											$whatOrdNo = 'Secondary';
											$executeQuery = $this->callInsertQuery($getNewCon,$getuserid,$toSave,$whatOrdNo);
												$dataResult = json_encode(array(
													'msg' => '<p style="color: #0ae;top:5px;position:relative;">Inserted</p>'
												));
										}
							}	
						}
					}
					return $dataResult;
				}
				

				//update call insert query
				public function callInsertQuery($getNewCon,$getuserid,$toSave,$whatOrdNo){
					if ($toSave == 'contactEmail' && $whatOrdNo == 'Primary'){
						$insertNewCon = $this->db->prepare('INSERT into tbl_userContacts(contactEmail,primaryCon,userId) values (:getNewCon,:getStatus,:getId)');
					}
					else if ($toSave == 'contactEmail' && $whatOrdNo == 'Secondary'){
						$insertNewCon = $this->db->prepare('INSERT into tbl_userContacts(contactEmail,secondaryCon,userId) values (:getNewCon,:getStatus,:getId)');
					}else {
						$insertNewCon = $this->db->prepare('INSERT into tbl_userContacts(contactNo,secondaryCon,userId) values (:getNewCon,:getStatus,:getId)');
					}
						$insertNewCon->execute(
							array(
								':getNewCon'		=>	$getNewCon,
								':getStatus'		=>	'yes',
								':getId'				=>	$getuserid
							)
						);
				}
				
				//update contact info
				public function updateContactInfo($getNewCon,$gettoMake,$getIdentifier,$getTableCount,$getContactId,$getuserid){
					if ($gettoMake == "noThing"){
						if($getIdentifier == "Email"){
							if($getTableCount == "1") {
								$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_userContacts set contactEmail = :newCon where contactId = :getconId and userId = :getId');
								$updateContactInfoQuery->execute(
									array(
										':newCon' 	=> $getNewCon, 
										':getconId' => $getContactId, 
										':getId' 	=> $getuserid,
									)
								);
								echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
							}else{
								$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_user,tbl_userContacts set tbl_user.userEmail = :newCon, tbl_userContacts.contactEmail = :newCon where tbl_user.userId = :getId and tbl_userContacts.userId = :getId and tbl_userContacts.contactId = :getconId');
								$updateContactInfoQuery->execute(
									array(
										':newCon' 	=> $getNewCon, 
										':getconId' => $getContactId, 
										':getId' 	=> $getuserid,
									)
								);
								$_SESSION['loginuseremail'] = $getNewCon;
								echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";

							}
						}else{
							if($getTableCount == "1") {
								$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_userContacts set contactNo = :newCon where contactId = :getconId and userId = :getId');
								$updateContactInfoQuery->execute(
									array(
										':newCon' 	=> $getNewCon, 
										':getconId' => $getContactId, 
										':getId' 	=> $getuserid,
									)
								);
								echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
							}else{
								echo "<p style='color: #ff0000;top:5px;position:relative;'>Phone system are not yet available.</p>"; //not available
							}
						}
					}else if($gettoMake == "Primary") {
						$checkExistingPrimeCon = $this->db->prepare('SELECT * from tbl_userContacts where primaryCon = :curStatus and userId = :getId');
						$checkExistingPrimeCon->execute(
							array(
								':curStatus'	=>	'yes',
								':getId'		=>	$getuserid
							)
						);
						$row = $checkExistingPrimeCon->rowcount();
							if($row > 0){
								while ($row = $checkExistingPrimeCon->fetch(PDO::FETCH_OBJ)) {
									$No = 'prime';
									$CallupdateContactQuery = $this->callUpdateQuery($row->contactId,$No);

									$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_user,tbl_userContacts set tbl_user.userEmail = :newCon, tbl_userContacts.contactEmail = :newCon, tbl_userContacts.primaryCon = :newStatus where tbl_user.userId = :getId and tbl_userContacts.userId = :getId and tbl_userContacts.contactId = :getconId');
									$updateContactInfoQuery->execute(
										array(
											':newCon' 		=> $getNewCon, 
											':newStatus' 	=> 'yes', 
											':getconId' 	=> $getContactId, 
											':getId' 		=> $getuserid,
										)
									);
									$_SESSION['loginuseremail'] = $getNewCon;
									echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
								}
							}else{
								$checkExistingSeconCon = $this->db->prepare('SELECT contactId from tbl_userContacts where secondaryCon = :curStatus and userId = :getId');
								$checkExistingSeconCon->execute(
									array(
										':curStatus'	=>	'yes',
										':getId'		=>	$getuserid
									)
								);
								$contactId = $checkExistingSeconCon->fetchColumn();

								$No = 'second';
								$CallupdateContactQuery = $this->callUpdateQuery($contactId,$No);

								$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_user,tbl_userContacts set tbl_user.userEmail = :newCon, tbl_userContacts.contactEmail = :newCon, tbl_userContacts.primaryCon = :newStatus where tbl_user.userId = :getId and tbl_userContacts.userId = :getId and tbl_userContacts.contactId = :getconId');
									$updateContactInfoQuery->execute(
										array(
											':newCon' 		=> $getNewCon, 
											':newStatus' 	=> 'yes', 
											':getconId' 	=> $getContactId, 
											':getId' 		=> $getuserid,
										)
									);
									$_SESSION['loginuseremail'] = $getNewCon;
								echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
							}
					}else if($gettoMake == "Secondary") {
						$checkExistingSeconCon = $this->db->prepare('SELECT * from tbl_userContacts where secondaryCon  = :curStatus and userId = :getId');
						$checkExistingSeconCon->execute(
							array(
								':curStatus'	=>	'yes',
								':getId'		=>	$getuserid
							)
						);
							$row = $checkExistingSeconCon->rowcount();
								if ($row > 0){
									while ($row = $checkExistingSeconCon->fetch(PDO::FETCH_OBJ)) {
										$No = 'second';
										$CallupdateContactQuery = $this->callUpdateQuery($row->contactId,$No);
										if ($getIdentifier == 'Email'){
											$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_userContacts set contactEmail = :newCon, secondaryCon = :newSecStatus where contactId = :getconId and userId = :getId');
										}else{
											$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_userContacts set contactNo = :newCon, secondaryCon = :newSecStatus where contactId = :getconId and userId = :getId');
										}
										$updateContactInfoQuery->execute(
											array(
												':newCon' 			=> $getNewCon, 
												':newSecStatus' 	=> 'yes', 
												':getconId' 		=> $getContactId, 
												':getId' 			=> $getuserid,
											)
										);	
									}
									echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
								}else{
										if ($getIdentifier == 'Email'){
											$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_userContacts set contactEmail = :newCon, secondaryCon = :newSecStatus where contactId = :getconId and userId = :getId');
										}else{
											$updateContactInfoQuery = $this->db->prepare('UPDATE tbl_userContacts set contactNo = :newCon, secondaryCon = :newSecStatus where contactId = :getconId and userId = :getId');
										}
										$updateContactInfoQuery->execute(
											array(
												':newCon' 			=> $getNewCon, 
												':newSecStatus' 	=> 'yes', 
												':getconId' 		=> $getContactId, 
												':getId' 			=> $getuserid,
											)
										);	
										echo "<p style='color: #0ae;top:5px;position:relative;'>New update has been save.</p>";
								}
					}
				}

				//update call Update query 
				public function callUpdateQuery($conID,$No){
					if ($No == 'prime'){
						$updateContactQuery = $this->db->prepare('UPDATE tbl_userContacts set primaryCon = :updateCon where contactId = :getconId');
					}
					else if($No == 'second'){
						$updateContactQuery = $this->db->prepare('UPDATE tbl_userContacts set secondaryCon = :updateCon where contactId = :getconId');
					}
					$updateContactQuery->execute(
						array(
							':updateCon'	=>	'NULL',
							':getconId'			=>	$conID
						)
					);
				}

				public function displayUserContactId($getUserContact,$getContactTodo){
					if ($getContactTodo == 'Email'){
						$displayUserContactIdQuery = $this->db->prepare('SELECT * from tbl_userContacts where contactEmail = :getContact');
					}else{
						$displayUserContactIdQuery = $this->db->prepare('SELECT * from tbl_userContacts where contactNo = :getContact');
					}
					$displayUserContactIdQuery->execute(
						array(
							':getContact'			=>	$getUserContact
						)
					);
					while($row = $displayUserContactIdQuery->fetch(PDO::FETCH_OBJ)){
						echo $row->contactId;
					}
					
				}
				//END USER INFO SECTION------------->

				

			
	}	
?>

