<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Update Password | FON Gateway | SMS, Short Code & USSD Platform</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Play:400,700" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="../css/owl.carousel.css">
    <link rel="stylesheet" href="../css/owl.theme.css">
    <link rel="stylesheet" href="../css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="../css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="../css/normalize.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="../css/main.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="../css/morrisjs/morris.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="../css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- metisMenu CSS
		============================================ -->
    <link rel="stylesheet" href="../css/metisMenu/metisMenu.min.css">
    <link rel="stylesheet" href="../css/metisMenu/metisMenu-vertical.css">
    <!-- calendar CSS
		============================================ -->
    <link rel="stylesheet" href="../css/calendar/fullcalendar.min.css">
    <link rel="stylesheet" href="../css/calendar/fullcalendar.print.min.css">
    <!-- forms CSS
		============================================ -->
    <link rel="stylesheet" href="../css/form/all-type-forms.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="../style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="../css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="../js/vendor/jquery-3.3.1.min.js"></script>
</head>
<?php
$email =$_GET['email'];
$token = $_GET['token'];
?>
<body>
    <!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
	<div class="error-pagewrap">
		<div class="error-page-int">	
			<div class="content-error">      
				<div class="hpanel">
                  <div class="alert alert-warning" role="alert">
                                <strong>Password Update</strong>Kindly update your password...
                            </div>

   <div class="panel-body" style="border:1px solid #000;">
                  
                           <div id="form_content">
                           <div class="text-center m-b-md custom-login">
                    				<h3>Update Password</h3>
                    				
                    			</div>
                            <hr/>
                            <div class="form-group">
                                <label class="control-label" for="username">New Password</label>
                                <input type="password" placeholder="New Password" title="Please your new password" required="" value="" name="password" id="password" class="form-control">
                                <span class="help-block small" id="password_error"><font color="red">*Enter your new password</font></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Confirm Password</label>
                                <input type="password" title="Please confirm your password" placeholder="Confirm Password" required="" value="" name="confirm_password" id="confirm_password" class="form-control">
                                <span class="help-block small" id="confirm_password_error"><font color="red">* Confirm  your password</font></span>
                               <span class="help-block small" id="match_password_error"><font color="red">* Passwords do not match </font></span>
                            </div>
                          
                            <button id="update" class="btn btn-success btn-block loginbtn">Change Password</button>
							</div>
                            <div id="response" style="margin:10px;padding:10px;"></div>
                      
                       
                    </div>
                    
                </div>
                
			</div>
			<a id="loader"><img src="../img/loader.gif" width="50" height="50" /></a>
			<script>
			$(document).ready(function(){
			     token='<?php echo $token; ?>';	
                             email='<?php echo $email; ?>';
			      //Hide errors
				$("#password_error").hide();
				$("#confirm_password_error").hide();
                $('#match_password_error').hide();
				$("#loader").hide();
				$("#update").click(function(){
				    error = false;
					password = $("#password").val();
					confirm_password = $("#confirm_password").val();
					
					$("#response").empty();					
					if(password===""){
					error = true;
					$("#password_error").show();
					$("#password").css("border-color","red");
					$("#password").focus();
					}else{
						$("#password_error").hide();
					    $("#password").css("border-color","green");					
					}
					
					if(confirm_password===""){
					error = true;
					$("#confirm_password_error").show();
					$("#confirm_password").css("border-color","red");
					$("#confirm_password").focus();
					}else{
					   $("#confirm_password_error").hide();
					   $("#confirm_password").css("border-color","green");					
					}                                        
                     if(password!==confirm_password){
                     error = true;
                      $("#match_password_error").show();
                      $("#confirm_password").css("border-color","red");
				       $("#confirm_password").focus();
                     }else{
                     $("#match_password_error").hide();
                     $("#confirm_password").css("border-color","green");
					}
					
if(error == false){
//Hide Button
$("#update").hide();
//Show loader
$("#loader").show();
myData = "password=" + password + "&confirm_password=" + confirm_password + "&token=" + token + "&email=" + email ;
jQuery.ajax({
type: "POST",
url: "../process/update_password.php",
dataType: "text",
data: myData,
success: function(b) {
$("#response").append(b);
$("#loader").hide();
$("#update").show();
$("#response").show();
}
});
}
});
	
$("#password").focusout(function(){
				  password = $("#password").val();
					error = false;
					if(password===""){
					error = true;
					$("#password_error").show();
					$("#password").css("border-color","red");
					$("#password").focus();
					}else{
						$("#password_error").hide();
					   $("#password").css("border-color","green");
					
					}
				});
				
				$("#confirm_password").focusout(function(){
				       password = $("#password").val();	
                       confirm_password = $("#confirm_password").val();
					   error = false;
					if(confirm_password===""){
					error = true;
					$("#confirm_password_error").show();
					$("#confirm_password").css("border-color","red");
					$("#confirm_password").focus();
					}else{
						$("#confirm_password_error").hide();
					    $("#confirm_password").css("border-color","green");					
					}
                   if(password!==confirm_password){
                     error = true;
				  $("#match_password_error").show();
 				  $("#confirm_password").css("border-color","red");
 				  $("#confirm_password").focus();
                   }else{
                   error = false;
                    $("#match_password_error").hide();
                    $("#confirm_password").css("border-color","green");                                 
				   }
				});	

			});
			</script>
			<div class="text-center login-footer">
				<p>Copyright &copy; 2019 <a href="https://www.fon.co.ke">Frontier Opticals Ltd</a> All rights reserved.</p>
			</div>
		</div>   
    </div>
    <!-- jquery
		============================================ -->
    
    <!-- bootstrap JS
		============================================ -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="../js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="../js/jquery-price-slider.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="../js/jquery.meanmenu.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="../js/owl.carousel.min.js"></script>
    <!-- sticky JS
		============================================ -->
    <script src="../js/jquery.sticky.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="../js/jquery.scrollUp.min.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="../js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="../js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="../js/metisMenu/metisMenu.min.js"></script>
    <script src="../js/metisMenu/metisMenu-active.js"></script>
    <!-- tab JS
		============================================ -->
    <script src="../js/tab.js"></script>
    <!-- icheck JS
		============================================ -->
    <script src="../js/icheck/icheck.min.js"></script>
    <script src="../js/icheck/icheck-active.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="../js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="../js/main.js"></script>
  

</body>

</html>

