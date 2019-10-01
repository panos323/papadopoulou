var iznogoud = (function () {
	

	
	  // Load the SDK asynchronously
	  (function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "https://connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	  }(document, 'script', 'facebook-jssdk'));
	  

	  function isValidEmailAddress(emailaddress) {
        var pattern = new RegExp(
            /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(emailaddress);
    }
			// Here we run a very simple test of the Graph API after login is
	// successful.  See statusChangeCallback() for when this call is made.
	function testAPI() {
		console.log('Welcome!  Fetching your information.... ');
		FB.api('/me', {fields: 'id,name,email'}, function(response) {
			 //console.log(response.id);
			FB.api('/'+response.id, {fields: 'email,id,name'}, function(response) {
				//console.log(response);
				if(response.email != '' && isValidEmailAddress(response.email)){
					checkLogFBUser(response.email);
				}else{
					//error from fb
					console.log('fb-error')
				}
			});
		});
	}
	
	

	
	function closeInstructions(){
		$('#instructions').fadeOut('400');
	}
	
	function closeAllScreens(){
		$('.screen').fadeOut('400');
//		$('.screen').css('display', 'none');
	}
	
	function processGameResults( answer ){
		if( answer.res == 1 ){
			openThanks();
		}else{
			openFail();
		}
	}

	
	function checkLoginForm() {
        var email = $.trim($(".input_email_login").val());
        var password = $.trim($(".input_password_login").val());
        var pass = true;
		if (email == "" || !isValidEmailAddress(email)) {
			pass = false;
			$(".input_email_login").addClass('error');
			$(".input_email_login").attr('placeholder','To email σας δεν είναι σωστό.');
		}

		if (password == "") {
			pass = false;
			$(".input_password_login").addClass('error');
			$(".input_password_login").attr('placeholder','Προσθέστε τον κωδικό σας.');
		}
		if(pass){
			LogUser();
			fbq('track', 'CompleteRegistration');
		  }
	}

	function LogUser() {
        var email = $.trim($(".input_email_login").val());
        var password = $.trim($(".input_password_login").val());
        if ( $("body").hasClass("submitting") ){
            return;
        }

        $("body").addClass("submitting");

        var request = $.ajax({
            url: "index.php",
            type: "POST",
            cache: false,
            dataType: "json",
            data: {
                page: 'ajax',
                email : email,
    	    	password : password,
                action: 'logUser',
            }
        });

        request.done( function( ans ) {
            if (ans){
				 console.log(ans)
	
				if(ans.password == 'wrong'){
					$('.wrong-pass').fadeIn(100);
				}else if(ans.login == 0){
					$('.no-user').fadeIn(100);
				}else{
					location.reload();
				}

            }
        });

        request.always( function(){
			$("body").removeClass("submitting");
        });
    }
	
	
    function sendNotificationToAll(text){
        if ( $("body").hasClass("submitting") ){
            return;
        }

        $("body").addClass("submitting");

        var request = $.ajax({
            url: "index.php",
            type: "POST",
            cache: false,
            dataType: "json",
            data: {
                page: 'ajax',
                text: text,
                action: 'sendNotificationToAll'
            }
        });

        request.done( function( ans ) {
            if (!jQuery.isEmptyObject(ans)){
                console.log(ans);
            }else{
                console.log('@@');
            }
        });

        request.always( function(){
            $("body").removeClass("submitting");
        });
    }
	
	


	function submitGame() {



        if( checkForm() ){
        	
	        var request = $.ajax({
	            url: "index.php",
	            type: "POST",
	            cache: false,
	            dataType: "json",
	            data: {
	                page: 'ajax',
	                firstName: $(".input-firstName").val(),
	                lastName: $(".input-lastName").val(),
	                mobile: $(".input-mobile").val(),
	                action: 'submitGame',
	            }
	        });

	        request.done( function( ans ) {
	            if (ans){
	            	openThanks();
	            }
	        });
	        openThanks();
//	        request.always( function(){
//	            $("body").removeClass("submitting");
//	        });

	    }
    }
	function checkSubmitGame(){
        if ( $("body").hasClass("submitting") ){
            return;
        }

        $("body").addClass("submitting");

        var request = $.ajax({
            url: "index.php",
            type: "POST",
            cache: false,
            dataType: "json",
            data: {
                page: 'ajax',
                action: 'submitGame',
            }
        });

        request.done( function( ans ) {
            if (ans){
				console.log(ans)
				if(ans.logged == 0){
					$('.error-section').fadeIn(10);
				}else if(ans.todayGame == 1){
					$('.already-play-popup').fadeIn(500);
				}else if(ans.insertGameContest == 1){
					$('.lost-popup').fadeIn(500);
				}else if(ans.instantWin == 1){
					$('.instant-win-popup').fadeIn(500);
				}
            }
        });

        request.always( function(){
            $("body").removeClass("submitting");
        });
	}
	
    function checkForm() {
        var firstName = $.trim($(".input-firstName").val());
        var lastName = $.trim($(".input-lastName").val());
        var mobile = $.trim($(".input-mobile").val());

        var pass = true;
        $("#consent").removeClass('error');
        $("input").removeClass('error');

        if (firstName.length < 3) {
        	$(".input-firstName").addClass('error');
            pass = false;
        }
        
        if (lastName.length < 3) {
        	$(".input-lastName").addClass('error');
            pass = false;
        }
        
//        if (email.length < 6) {
//            pass = false;
//            $(".input-email").addClass('error');
//        }
		
        if ($(".checkbox_style").prop("checked")==false) {
        	//console.log("paparia");
        	$("#consent").addClass('error');
            pass = false;
        }
        
		if (mobile.length < 10) {
			$(".input-mobile").addClass('error');
            pass = false;
        }

        return pass;
    }
	
	

    function requestCallback(response) {
        if( typeof response.request !== "undefined") {
            storeInvites(response.request, response.to);
        }
    }

    function storeInvites(post, shares) {
        var request = $.ajax({
            url: "index.php",
            type: "POST",
            cache: false,
            dataType: "json",
            data: {
                page: 'ajax',
                action: 'storeInvites',
                shares: shares,
                post_id: post,
                fbUserId: fbUserId
            }
        });

        request.done( function( msg ) {
            if (msg.res == "1") {
            }
        });
    }

	
	/*
	 * Core Functions
	 * 
	 */
	
	function ajaxCallReturnJson( dataToSend, cb ){
        if ( typeof cb !== 'function' ) var cb = function() {};

        dataToSend.page = 'ajax';
        var request = $.ajax({
            url: "index.php",
            type: "POST",
            cache: false,
            data: dataToSend,
            dataType: "json"
        });

        request.done( function( msg ){
            cb( msg );
        });
    }
	
	function ajaxCallReturnHtml( dataToSend, cb ){
		if ( typeof cb !== 'function' ) var cb = function() {};
		
		dataToSend.page = 'ajax';
		var request = $.ajax({
			url: "index.php",
			type: "POST",
			cache: false,
			data: dataToSend,
			dataType: "html"
		});
		
		request.done( function( msg ){
			cb( msg );
		});
	}

    function ajaxCallUploadReturnJson( dataToSend, cb ){
        if ( typeof cb !== 'function' ) var cb = function() {};

        var request = $.ajax({
            url: "index.php",
            type: "POST",
            cache: false,
            processData: false,
            contentType: false,
            data: dataToSend,
            dataType: "json"
        });
        request.done( function( msg ){
            cb( msg );
        });
    }
	
	function checkCodeForm() {
		var code = $('#input-code').val();
		var pass = true;
		if (code.length != 8) {
			$('#input-code').css("border", "1px solid red");
			pass = false;
		} 
		return pass;
	}
	
	

	function checkRegisterForm() {
		var full_name = $.trim($(".fullName_register").val());
		var email = $.trim($(".email_register").val());
		var password = $.trim($(".password_register").val());
		var phone = $.trim($(".phone_register").val());
		var confirmPassword = $.trim($(".password_confirm_register").val());
		var terms = $(".terms-check").is(":checked");
		var pass = true;
		
		//checks
		if (full_name == "" || full_name.length < 8) {
			pass = false;
			$(".fullName_register").addClass('error');
			$(".fullName_register").attr('placeholder','To όνομα σας δεν είναι σωστό.');
		} else {
			$(".fullName_register").removeClass('error');
		}

		if (email == "" || !isValidEmailAddress(email)) {
			pass = false;
			$(".email_register").addClass('error');
			$(".email_register").attr('placeholder','To email σας δεν είναι σωστό.');
		} else {
			$(".email_register").removeClass('error');
		}

		if (phone == "" || phone.length != 10) {
			pass = false;
			$(".phone_register").addClass('error');
			$(".phone_register").attr('placeholder','10 αριθμοι.');
		} else {
			$(".phone_register").removeClass('error');
		}

		if (password == "" || password.length < 8) {
			pass = false;
			$(".password_register").addClass('error');
			$(".password_register").val('');
			$(".password_register").attr('placeholder','8 χαρακτήρες και πάνω.');
		} else {
			$(".password_register").removeClass('error');
		}

		if (confirmPassword == "" || confirmPassword != password) {
			pass = false;
			$(".password_confirm_register").addClass('error');
			$(".password_confirm_register").val('');
			$(".password_confirm_register").attr('placeholder','Επιβεβαιώστε τον κωδικό σας.');
		}  else {
			$(".password_confirm_register").removeClass('error');
		}

		if (terms == false) {
			pass = false;
			$(".terms-check").css("outline","1px solid red");
		} else {
			$(".terms-check").css("outline","none");
		}
		 
		return pass;
	}
	

	$(document).ready(function(){
		
		$('#register_form').on("submit", function(e) {
			e.preventDefault();
			submitRegisterForm();
		});

		$('#login_form').on("submit", function(e) {
			e.preventDefault();
			submitLoginForm();
		});

		$('#logout-button').on("submit", function(e) {
			e.preventDefault();
			logoutBtn();
		});

		$('#submit-code').on("submit", function(e) {
			e.preventDefault();
			submitCodeBtn();
		});
		

		function submitRegisterForm() {
		
			if (checkRegisterForm()) {
	
				var request = $.ajax({
					url: "index.php",
					type: "POST",
					cache: false,
					//dataType: "json",
					data: {
						page: 'ajax',
						full_name: $(".fullName_register").val(),
						email : $(".email_register").val(), 
						phone : $(".phone_register").val(),
						password : $(".password_register").val(),
						newsletter : $(".newsletter-check").is( ":checked" ) ? 1 : 0,
						captcha: grecaptcha.getResponse($('#register_recaptcha').attr('data-widget-id')),
						action: 'storeUser'
					}
				});
	
				request.done( function( ans ) {
					if (ans == "register_success"){
						$("#register_form")[0].reset();
						grecaptcha.reset(); // Reset reCaptcha
						setTimeout(function() {
							document.location.reload()
					  }, 1000);
					} else if (ans == "register_email_exists"){ 
						alert("Το email υπάρχει ήδη");
					}
				});
	
			}
		}

		function submitLoginForm() {
			if (check_loginForm()) {
				var request = $.ajax({
					url: "index.php",
					type: "POST",
					data: { 
						page: 'ajax',
						email: $("#email_login").val(),
						password: $("#password_login").val(),
						captcha_login: grecaptcha.getResponse($('#login_recaptcha').attr('data-widget-id')),
						action: 'logUser'
					},
					success: function(data) {
						if(data == "wrongCrendetials"){
							//display errors
							$("#ErrorLoggedIn").html("email or password are wrong...");
						} else if (data == "rightCrendetials") {
							$("#register_form")[0].reset();
							grecaptcha.reset(); // Reset reCaptcha
								setTimeout(function() {
									document.location.reload()
							}, 1000);
						}
					   console.log("SUCCESS");
					   console.log(data);
					   $("#loginForm")[0].reset();
					},
					error: function(err,xhr) {
						console.log("ERROR");
						console.log(err);
						console.log(xhr);
					}
				});
				request.done(function(ans) {
					if (ans) {
						//openThanks();
						$("#loginForm")[0].reset();
					}
				});
			}
		}


		function check_loginForm() {

			$email = $.trim($("#email_login").val());
			$password = $.trim($("#password_login").val());
		  
			var pass = true;
			$("input").removeClass('error');
	
			//check input length
			if ($email.length < 6) {
				$("#email_login").addClass('error');
				pass = false;
			}
			if ($password.length < 6) {
				$("#password_login").addClass('error');
				pass = false;
			}
	
			return pass;
		}

		function submitCodeBtn() {
			if (checkCodeForm()) { 
				var request = $.ajax({
					url: "index.php",
					type: "POST",
					data: { 
						page: 'ajax',
						code: $("#input-code").val(),
						action: 'sumbitCode'
					},
					success: function(ans){
						
					},
					error : function(err,xhr,status) {
						console.log(err);
						console.log(xhr);
						console.log(status);
					}
				});

			request.done(function(ans) { 
				alert(ans);
				$("#submit-code")[0].reset();
			});
			}
		}
		

		function logoutBtn() {
			var request = $.ajax({
				url: "index.php",
				type: "POST",
				cache: false,
				//dataType: "json",
				data: {
					page: 'ajax',
					action: 'logOutUser',
				}
			});

			request.done( function( ans ) {
				window.location.reload(true);
			})
		}
		












		$('#login_btn').click(function(){
			checkLoginForm();
		});
		
		
		$(".input_email_register").keyup(function() {
			if ($(".input_email_register").hasClass('error')) {
				$(".input_email_register").removeClass('error');
			}
		});
		$(".input_password_register").keyup(function() {
			if ($(".input_password_register").hasClass('error')) {
				$(".input_password_register").removeClass('error');
			}
		});
		$(".input_password_confirm_register").keyup(function() {
			if ($(".input_password_confirm_register").hasClass('error')) {
				$(".input_password_confirm_register").removeClass('error');
			}
		});
		$(".terms-check").click(function() {
			if ($(".terms-check").hasClass('error')) {
				$(".terms-check").removeClass('error');
			}
		});
		$(".privacy-check").click(function() {
			if ($(".privacy-check").hasClass('error')) {
				$(".privacy-check").removeClass('error');
			}
		});
		

		$(".input_email_login").keyup(function() {
			if ($(".input_email_login").hasClass('error')) {
				$(".input_email_login").removeClass('error');
			}
			$('.no-user, .wrong-pass').fadeOut(100);
		});
		$(".input_password_login").keyup(function() {
			if ($(".input_password_login").hasClass('error')) {
				$(".input_password_login").removeClass('error');
			}
			$('.no-user, .wrong-pass').fadeOut(100);
		});


		//on document ready, send pageview of the landing page

		$('body').on('click', '.sbmt_btn',function(){
        	submitGame();
        });
		
		$('body').on('click', '.menu-welcome', function(){
          openWelcome();
		});
		
		$('body').on('click', '.play_btn', function(){
            openPlay();
        });
			
        $('body').on('click', '.menu-play', function(){
            openPlay();
        });

//        $('body').on('click', '.menu-play', function(){
//            submitGame();
//        });
//		
		/*iphone bug for click*/
		if (/ip(hone|od)|ipad/i.test(navigator.userAgent)) {
			$("body").css ("cursor", "pointer");
		}
        
       

        $('body').on('click', '.termsmnu img', function(){
            openTerms();
        });

        $('body').on('click', '.menu-gifts', function(){
            openPresents();
        });

        $('body').on('click', '.popupclose', function(){
            $('.ntch').fadeOut('400');
        });
        
    });
	
})();



jQuery(function($)
{
    $(".hamburger").click(function()
    {
        $(".navigation").toggleClass("open");
    })

});


   