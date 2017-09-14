	var globalDataUsername = "0";
	var globalDataEmailUpdate = "";
	var globalTodo = ""; 
	var globalContactData = "6";
	var globalSessionUserId = ""; //session id 
	var globalSelectOption = "";  //sa selection list email or phone
	var globalSelectDummy = ""; //dummy val compare to edit input value
	var globalSelectGivenNumber = "";
	var globalSelectGivenNumber2update = ""; //primary or secondary update
	var globalKnowingAction = "";
	var globalAction = "";
	var globalUserContactId = "";

$(document).ready(function(){
	getUserId();
	getUserPhoto();
	refreshdiv();
	displayOnlineUser();
	goOnline();

	window.addEventListener("beforeunload", function (event) {
    	goOffline();    
	});


//keypress enter key
//	$(document).keypress(function(e) {
//		if(e.which == 13) {
//			alert('you hit me');
//		}
//	});
	
	//scrollong event
	document.addEventListener('scroll', function (event) {
		if (event.target.id === 'scrollbar-id') {       
			$('.div-post-action').hide();
		}
	}, true );
	//body click event
	$('body').click(function(evt){    
		if(!$(evt.target).is('.php-user-arrow')) {
    		$('.div-post-action').hide();
 		}
	});

	//offcanvas 
	$('.side-bar-button').click(function(){
		$('.side-nav').toggleClass('side-nav-view');
	});
	$('#click-b').click(function() {
		$('.side-nav').removeClass('side-nav-view');
	});

	//offcanvas toggle out
	$('.div-content').click(function(){
		$('.side-nav').removeClass('side-nav-view');
	});
	$(window).resize(function() {
		if ($(window).width() > 768) {
			$('.side-nav').removeClass('side-nav-view');
		}
		$('.div-post-action').hide();
	});

	$('.eraseDummySession').click(function(){
		globalSessionUserId = "";
	});
	//search button
	$('.gly-btn-search').click(function(){
		alert('yes');
	});

	//post button
	$('.post-btn').click(function(){
		if ($('.modified-textarea').val() != ""){
			$.ajax({
				url: '../php/z-home/yourPost.php',
				type: 'POST',
				data: {
					yourPost: $('.modified-textarea').val(),
					userId: globalSessionUserId
				},
				cache: false,
				beforeSend: function(){
				},
				success: function(data){
					$('.modified-textarea').val("");
				}
			});
		}
	});

	//click show update modal
	$('.user-info-mdl-show').click(function(){
		$('.user-info-mdl').modal('show');
			//display all user info
	});
	
	//USER INFO SECTION -------------------------------->>
	//upload picture
	$('.get-file-name').change(function(){
		var property = $('.get-file-name')[0].files[0];
		var image_name = property.name;
		var image_extension = image_name.split('.').pop().toLowerCase();

	
		if(jQuery.inArray(image_extension, ['png','jpg','jpeg']) == -1){
			 $('.get-file-name').val("");
			$('.err-text').text('Invalid picture, we only accept png and jpg format thank you.').css({'color':'#ff3232'});
		}else{
			readURL(this);
			$('.mdl-preview-pic').modal('show');
		}
        
        	
    });

	$('.btn-save-img').click(function(){
		var property = $('.get-file-name')[0].files[0];
		var image_name = property.name;
		var image_extension = image_name.split('.').pop().toLowerCase();
		var image_size = property.size;

		if(jQuery.inArray(image_extension, ['png','jpg','jpeg']) == -1){
			alert('invalid file');
		}else{
			var	form_data = new FormData();
			form_data.append("file", property);
			form_data.append("sendUserId", globalSessionUserId);
				$.ajax({
					url: '../php/z-home/uploadPhoto.php',
					method: "POST",
					data: form_data,
					contentType: false,
					cache: false,
					processData: false,
					success: function(data){
						getUserPhoto();
						$('.mdl-preview-pic, .mdl-change-pic').modal('toggle');
					}
				});
		}

	});

	//save update name
	$('.btn-save-update').click(function(){
		var getUserId = globalSessionUserId;

		$.ajax({
				url: '../php/z-home/updateName.php',
				type: 'POST',
				data: $('.user-info').serialize() + "&sendUserId=" + getUserId,
				cache: false,
				beforeSend: function(){},
				success: function(data){
					$('.err-msg').fadeIn('fast').append(data);
					setTimeout(function() {
						$('.err-msg').hide('fast').text('');							
					}, 2000);
				}
			});
	});

	//check existing username
	$('.currentUsernametxt').keyup(function(){
		$.ajax({
			url: '../php/z-home/checkUsername.php',
			type: 'POST',
			data: {sendUsername: $.trim($(this).val())},
			cache: false,
			beforeSend: function(){},
			success: function(data){
				var getDataResult = $.trim(data);
				if(getDataResult == 0) {
					$('.p-current').text('Current username.').css({'color':'#fff'});
					globalDataUsername = getDataResult;
				}else if(getDataResult == 1){
					$('.p-current').text('Username taken.').css({'color':'#ff3232'});
					globalDataUsername = getDataResult;
				}else if(getDataResult == 2){
					$('.p-current').text('Invalid entry.').css({'color':'#ff3232'});
					globalDataUsername = getDataResult;
				}else if(getDataResult == 3){
					$('.p-current').text('Available.').css({'color':'#4c4cff'});
					globalDataUsername = getDataResult;
				}else{
					$('.p-current').text('Error Reload Browser!').css({'color':'#ff3232'});
				}
				
			}
		});
	});

	//update username 
	$('.btn-detail-save').click(function(){
		if(globalDataUsername == 3){
			$.ajax({
				url: '../php/z-home/updateUsername.php',
				type: 'POST',
				data: { sendUsername: $.trim($('.currentUsernametxt').val()), sendUserId: globalSessionUserId},
				beforeSend: function(){},
				success: function(data){
					$('.err-msg').fadeIn('fast').append(data);
					setTimeout(function() {
						$('.err-msg').hide('fast').text('');
							globalDataUsername = "0";
							$('.p-current').text('Changes save.').css({'color':'#fff'});								
					}, 2000);
					

				}
			})
		}else if(globalDataUsername == 0){
			$('.p-current').text('Kindly use other username.').css({'color':'#fff'});
		}else{
			$('.p-current').text('Invalid entry!').css({'color':'#ff3232'});
		}
	});


	//on insert tab check existing email/contact number
	$('.add-new-inp').keyup(function(){
		var addNewCon = $(this).val();
		var haveAT = addNewCon.indexOf("@");
		var haveDOT = addNewCon.lastIndexOf(".");

			if (haveAT<1 || haveDOT<haveAT+2 || haveDOT+2>=addNewCon.length){
				if($.isNumeric(addNewCon)){
					if(/^\d{11}$/.test(addNewCon)){
						//textbox input = phone number format
						$.ajax({
							url: '../php/z-home/checkPhoneNo.php',
							type: 'POST',
							data: {sendPhone: $.trim($(this).val())},
							cache: false,
							beforeSend: function(){},
							success: function(data){
								var getDataResult = $.trim(data);
									if(getDataResult == 1){
										$('.div-anp').show();
										$('.anp-current').text('Phone already taken.').css({'color':'#ff3232'});
										$('.prim-sec-rbtn').hide();
										globalContactData = getDataResult;
										
									}else if(getDataResult == 2){
										$('.div-anp').show();
										$('.anp-current').text('Invalid entry.').css({'color':'#ff3232'});
										$('.prim-sec-rbtn').hide();
										globalContactData = getDataResult;
										
									}else if(getDataResult == 3){
										$('.div-anp').show();
										$('.anp-current').text('Contact available.').css({'color':'#0ae'});
										$('.prim-sec-rbtn').show();
										globalTodo = "PhoneNo";
										globalContactData = getDataResult;
										
									}else{
										$('.anp-current').text('Error Reload Browser!').css({'color':'#ff3232'});
										$('.div-anp').hide();
										$('.prim-sec-rbtn').hide();
									}
							}
						});
					}
					else{
						$('.div-anp').show();
						$('.anp-current').text('Phone number should be 11 digit.').css({'color':'#ff3232'});
						$('.prim-sec-rbtn').hide();
						globalContactData = "5"
					}
				}else{
					if (addNewCon == ""){
						$('.div-anp').show();
						$('.anp-current').text('Add new contact.').css({'color':'#ff3232'});
						$('.prim-sec-rbtn').hide();
						globalContactData = "6"
					}else{
						$('.div-anp').show();
						$('.anp-current').text('Invalid email address.').css({'color':'#ff3232'});
						$('.prim-sec-rbtn').hide();
						globalContactData = "5"
					}
					
				}
        	}else{
        		//textbox input = email address format
				$.ajax({
					url: '../php/z-home/checkEmail.php',
					type: 'POST',
					data: {sendEmail: $.trim($(this).val())},
					cache: false,
					beforeSend: function(){},
					success: function(data){
						var getDataResult = $.trim(data);
							if(getDataResult == 0) {
								$('.div-anp').show();
								$('.anp-current').text('User primary email.').css({'color':'#0ae'});
								$('.prim-sec-rbtn').hide();
								globalContactData = getDataResult;

							}else if(getDataResult == 1){
								$('.div-anp').show();
								$('.anp-current').text('Email exist.').css({'color':'#ff3232'});
								$('.prim-sec-rbtn').hide();
								globalContactData = getDataResult;
								
							}else if(getDataResult == 2){
								$('.div-anp').show();
								$('.anp-current').text('Invalid entry.').css({'color':'#ff3232'});
								$('.prim-sec-rbtn').hide();
								globalContactData = getDataResult;
								
							}else if(getDataResult == 3){
								$('.div-anp').show();
								$('.anp-current').text('Contact available.').css({'color':'#0ae'});
								$('.prim-sec-rbtn').show();
								globalTodo = "emailAddress";
								globalContactData = getDataResult;
								
							}else{
								$('.anp-current').text('Error Reload Browser!').css({'color':'#ff3232'});
								$('.div-anp').hide();
								$('.prim-sec-rbtn').hide();
							}
					}
				});
        	}
	});

	//update tab checking email/contact number
	$('.edi-con-inp').keyup(function(){
		var editInpVal = $.trim($(this).val());
		if (globalSelectOption == "Phone") {
			//phonen number
			$.ajax({
				url: '../php/z-home/checkPhoneNo.php',
				type: 'POST',
				data: {sendPhone: editInpVal},
				cache: false,
				beforeSend: function(){},
				success: function(data){
					var getDataResult = $.trim(data);
					if (getDataResult == 1){
						if (editInpVal == globalSelectDummy){
							if(editInpVal == $('.label-secon-con').text()){
								$('.div-anp').show();
								$('.anp-current').text('Secondary contact.').css({'color':'#ff3232'});
								$("#second").prop('checked', true);
								globalKnowingAction = "1";
								globalAction = "Secondary";
							}else{
								$('.div-anp').show();
								$('.anp-current').text('User current number.').css({'color':'#0ae'});
							}
						}else {
							$('.div-anp').show();
							$('.anp-current').text('Phone number not available.').css({'color':'#ff3232'});
						}
						globalSelectGivenNumber = "1";
					}else if(getDataResult == 2){
						$('.div-anp').show();
						$('.anp-current').text('Invalid entry').css({'color':'#ff3232'});
						globalSelectGivenNumber = "2";
					}else if(getDataResult == 3){
						if($.isNumeric(editInpVal)){
							if(/^\d{11}$/.test(editInpVal)){
								$('.div-anp').show();
								$('.anp-current').text('ok').css({'color':'#0ae'});
								globalSelectGivenNumber = "3";
							}else{
								$('.div-anp').show();
								$('.anp-current').text('Phone number should be 11 digit.').css({'color':'#ff3232'});
								globalSelectGivenNumber = "1";
							}
						}else {
							$('.div-anp').show();
							$('.anp-current').text('Invalid phone number.').css({'color':'#ff3232'});
							globalSelectGivenNumber = "1";
						}
						
					}
					
				}
			})
							

		}else if (globalSelectOption == "Email"){
			//email address;
			$.ajax({
				url: '../php/z-home/checkEmail.php',
				type: 'POST',
				data: {sendEmail: editInpVal},
				cache: false,
				beforeSend: function(){},
				success: function(data){
					var getDataResult = $.trim(data);
						if (getDataResult == 0){
							$('.div-anp').show();
							$('.anp-current').text('Primary email').css({'color':'#ff3232'});
							$('.prim-sec-rbtn').hide();
							globalSelectGivenNumber = "1";
							globalSelectGivenNumber2update = "1";
							globalKnowingAction = "1";
						}else if(getDataResult == 1){
							if (editInpVal == globalSelectDummy){
								if(editInpVal == $('.label-secon-con').text()){
									$('.div-anp').show();
									$('.anp-current').text('Secondary contact.').css({'color':'#ff3232'});
									$("#second").prop('checked', true);
									globalKnowingAction = "1";
									globalAction = "Secondary";
								}else{
									$('.div-anp').show();
									$('.anp-current').text('User current email.').css({'color':'#0ae'});
								}
								globalSelectGivenNumber = "1";
							}else {
								$('.div-anp').show();
								$('.anp-current').text('Email address not available.').css({'color':'#ff3232'});
								globalSelectGivenNumber = "1";
							}
						}else if(getDataResult == 2){
							$('.div-anp').show();
							$('.anp-current').text('Invalid entry').css({'color':'#ff3232'});
							globalSelectGivenNumber = "1";
						}else if(getDataResult == 3){
							var haveAT = editInpVal.indexOf("@");
							var haveDOT = editInpVal.lastIndexOf(".");
								if (haveAT<1 || haveDOT<haveAT+2 || haveDOT+2>=editInpVal.length){
									$('.div-anp').show();
									$('.anp-current').text('Invalid email format').css({'color':'#ff3232'});
									globalSelectGivenNumber = "1";
								}else{
									$('.div-anp').show();
									$('.anp-current').text('ok').css({'color':'#0ae'});
									globalSelectGivenNumber = "3";
								}
						}
				}
			})
		}
	});
	
	//prime secondary botton
	$(".ordNo").click(function(){
		if ($(".ordNo:checked").val() == globalAction) {
			globalKnowingAction = "1";
		}else{
			globalKnowingAction = "";
		}
	});

	//insert/update button save
	$('.sub-mdl-btn-save').click(function(){
		if (globalDataEmailUpdate == "edit") {
			var setOrdinalNo =  $(".ordNo:checked").val();
			//alert(globalSelectOption + " " + globalSelectGivenNumber  + " " + globalSelectGivenNumber2update);
			if (globalSelectGivenNumber == 3){
				if (!setOrdinalNo){
					if(globalSelectGivenNumber2update == 1){
						//alert("update tbl_user and tbl_Usercontacts email only")
						var tableCountToUpdate = "2";
						var toMake = "noThing";
						updateContacInfo(tableCountToUpdate,toMake);
						

					}else{
						//alert("change email phone tbl_Usercontacts only")
						var tableCountToUpdate = "1";
						var toMake = "noThing";
						updateContacInfo(tableCountToUpdate,toMake);
					}
				}else{
					if (setOrdinalNo == "Primary"){
						
						if(globalSelectOption == "Phone"){
							$('.anp-current').text('Err: Phone primary is not yet available.').css({'color':'#ff3232'});
						}else{
							//alert("change email and primsec tbl_user and tbl_Usercontacts primary email")
							var tableCountToUpdate = "2";
							var toMake = "Primary";
							updateContacInfo(tableCountToUpdate,toMake);
						}
					}else{
						//alert("change email and primsec tbl_Usercontacts secondary email or phone ")
							var tableCountToUpdate = "1";
							var toMake = "Secondary";
							updateContacInfo(tableCountToUpdate,toMake);
					}
				}
			}else if(globalSelectGivenNumber == 1 && (setOrdinalNo) && globalKnowingAction != "1"){
					if (setOrdinalNo == "Primary"){
						if(globalSelectOption == "Phone"){
							$('.anp-current').text('Err: Phone primary is not yet available.').css({'color':'#ff3232'});
						}else{
							//alert("change primsec tbl_user and tbl_Usercontacts primary email")
							var tableCountToUpdate = "2";
							var toMake = "Primary";
							updateContacInfo(tableCountToUpdate,toMake);
						}
					}else{
						//alert("change primsec tbl_Usercontacts second email or phone ")
							var tableCountToUpdate = "1";
							var toMake = "Secondary";
							updateContacInfo(tableCountToUpdate,toMake);

					}
			}else{
				$('.anp-current').text('Err: use other contact!').css({'color':'#ff3232'});
			}

		}else if(globalDataEmailUpdate == "new"){
			if(globalContactData == 3){
				var setOrdinalNo =  $(".ordNo:checked").val();
					if (!setOrdinalNo){
						$.ajax({
							url: '../php/z-home/updateUserContact.php',
							type: 'POST',
							data: {sendNewCon: $.trim($('.add-new-inp').val()), saveTo: globalTodo, sendUserId: globalSessionUserId},
							cache: false,
							beforeSend: function(){},
							success: function(data){
								$('.err-msg').fadeIn('fast').append(data);
									setTimeout(function() {
										$('.err-msg').hide('fast').text('');
											$('.add-new-inp').val('');
											$('.sub-mdl-btn-cancel').hide();
											$('.contact-edit').hide();
											$('.contact-add').hide();
											$('.div-anp').hide();
											$('.prim-sec-rbtn').hide();
											$('.contact-opt').show();
											$('.sub-mdl-btn-edit').show();
											cancelButton();
									}, 2000);
								//$('.new-contact-mdl').modal('toggle');
								globalContactData = "6";
								globalDataEmailUpdate = '';
								$('.slctCon').val('');
								$('.edi-con-inp').val('');
								callPrimeCon();
								callSeconCon();
								callList();
							}
						});
					}else{
						if (setOrdinalNo == "Primary"){
								if(globalTodo == "PhoneNo"){
									var conTodo = "PhonePrime";
								}else {
									var conTodo = "Update";
								}
						}else{
							var conTodo = "Insert"
						}
							$.ajax({
								url: '../php/z-home/updateUserContactPrimSec.php',
								type: 'POST',
								data: {sendNewCon: $.trim($('.add-new-inp').val()), sendTodo: conTodo, saveTo: globalTodo, sendUserId: globalSessionUserId},
								cache: false,
								beforeSend: function(){},
								success: function(data){
									var getDataResult = $.parseJSON(data);
									$('.err-msg').fadeIn('fast').append(getDataResult.msg);
										setTimeout(function() {
											$('.err-msg').hide('fast').text('');
												$('.add-new-inp').val('');
												$('.sub-mdl-btn-cancel').hide();
												$('.contact-edit').hide();
												$('.contact-add').hide();
												$('.div-anp').hide();
												$('.prim-sec-rbtn').hide();
												$('.contact-opt').show();
												$('.sub-mdl-btn-edit').show();
												$('.ordNo').prop('checked', false);	
												cancelButton();
										}, 1000);
									//$('.new-contact-mdl').modal('toggle');
									$('.div-nav-user-emaill').text(getDataResult.newEmail);
									globalContactData = "6";
									globalDataEmailUpdate = '';
									$('.slctCon').val('');
									$('.edi-con-inp').val('');
									callPrimeCon();
									callSeconCon();
									callList();
								}
							});
						
					}
			}	
		}
		
	});


	//user's info modal
	$('.mdl-ul li').click(function(){
		if(this.id == 0) {
			$('#0').css({'color':'#666666'});
			$('#1').css({'color':'#fff'});
			$('#2').css({'color':'#fff'});

			$('.mdl-inner-content-name').show();
			$('.mdl-inner-content-detail').hide();
			$('.mdl-inner-content-contact').hide();
			$('.mdl-inner-out').hide();
			$('.update-name-text').text('Be matured when updating your name.');
			
				
				$.ajax({
					url: '../php/z-home/displayFullName.php',
					type: 'POST',
					data: {sendUserId: globalSessionUserId},
					cache: false,
					beforeSend: function(){},
					success: function(data){
						var getDataResult = $.parseJSON(data);
							$('.fnametxt').val($.trim(getDataResult.firtName));
							$('.mnametxt').val($.trim(getDataResult.middleName));
							$('.lnametxt').val($.trim(getDataResult.lastName));
					}
				});
		}
		if(this.id == 1) {
			$('#1').css({'color':'#666666'});
			$('#0').css({'color':'#fff'});
			$('#2').css({'color':'#fff'});

			$('.mdl-inner-content-detail').show();
			$('.mdl-inner-content-name').hide();
			$('.mdl-inner-content-contact').hide();
			$('.mdl-inner-out').hide();

				$.ajax({
					url: '../php/z-home/displayUsername.php',
					type: 'POST',
					cache: false,
					beforeSend: function(){},
					success: function(data){
						$('.currentUsernametxt').val($.trim(data));
						$('.p-current').text('Current username.').css({'color':'#fff'});
					}
				})
		}
		if(this.id == 2) {
			callPrimeCon();
			callSeconCon();
			$('#2').css({'color':'#666666'});
			$('#0').css({'color':'#fff'});
			$('#1').css({'color':'#fff'});

			$('.mdl-inner-content-contact').show();
			$('.mdl-inner-content-name').hide();
			$('.mdl-inner-content-detail').hide();
			$('.mdl-inner-out').hide();
		}
	})
	
	
	
	$('.sub-mdl-btn-edit').click(function(){
		if ($('.slctCon').val() == ""){
			CallErrMsgForSelectOption();
		}else{
			globalDataEmailUpdate = 'edit';
				$(this).hide();
				$('.sub-mdl-btn-save').show();
				$('.sub-mdl-btn-delete').hide();
				$('.add-new-con').hide();
				$('.contact-opt').hide();
				$('.contact-edit').show();
				$('.prim-sec-rbtn').show();
				$('.sub-mdl-btn-cancel').show();
				$('.edi-con-inp').val($.trim($('.slctCon').val()));

				phoneOrEmailOnEditInp();
				callCurrentUserContactId()
					$('.edi-con-inp').keyup();

		}
	});
	$('.add-new-con').click(function(event){
		event.preventDefault();
		globalDataEmailUpdate = 'new';
		$('.sub-mdl-btn-save').show();
		$('.sub-mdl-btn-delete').hide();
		$('.add-new-con').hide();
		$('.contact-opt').hide();
		$('.contact-edit').hide();
		$('.sub-mdl-btn-edit').hide();
	//	$('.prim-sec-rbtn').show();
		$('.contact-add').show();
		$('.sub-mdl-btn-cancel').show();
		$('.div-anp').show();
		$('.anp-current').text('Add new contact.').css({'color':'#ff3232'});
	});
	
	$('.sub-mdl-btn-cancel').click(function(){
		cancelButton();
	});

	$(".mdl-user-pic").hover(function(){
	    $('.mdl-user-pic-hover').show();
	},function(){
	    $('.mdl-user-pic-hover').hide();
	});
	$('.mdl-user-pic, .mdl-user-pic-hover').click(function(){
		$('.mdl-change-pic').modal('show');
	});

	$('.update-link').click(function(){
		$('.ordNo').prop("checked", false);
		$('.new-contact-mdl').modal('show');
		$('.sub-mdl-btn-save').hide();
		callList();
	});

	//delete contacts
	$('.sub-mdl-btn-delete').click(function(){
		if ($('.slctCon').val() == ""){
			CallErrMsgForSelectOption();
		}else{
			$.ajax({
				url: '../php/z-home/deleteContacts.php',
				type: 'POST',
				data: {sendUserContact: $.trim($('.slctCon').val()), sendUserId: globalSessionUserId},
				cache: false,
				beforeSend: function(){},
				success: function(data){
					$('.err-msg').fadeIn('fast').append(data);
						setTimeout(function() {
							$('.err-msg').hide('fast').text('');
						}, 2000);
						$('.btn-select-value').text('Select contact');
						callSeconCon();
						callList();
				}
			});
		}
	});

	
	$(":file").filestyle({iconName: "glyphicon-picture"});
	$(":file").filestyle({buttonText: "Select picture"});
	$(":file").filestyle('placeholder', 'Photo');
	//END USER INFO SECTION -------------------------------->>

	//when modal hide
	$('.new-contact-mdl').on('hidden.bs.modal', function () {
		globalDataEmailUpdate = '';
		$('.slctCon').val('');
		$('.btn-select-value').text('Select contact');
		$('.contact-opt').show();
		$('.sub-mdl-btn-edit').show();
		$('.add-new-con').show();
		$('.sub-mdl-btn-delete').show();
		$('.sub-mdl-btn-cancel').hide();
		$('.prim-sec-rbtn').hide();
		$('.div-anp').hide();
		$('.anp-current').text('');
		$('.contact-edit').hide();
		$('.contact-add').hide();
		$('.edi-con-inp').val('');
		$('ordNo').prop("checked", false);
		globalSelectGivenNumber = "";
		globalSelectGivenNumber2update = ""; //primary update
		globalKnowingAction = "";
		globalAction = "";
		globalUserContactId = "";
	});

	$('.user-info-mdl').on('hidden.bs.modal', function () {
		$('.mdl-inner-out').show();
		$('.mdl-inner-content-name').hide();
		$('.mdl-inner-content-detail').hide();
		$('.mdl-inner-content-contact').hide();

		$('.fnametxt').val('');
		$('.mnametxt').val('');
		$('.lnametxt').val('');

		$('#0').css({'color':'#fff'});
		$('#1').css({'color':'#fff'});
		$('#2').css({'color':'#fff'});
	});
	//end when modal hide
});



