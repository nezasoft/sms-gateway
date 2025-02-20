<?php
include("../connect/connect.php");

if(!isset($_SESSION['FON_G_USER_ID'])){
echo "<script>alert('Session Expired');window.location.href='../';</script>";
}
//Lets get company name 
$company= $conn->prepare("SELECT company_name FROM company WHERE id='".$_SESSION['FON_G_COMPANY_ID']."' LIMIT 1");
$company->execute();
$company_row = $company->fetch(PDO::FETCH_ASSOC);
$_SESSION['FON_G_COMPANY_NAME']=$company_row['company_name'];
//Lets get statistics 
//SMS Sent
$count_sms = $conn->prepare("SELECT SUM(sms_count) as total_sent FROM outgoing_messages WHERE company_id='".$_SESSION['FON_G_COMPANY_ID']."'");
$count_sms->execute();
$count_sms_rows = $count_sms->fetch(PDO::FETCH_ASSOC);
$total_sms = $count_sms_rows['total_sent'];
//SMS Received
$count_incoming_sms = $conn->prepare("SELECT count(*) AS total_sms FROM outgoing_messages WHERE company_id='".$_SESSION['FON_G_COMPANY_ID']."'");
$count_incoming_sms->execute();
$count_incoming_sms_rows = $count_incoming_sms->fetch(PDO::FETCH_ASSOC);
$total_outgoing_sms = $count_incoming_sms_rows['total_sms'];
//Short Codes
$count_short_codes = $conn->prepare("SELECT count(*) AS total_short_codes FROM company_short_codes AS csc LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id WHERE csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND sc.product_id=1");
$count_short_codes->execute();
$short_code_rows = $count_short_codes->fetch(PDO::FETCH_ASSOC);
$total_short_codes = $short_code_rows['total_short_codes'];

?>
<script src="../js/vendor/jquery-3.3.1.min.js"></script>
<!doctype html>
<html class="no-js" lang="en">
<!-- Frontier Opticals Networks Limited
USSD, Short Code & SMS Platform
Author: Walter Omedo
Date: 28-11-2018
 -->
<style>
#progress_password {
  height: 15px;
  width: 100%;
  margin-top: 0.6em;
}

#progress-bar-password {
  width: 0%;
  height: 100%;
  transition: width 500ms linear;
}

.progress-bar-danger {
  background: #FF3355;
}

.progress-bar-warning {
  background: #FFD433;
}

.progress-bar-success {
  background:#12CB23;
}

.pager,
.pagination {
    margin: 20px 0
}

.breadcrumb>li {
    display: inline-block
}

.breadcrumb>li+li:before {
    padding: 0 5px;
    color: #ccc;
    content: "/\00a0"
}

.breadcrumb>.active {
    color: #777
}

.pagination>li.dot {
    padding: 3px 0
}

.pagination>li>a.current_page {
    background: #0d92e1;
    color: #fff;
    text-decoration: none
}

.progress-bar-striped,
.progress-striped .progress-bar,
.progress-striped .progress-bar-info,
.progress-striped .progress-bar-success,
.progress-striped .progress-bar-warning {
    background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
    background-image: -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
    background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent)
}

.pagination>li>a,
.pagination>li>span {
    background-color: #fff;
    border: 1px solid #ddd
}

.pagination>li>a:focus,
.pagination>li>a:hover,
.pagination>li>span:focus,
.pagination>li>span:hover {
    z-index: 2;
    border-color: #ddd
}

.pagination>.active>a,
.pagination>.active>a:focus,
.pagination>.active>a:hover,
.pagination>.active>span,
.pagination>.active>span:focus,
.pagination>.active>span:hover {
    cursor: default
}

.pagination>.disabled>a,
.pagination>.disabled>a:focus,
.pagination>.disabled>a:hover,
.pagination>.disabled>span,
.pagination>.disabled>span:focus,
.pagination>.disabled>span:hover {
    border-color: #ddd
}
</style>
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>FON | SMS & USSD Platform</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="1800">
    <script>
    //Reload after 30 mins
    setInterval(function(){window.location.reload();},18000000);
    </script>
    
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">

    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="../css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="../css/normalize.css">
    <!-- meanmenu icon CSS
		============================================ -->
    <link rel="stylesheet" href="../css/meanmenu.min.css">
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
	<link rel="stylesheet" href="../css/preloader/preloader-style.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="../style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/alerts.css">
     <link rel="stylesheet" href="../css/modals.css">
    <link rel="stylesheet" href="../css/form/all-type-forms.css">
    <!-- modernizr JS
		============================================ -->
    <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
	<script src="../js/api/system.js"></script>
	
</head>

<body>
    <div class="error-pagewrap">
		<div class="error-page-int">
			<div class="hpanel">
				<div class="panel-body text-center lock-inner">
					<i class="fa fa-lock" aria-hidden="true"></i>
					<br/>
					<h4><span class="text-success">Locked Content!</span> </h4>
					<p>Sorry! You do not have the rights to access this content</p>
				
						<a href="../" class="btn btn-primary block full-width"><font color="#fff;">Go Back </font></a>
				</div>
			</div>
			<div class="text-center login-footer">
				<p>Copyright © <?php echo date('Y'); ?> <a href="https://www.fon.co.ke">Frontier Opticals Networks Ltd</a> All Rights reserved.</p>
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
    <!-- counterup JS
		============================================ -->
    <script src="../js/counterup/jquery.counterup.min.js"></script>
    <script src="../js/counterup/waypoints.min.js"></script>
    <script src="../js/counterup/counterup-active.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="../js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="../js/scrollbar/mCustomScrollbar-active.js"></script>
    <!-- metisMenu JS
		============================================ -->
    <script src="../js/metisMenu/metisMenu.min.js"></script>
    <script src="../js/metisMenu/metisMenu-active.js"></script>
     <!-- sparkline JS
		============================================ -->
    <script src="../js/sparkline/jquery.sparkline.min.js"></script>
    <script src="../js/sparkline/sparkline-active.js"></script>
    <!-- Chart JS
		============================================ -->
    <script src="../js/chart/jquery.peity.min.js"></script>
    <script src="../js/chart/peity-active.js"></script>
     <script src="../js/charts/Chart.js"></script>
   
    <!-- calendar JS
		============================================ -->
    <script src="../js/calendar/moment.min.js"></script>
    <script src="../js/calendar/fullcalendar.min.js"></script>
    <script src="../js/calendar/fullcalendar-active.js"></script>
    <script src="../js/tab.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="../js/plugins.js"></script>
   
    <!-- main JS
		============================================ -->
    <script src="../js/main.js"></script>
   

</body>

</html>