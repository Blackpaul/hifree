$(document).ready(function(){
	//start validation 
	resetRegForm();

	$("#signupForm").validate({
		rules: {
			txtusername: {
				required: true,
				minlength: 3,
				maxlength: 15,
				remote: {
					url: 'php/z-userSubFunction/checkUsername.php',
                    type: 'post'
				}
			},
			txtuseremail: {
				required: true,
				email: true,
				remote: {
					url: 'php/z-userSubFunction/checkEmail.php',
                    type: 'post'
				}
			},
			txtuserpass: {
				required: true,
				minlength: 3
			},
			slctGender: {
				required: true
			}
		},
		messages: {
			txtusername: {
				required: "Please enter your username.",
				minlength: "Username must 3 characters up.",
				maxlength: "Exceed maximum characters.",
				remote: "Username already in use!"
			},
			txtuseremail: {
				required: "Please enter your email address.",
				email: "enter valid email address.",
				remote: "Email already in use!"
			},
			txtuserpass: {
				required: "Please enter your Password.",
				minlength: "Password must 3 characters up."   
			},
			slctGender: {
				required: "Kindly select your gender."  
			}
		},
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
			$(element).closest('.form-group').find('[class^="glyphicon"]').remove();
    		$(element).closest('.form-group').append('<span class="glyphicon glyphicon-remove form-control-feedback" style="margin: 0 15px 0 0; text-shadow: 1px 1px 0px black;">' );
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error').addClass('has-success');   
			$(element).closest('.form-group').find('[class^="glyphicon"]').remove();
			$(element).closest('.form-group').append('<span class="glyphicon glyphicon-ok form-control-feedback" style="margin: 0 15px 0 0;text-shadow: 1px 1px 0px black;color:#ffdf00">' );   
		 },
		 invalidHandler: function(form) {
			$('.div-alert-reg').text("Invalid input, kindly check or enter new one.").css({
				'margin': '10px 0 0 0',
				'text-align' : 'center',
				'color': '#ffa500'
			});
		}
	});

	$('.ulList').click(function(){
		$('.slctGender').val(" ");
			if ($('.slctGender').val()){
				$('.slctGender').valid();
			}
	})
	//end validation	

	//start registration 
	$('.div-reg-btn-i').click(function(){
		if($("#signupForm").valid()){
			$('.div-alert-reg').text('');
			$('.div-reg-btn-i').attr("disabled", true);

			$('.getusername').text($("input[name='txtusername']").val().charAt(0).toUpperCase() + $("input[name='txtusername']").val().slice(1));
			$('.getEmail').text($("input[name='txtuseremail']").val());
			var vcode = createVcode();
				$.ajax({
					url: 'php/z-userSubFunction/register.php',
					type: "POST",
					data: $('#signupForm').serialize() + "&verificationCode=" + vcode,
					cache: false,
					beforeSend: function(){
     					$('.div-loading-background').css({
							'display': 'block', 
							'z-index': '1500'
						});	
   					},
					success: function(data){
						var queryMsg = $.trim(data)
						if (queryMsg === "success"){
							$('.div-loading-background').css('display', 'none')
							$('.account-confirmation-mdl').modal('show');
							$('.div-logreg-container').css('overflow', 'hidden');
							$('.div-reg-btn-i').removeAttr('disabled');
						}else{
							$('.div-loading-background').css('display', 'block')
						}
					}
				});
			
		}
	});
	//end registration 

	//verification
		$('.btn-verify').click(function(){
			
			$.ajax({
				url: 'php/z-userSubFunction/verified.php',
				type: 'POST',
				data: {
					gvemail: $('.getEmail').text(), 
					gvcode: $.trim($('.verify-txtbox').val())
				},
				cache: false,
				success: function(data){
					var queryMsg = $.trim(data);
					if(queryMsg === "verified"){
						window.location.href = 'page/home.html';
					}
					else{
						$('.div-alert-verify').html(queryMsg);
					}
				}
			});
		});
	//end verification

	//get new verification code 
	$('.getNewCode, .btn-ask-again').click(function(){
		if ($('.getEmail').text().trim() === ''){
			alert('Invalid entry kindly reload your page.');
		}
		else{
			$.ajax({
				url: 'php/z-userSubFunction/sendNewVerificationCode.php',
				type: 'POST',
				data: { 
					getusername: $('.getusername').text(), 
					getuseremail: $('.getEmail').text(), 
					newVerificationCode: createVcode()
				},
				cache: false,
				beforeSend: function() {
					$('.div-loading-background').css({
						'display': 'block', 
						'z-index': '1500'
					});
				},
				success: function(data){
					$('.mdl-body-text').append('<p style="margin: 5px 0 0 0; color: #fff;"> A new verification code has been sent to your email address! </p>');
					$('.div-alert-verify').html("");
				},
				complete: function(){
					$('.div-loading-background').css({
						'display': 'none', 
						'z-index': 'initial'
					});
				}
		});
		}
	});
	//end get new verification code

	// start login 
	$('.div-logreg-button-i').click(function(){
		$.ajax({
			url: 'php/z-userSubFunction/login.php',
			type: 'POST',
			data: $('.login-form').serialize(),
			cache: false,
			success: function(data){
				var queryMsg = $.trim(data);
				if(queryMsg === "success"){
					window.location.href = 'page/home.html';
				}else if(queryMsg === "Invalid"){
					$('.alert-div').html("Invalid Account! Check your login information.");
				}else{
					var getData = $.parseJSON(data);
					$('.getusername').text($.trim(getData.username).charAt(0).toUpperCase() + $.trim(getData.username).slice(1));
					$('.getEmail').text($.trim(getData.useremail));
					$('.account-confirmation-mdl').modal('show');
					$('.div-logreg-container').css('overflow', 'hidden');
				}
			}
		});
	});
	//end login


	//forgot password modal show hide
	$('.div-logreg-link-a').click(function(){
		$('.forgot-pass-mdl').modal('show');
	});

	$('.btn-back-login').click(function(){
		$('.forgot-pass-mdl').modal('hide');
		resetRegForm();
	});
	//end forgot password modal show hide

	//forgot password function 
	$('.btn-reset-pass').click(function(){
		$.ajax({
			url: 'php/z-userSubFunction/forgotPassword.php',
			type: 'POST',
			data: { forgotEmail: $('.forgot-txtbox').val().trim()},
			cache: false,
			beforeSend: function() {
					$('.div-loading-background').css({
						'display': 'block', 
						'z-index': '1500'
					});
				},
			success:function(data){
				$('.div-alert-reset').html(data);
			},
			complete: function(){
				$('.div-loading-background').css({
					'display': 'none', 
					'z-index': 'initial'
				});
			}
		});
	});
	//end forgot password function 

	//when modal hide
	$('.account-confirmation-mdl, .forgot-pass-mdl').on('hidden.bs.modal', function () {
		$('.div-reg-btn-i').removeAttr('disabled');
		$('.div-logreg-container').css('overflow', 'auto');
		resetRegForm();
	});
	//end when modal hide

});//end doc ready