//FUNCTION SECTION -------------------------------------------------------------------------------------->>
function getUserPhoto(){
	 $.ajax({
		url: '../php/z-home/displayUserPhoto.php',
		type: 'post',
		data: {sendUserId: globalSessionUserId},
		cache: false,
		success: function(data){
			var userPhoto = "'"+$.trim(data)+"'";
				$('.mdl-user-pic-display, .post-write-div-col-1-pic-main').css({
					"background" : "url("+userPhoto+")",
					"background-position": "center",
					"background-repeat": "no-repeat",
					"background-size": "cover"
				});
				if(userPhoto != "''"){
					$('.mdl-user-pic, .post-write-div-col-1-pic').css('background-image', 'none');
				}
		}
	});
}

function getUserId(){
	 $.ajax({
		url: '../php/z-home/displayUserid.php',
		type: 'get',
		cache: false,
		async: false,
		success: function(data){
			globalSessionUserId = $.trim(data); //sodlan ang global session id
			
		}
	});
}

function refreshdiv() {
		setTimeout(function(){
			$('.all-post').load('../php/z-home/displayPost.php').fadeIn("slow");
			refreshdiv();
		}, 1000);
}

function displayOnlineUser() {
		setTimeout(function(){
			$('.online-user').load('../php/z-home/displayOnlineUser.php').fadeIn();
			displayOnlineUser();
		}, 1000);
}

