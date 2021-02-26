<?php
include("connect/connect.php");
/*
if(isset($_SESSION['FON_G_USER_ID']) && $_SESSION['FON_G_USER_ID'] !=''){
//Redirect user to account
echo "<script>window.location.href='my_account/';</script>";
}*/
?>

<!doctype html>
<html class="no-js" lang="en">

<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login | FON Gateway | SMS, Short Code & USSD Platform</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="css/normalize.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="css/main.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="css/morrisjs/morris.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- metisMenu CSS
		============================================ -->
    <link rel="stylesheet" href="css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="css/metisMenu/metisMenu-vertical.css">
    <!-- calendar CSS
		============================================ -->
    <link rel="stylesheet" href="css/calendar/fullcalendar.min.css">
    <link rel="stylesheet" href="css/calendar/fullcalendar.print.min.css">
    <!-- forms CSS
		============================================ -->
    <link rel="stylesheet" href="css/form/all-type-forms.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="js/vendor/jquery-3.3.1.min.js"></script>
</head>

<body>
    <!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
	<div class="error-pagewrap">
		<div class="error-page-int">	
			<div class="content-error">      
				<div class="hpanel">
                    <div class="panel-body" style="border:1px solid #000;">
                  
                           <div id="form_content">
                           <div class="text-center m-b-md custom-login">
                    				<h3>GATEWAY</h3>
                    				<p>SMS,Short Code & USSD</p>
                    			</div>
                            <hr/>
                            <div class="form-group">
                                <label class="control-label" for="username">Email</label>
                                <input type="text" placeholder="Email" title="Please enter you email address" required="" value="" name="email" id="email" class="form-control">
                                <span class="help-block small" id="email_error"><font color="red">*Enter your email address</font></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" title="Please enter your password" placeholder="Password" required="" value="" name="password" id="password" class="form-control">
                                <span class="help-block small" id="password_error"><font color="red">* Enter your password</font></span>
                            </div>
                          
                            <button id="login" class="btn btn-success btn-block loginbtn">Login</button>
							</div>
                            <div id="response" style="margin:10px;padding:10px;"></div>
                      
                       
                    </div>
                    
                </div>
                
			</div>
			<a id="loader"><img src="img/loader.gif" width="50" height="50" /></a>
			<script>
			$(document).ready(function(){
				//Hide errors
				$("#email_error").hide();
				$("#password_error").hide();
				$("#loader").hide();
				$("#login").click(function(){
					email = $("#email").val();
					password = $("#password").val();
					error = true;
					$("#response").empty();
					
					if(email===""){
					error = true;
					$("#email_error").show();
					$("#email").css("border-color","red");
					$("#email").focus();
					}else{
						error = false;
						$("#email_error").hide();
					   $("#email").css("border-color","green");
					
					}
					
					if(password===""){
					error = true;
					$("#password_error").show();
					$("#password").css("border-color","red");
					$("#password").focus();
					}else{
						error = false;
						$("#password_error").hide();
					   $("#password").css("border-color","green");
					
					}
					
					if(error == false){
//Hide Button
$("#login").hide();
//Show loader
$("#loader").show();

myData = "email=" + email + "&password=" + password ;
jQuery.ajax({
type: "POST",
url: "process/signin.php",
dataType: "text",
data: myData,
success: function(b) {
$("#response").append(b);
$("#loader").hide();
$("#login").show();
$("#response").show();
}
});

}
				
				});
				$("#email").focusout(function(){
				  email = $("#email").val();
					error = true;
					if(email===""){
					error = true;
					$("#email_error").show();
					$("#email").css("border-color","red");
					$("#email").focus();
					}else{
						error = false;
						$("#email_error").hide();
					   $("#email").css("border-color","green");
					
					}
				});
				
				$("#password").focusout(function(){
					password = $("#password").val();
					error = true;
					if(password===""){
					error = true;
					$("#password_error").show();
					$("#password").css("border-color","red");
					$("#password").focus();
					}else{
						error = false;
						$("#password_error").hide();
					    $("#password").css("border-color","green");
					
					}
				
				});
			
			
			
			});
			</script>
			<div class="text-center login-footer">
				<p>Copyright &copy; <?php echo date('Y');?> <a href="https://www.fon.co.ke">Frontier Opticals Ltd</a> All rights reserved.</p>
			</div>
		</div>   
    </div>
    <!-- jquery
		============================================ -->
    
    <!-- bootstrap JS
		============================================ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="js/jquery-price-slider.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="js/jquery.meanmenu.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- sticky JS
		============================================ -->
    <script src="js/jquery.sticky.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="js/jquery.scrollUp.min.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="js/metisMenu/metisMenu.min.js"></script>
    <script src="js/metisMenu/metisMenu-active.js"></script>
    <!-- tab JS
		============================================ -->
    <script src="js/tab.js"></script>
    <!-- icheck JS
		============================================ -->
    <script src="js/icheck/icheck.min.js"></script>
    <script src="js/icheck/icheck-active.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="js/main.js"></script>
  

</body>

</html>