function resetRegForm(){
	$('#signupForm')[0].reset();
	$('.login-form')[0].reset();
	$('.btn-select-value').html('Select Gender');
	$('.div-reg-user').find('[class^="glyphicon"]').remove();
	$('.div-reg-user').append('<span class="glyphicon glyphicon-user form-control-feedback" style="margin: 0 15px 0 0;color:#555555;">');
	$('.div-reg-email').find('[class^="glyphicon"]').remove();
	$('.div-reg-email').append('<span class="glyphicon glyphicon-envelope form-control-feedback" style="margin: 0 15px 0 0;color:#555555;">');
	$('.div-reg-pass').find('[class^="glyphicon"]').remove();
	$('.div-reg-pass').append('<span class="glyphicon glyphicon-lock form-control-feedback" style="margin: 0 15px 0 0;color:#555555;">');
	$('.div-reg-gender').find('[class^="glyphicon"]').remove();
	$('.div-reg-gender').append('<span class="glyphicon glyphicon-chevron-down form-control-feedback" style="margin: 0 15px 0 0;color:#fff;">'); 
	$('.getEmail').html("");
	$('.mdl-body-text p').remove();
	$('.verify-txtbox, .forgot-txtbox').val("");
	$('.alert-div, .div-alert-verify, .div-alert-reset').html("");
	$('.div-alert-reg').text('');
}

function createVcode(){
	var randomString = function(length) {
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			for(var i = 0; i < length; i++) {
				text += possible.charAt(Math.floor(Math.random() * possible.length));
			}
		return text;
	}
	var createVcode = randomString(5);
	return createVcode;
}