function callList(){
	$('.con-select-opt').empty();
	$.ajax({
		url: '../php/z-home/displayAllContacts.php',
		type: 'POST',
		data: {sendUserId: globalSessionUserId},
		cache: false,
		beforeSend: function(){},
		success: function(data){
			var getDataResult = $.parseJSON(data);
			var kan = getDataResult;
			var i;
				for (i = 0; i < getDataResult.length; i++){
					$('.con-select-opt').append('<li>' + $.trim(getDataResult[i])  + '</li>');
				}
		}

	});
}

function callPrimeCon(){
	$.ajax({
		url: '../php/z-home/displayPrimaryContact.php',
		type: 'POST',
		data: {sendUserId: globalSessionUserId},
		cache: false,
		beforeSend: function(){},
		success: function(data){
			$getDataResult = $.trim(data);
			$('.label-prime-con').text($getDataResult).css({'color':'#666'});
		}
	});
}

function callSeconCon(){
	$.ajax({
		url: '../php/z-home/displaySecondaryContact.php',
		type: 'POST',
		data: {sendUserId: globalSessionUserId},
		cache: false,
		beforeSend: function(){},
		success: function(data){
			$getDataResult =$.trim(data);
			$('.label-secon-con').text($getDataResult).css({'color':'#666'});
		}
	});
}

function phoneOrEmailOnEditInp(){
		var addNewCon = $.trim($('.slctCon').val());
		var haveAT = addNewCon.indexOf("@");
		var haveDOT = addNewCon.lastIndexOf(".");
			if (haveAT<1 || haveDOT<haveAT+2 || haveDOT+2>=addNewCon.length){
				globalSelectOption = 'Phone';
				globalSelectDummy = addNewCon;
			}else{
				globalSelectOption = 'Email';
				globalSelectDummy = addNewCon;
								
			}
}

function callCurrentUserContactId(){
	$.ajax({
		url: '../php/z-home/displayUserContactId.php',
		type: 'POST',
		data: {sendUserContact: $.trim($('.slctCon').val()), sendContactTodo : globalSelectOption},
		cache: false,
		beforeSend: function(){},
		success: function(data){
			globalUserContactId = $.trim(data);
		}
	});
}

function updateContacInfo(tableCountToUpdate,toMake){
	$.ajax({
		url: '../php/z-home/updateContactInfo.php',
		type: 'POST',
		data: {sendNewCon: $.trim($('.edi-con-inp').val()),sendContactId: globalUserContactId, sendUserId: globalSessionUserId, sendIdentifier: globalSelectOption, sendTableCount: tableCountToUpdate, sendtoMake: toMake},
		cache: false,
		beforeSend: function(){},
		success: function(data){
			$('.err-msg').fadeIn('fast').append(data);
				setTimeout(function() {
					$('.err-msg').hide('fast').text('');
				}, 2000);
			$('.new-contact-mdl').modal('toggle');
			callPrimeCon();
			callSeconCon();
			cancelButton();
		}
	});
	
}

function cancelButton(){
	globalDataEmailUpdate = '';
	$('.sub-mdl-btn-cancel').hide();
	$('.sub-mdl-btn-save').hide();
	$('.contact-edit').hide();
	$('.contact-add').hide();
	$('.div-anp').hide();
	$('.prim-sec-rbtn').hide();
	$('.sub-mdl-btn-delete').show();
	$('.add-new-con').show();
	$('.contact-opt').show();
	$('.sub-mdl-btn-edit').show();
	$('.slctCon').val('');
	$('.edi-con-inp').val('');
	$('.ordNo').prop("checked", false);
	$('.curr-span-val').text("Select Contact");
		globalSelectGivenNumber = "";
		globalSelectGivenNumber2update = ""; //primary update
		globalKnowingAction = "";
		globalAction = "";
		globalUserContactId = "";
}

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
			reader.onload = function (e) {
				$('.prev-img').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
	}
}	

function CallErrMsgForSelectOption(){
	$('.sub-mdl-btn-delete').prop('disabled', true);
	$('.err-msg').fadeIn('fast').append('Select a contact please!');
	setTimeout(function() {
		$('.sub-mdl-btn-delete').prop('disabled', false);
		$('.err-msg').hide('fast').text('');
	}, 2000);
}

function goOnline(){
	$.ajax({
		url: '../php/z-home/displayOnline.php',
		type: 'get',
		cache: false,
		async: false,
		success: function(data){}
	});
}

function goOffline(){
	$.ajax({
		url: '../php/z-home/displayOffline.php',
		type: 'get',
		cache: false,
		async: false,
		success: function(data){}
	});
}

function testing(id, name){
	alert(id + " " + name);
}
