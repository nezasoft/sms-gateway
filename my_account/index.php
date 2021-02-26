<?php
include("../connect/connect.php");
ini_set("display_errors",false);
if(!isset($_SESSION['FON_G_USER_ID'])){
echo "<script>alert('Session Expired');window.location.href='../';</script>";
}

// Include and instantiate the class.
require_once('../mobile_detect.php');
$detect = new Mobile_Detect;
// Any mobile device (phones or tablets).
if($detect->isMobile() ) {
$_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
}
 
// Any tablet device.
if( $detect->isTablet() ){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
}
 
// Exclude tablets.
if( $detect->isMobile() && !$detect->isTablet() ){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
}
 
// Check for a specific platform with the help of the magic methods:
if( $detect->isiOS()){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
}
if($detect->isAndroidOS() ){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
}

if($_SESSION['FON_IS_MOBILE']==1){
echo "<script>window.location.href='../mobile?my_account&action=short_codes';</script>";
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
$count_incoming_sms = $conn->prepare("SELECT count(*) AS total_sms FROM incoming_messages WHERE company_id='".$_SESSION['FON_G_COMPANY_ID']."'");
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
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>FON | SMS & USSD Platform</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta>
    <script>
    //Reload after 30 mins
    //setInterval(function(){window.location.reload();},18000000);
    //Check for internet connectivity
    var ifConnected = window.navigator.onLine;
    
    if (ifConnected) {
      $('#check_online').css('color','green');
      $('#check_online').html('Online - You are connect to the internet!');
    } else {
       $('#check_online').css('color','red');
       $('#check_online').html('Offline - Check your internet connection!');
      
    }
  setInterval(function(){ 
   
   
   if (ifConnected) {
      $('#check_online').css('color','green');
      $('#check_online').html('Online - You are connected to the internet!');
    } else {
       $('#check_online').css('color','red');
       $('#check_online').html('Offline - Check your internet connection!');
      
    }
 

}, 3000);
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
    <!-- forms CSS
		============================================ -->
    <link rel="stylesheet" href="../css/form/all-type-forms.css">
    <!-- educate icon CSS
		============================================ -->
    <link rel="stylesheet" href="../css/educate-custon-icon.css">
    <!-- morrisjs CSS
		============================================ -->
    <link rel="stylesheet" href="../css/morrisjs/morris.css">
    <!-- dropzone CSS
		============================================ -->
    <link rel="stylesheet" href="../css/dropzone/dropzone.css">
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
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="../style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/alerts.css">
     <link rel="stylesheet" href="../css/modals.css">
    <link rel="stylesheet" href="../css/form/all-type-forms.css">


  <script src="../js/datapicker/bootstrap-datepicker.js"></script>
    <script src="../js/datapicker/datepicker-active.js"></script>

	 <!-- notifications CSS
		============================================ -->
    <link rel="stylesheet" href="../css/notifications/Lobibox.min.css">
    <link rel="stylesheet" href="../css/notifications/notifications.css">
    <!-- modernizr JS
		============================================ -->
    <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
	 <script src="../js/api/system.js"></script>
   <script src="../js/exportExcel.js"></script>
	
</head>

<body>
    <!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
    <!-- Start Left menu area -->
    <div class="left-sidebar-pro">
        <nav id="sidebar" class="">           
            <div class="left-custom-menu-adp-wrap comment-scrollbar">
                <nav class="sidebar-nav left-sidebar-menu-pro">
                    <ul class="metismenu" id="menu1">
                <li class="active"><a  href="#"><span class="fa fa-home"></span>
								   <span class="mini-click-non">Home</span>
								</a>
                        </li>
                        <?php 
                        //Lets decide whether to decide this menu or not based on whether the company subscribed to this product or not
                        $confirm_sms = $conn->prepare("SELECT csc.id AS short_code_id,sc.short_code,sc.product_id,company_id 
                        										 FROM company_short_codes AS csc 
                                                       LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id
                                                       WHERE csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND sc.product_id=2");
                         $confirm_sms->execute();
                         $confirm_sms_count = $confirm_sms->rowCount();
                         if($confirm_sms_count>=1) {                             
                        ?>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bars"></i> <span class="mini-click-non">Bulk SMS</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Message" id="new_sms_menu" href="#"><span>New Message</span></a></li>
                                <li><a title="Messages" id="list_bulk_sms_menu"  href="#"><span>Messages</span></a></li>
                                <li><a title="Message Groups" id="list_sms_groups"  href="#"><span>Message Groups</span></a></li>
                                <li><a title="Schedules" id="list_schedules"  href="#"><spa>Schedules</span></a></li>


                            </ul>
                        </li>
                        <?php 
                        }elseif($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){
                        
                        ?>
                         <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bars"></i> <span class="mini-click-non">Bulk SMS</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Message" id="new_sms_menu" href="#"><span>New Message</span></a></li>
                                <li><a title="Messages" id="list_bulk_sms_menu"  href="#"><span>Messages</span></a></li>
			        <li><a title="Message Groups" id="list_sms_groups"  href="#"><span>Message Groups</span></a></li>
                                 <li><a title="Schedules" id="list_schedules"  href="#"><span>Schedules</span></a></li>
                                <li><a title="Scheduled Messages" id="list_scheduled_messages"  href="#"><span>Scheduled Messages</span></a></li>




                                

                            </ul>
                        </li>
                        <?php

                         } 
                         ?>
                      
                        <?php
                        //Lets confirm for short codes too
                        $confirm_short_codes = $conn->prepare("SELECT csc.id AS short_code_id,sc.short_code,sc.product_id,company_id 
                        													FROM company_short_codes AS csc 
                        													LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id
                        													WHERE csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND sc.product_id=1");
                        $confirm_short_codes->execute();
                        $confirm_short_code_count = $confirm_short_codes->rowCount();
                        if($confirm_short_code_count>=1)	{												
                        ?>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-exchange"></i> <span class="mini-click-non">Short Codes</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                             <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                                <li><a title="New Short Code" id="new_short_code_menu" href="#"><span>New Short Code</span></a></li>
                                <?php } ?>
                                <li><a title="Short Codes" id="company_short_code_menu" href="#"><span>Short Codes</span></a></li>
                                <li><a title="Subscriptions" id="subscriptions_menu" href="#"><span>Subscriptions</span></a></li>
                            </ul>
                        </li>
                         <?php 
                        }elseif($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){
                        
                        ?>
                         <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-exchange"></i> <span class="mini-click-non">Short Codes</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Short Code" id="new_short_code_menu" href="#"><span>New Short Code</span></a></li>
                                <li><a title="Short Codes" id="company_short_code_menu" href="#"><span>Short Codes</span></a></li>
                                <li><a title="Subscriptions" id="subscriptions_menu" href="#"><span>Subscriptions</span></a></li>
                            </ul>
                        </li>
                        <?php
                         } 
                         ?>
                        <li>
                            <a  id="list_menu_statistics" href="#" aria-expanded="false"><i class="fa fa-bar-chart"></i> <span class="mini-click-non">Statistics</span></a>
                           <!-- <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="USSD Statistics" href="statistics?action=ussd_stats"><span>USSD</span></a></li>
                                <li><a title="Short Code Statistics" href="statistics?action=short_codes"><span>Short Code</span></a></li>
                                <li><a title="SMS Statistics" href="statistics?action=bulk_sms"><span>Bulk SMS</span></a></li>
                            </ul>-->
                        </li>  
<?php
 if($confirm_sms_count>=1) {

?>

<li>
                            <a  id="list_payments" href="#" aria-expanded="false"><i class="fa fa-money"></i> <span class="mini-click-non">Payments</span></a>
                        </li> 
<?php

}

?>
                          <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-gears"></i> <span class="mini-click-non">Settings</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                             <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                                <li><a title="Companies" id="list_companies_menu" href="#"><span>Companies</span></a></li>
                              <?php } ?>  
                                <li><a title="Contacts" id="list_contacts_menu" href="#"><span>Contacts</span></a></li>
								<li><a title="Groups" id="list_contact_groups" href="#"><span>Groups</span></a></li>
                              <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                                <li><a title="Product Category" id="list_product_category_menu" href="#"><span>Product Category</span></a></li>
										  <li><a title="Short Codes" href="#" id="list_short_codes_menu"><span>Short Codes</span></a></li>
										  
										  <li><a title="Access Levels" href="#" id="list_levels_menu"><span>Access Levels</span></a></li>				
				                         <li><a title="Client Categories" id="list_categories_menu" href="#"><span>Categories</span></a></li>
                                         <li><a title="System Settings" id="list_system_menu" href="#"><span>System Settings</span></a></li>
                                         <li><a title="Client SMS Package" id="list_client_package" href="#"><span>Client Package</span></a></li>
								        <?php } ?>
								        <?php if($_SESSION['FON_G_LEVEL_ID']==4 ||$_SESSION['FON_G_LEVEL_ID']==1){ ?>
								        <li><a title="Users" id="list_users_menu" href="#"><span>Users</span></a></li>
								        <?php } ?>
                            </ul>
                        </li> 						
                    </ul>
                </nav>
            </div>
        </nav>
    </div>
  
    <?php if($_SESSION['FON_IS_MOBILE']==1){ ?>
     <!-- Mobile Menu start -->
            <div class="mobile-menu-area">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="mobile-menu">
                                <nav id="dropdown">
                                    <ul class="mobile-menu-nav">
                             <li class="active">
                                   <a  href="#">
								                   <span class="fa fa-home"></span>
								                   <span class="mini-click-non">Home</span>
								                   </a>
                           </li>
                        <?php 
                        //Lets decide whether to decide this menu or not based on whether the company subscribed to this product or not
                        $confirm_sms = $conn->prepare("SELECT csc.id AS short_code_id,sc.short_code,sc.product_id,company_id 
                        										 FROM company_short_codes AS csc 
                                                       LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id
                                                       WHERE csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND sc.product_id=2");
                         $confirm_sms->execute();
                         $confirm_sms_count = $confirm_sms->rowCount();
                         if($confirm_sms_count>=1) {                             
                        ?>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bars"></i> <span class="mini-click-non">Bulk SMS</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Message" id="new_sms_menu" href="#"><span>New Message</span></a></li>
                                <li><a title="Messages" id="list_bulk_sms_menu"  href="#"><span>Messages</span></a></li>
                                

                            </ul>
                        </li>
                        <?php 
                        }elseif($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){
                        
                        ?>
                         <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bars"></i> <span class="mini-click-non">Bulk SMS</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Message" id="new_sms_menu" href="#"><span>New Message</span></a></li>
                                <li><a title="Messages" id="list_bulk_sms_menu"  href="#"><span>Messages</span></a></li>
                                

                            </ul>
                        </li>
                        <?php
                         } 
                         ?>
                      
                        <?php
                        //Lets confirm for short codes too
                        $confirm_short_codes = $conn->prepare("SELECT csc.id AS short_code_id,sc.short_code,sc.product_id,company_id 
                        													FROM company_short_codes AS csc 
                        													LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id
                        													WHERE csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND sc.product_id=1");
                        $confirm_short_codes->execute();
                        $confirm_short_code_count = $confirm_short_codes->rowCount();
                        if($confirm_short_code_count>=1)	{												
                        ?>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-exchange"></i> <span class="mini-click-non">Short Codes</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                             <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                                <li><a title="New Short Code" id="new_short_code_menu" href="#"><span>New Short Code</span></a></li>
                                <?php } ?>
                                <li><a title="Short Codes" id="company_short_code_menu" href="#"><span>Short Codes</span></a></li>
                                <li><a title="Subscriptions" id="subscriptions_menu" href="#"><span>Subscriptions</span></a></li>
                            </ul>
                        </li>
                         <?php 
                        }elseif($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){
                        
                        ?>
                         <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-exchange"></i> <span class="mini-click-non">Short Codes</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Short Code" id="new_short_code_menu" href="#"><span>New Short Code</span></a></li>
                                <li><a title="Short Codes" id="company_short_code_menu" href="#"><span>Short Codes</span></a></li>
                                <li><a title="Subscriptions" id="subscriptions_menu" href="#"><span>Subscriptions</span></a></li>
                            </ul>
                        </li>
                        <?php
                         } 
                         ?>
                        <li>
                            <a  id="list_menu_statistics" href="#" aria-expanded="false"><i class="fa fa-bar-chart"></i> <span class="mini-click-non">Statistics</span></a>
                           <!-- <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="USSD Statistics" href="statistics?action=ussd_stats"><span>USSD</span></a></li>
                                <li><a title="Short Code Statistics" href="statistics?action=short_codes"><span>Short Code</span></a></li>
                                <li><a title="SMS Statistics" href="statistics?action=bulk_sms"><span>Bulk SMS</span></a></li>
                            </ul>-->
                        </li>  
                          <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-gears"></i> <span class="mini-click-non">Settings</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                             <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                                <li><a title="Companies" id="list_companies_menu" href="#"><span>Companies</span></a></li>
                              <?php } ?>  
                                <li><a title="Contacts" id="list_contacts_menu" href="#"><span>Contacts</span></a></li>
                              <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                                <li><a title="Product Category" id="list_product_category_menu" href="#"><span>Product Category</span></a></li>
										  <li><a title="Short Codes" href="#" id="list_short_codes_menu"><span>Short Codes</span></a></li>
										  <li><a title="Access Levels" href="#" id="list_levels_menu"><span>Access Levels</span></a></li>
								        <li><a title="Client Categories" id="list_categories_menu" href="#"><span>Categories</span></a></li>
								        <?php } ?>
								        <?php if($_SESSION['FON_G_LEVEL_ID']==4 ||$_SESSION['FON_G_LEVEL_ID']==1){ ?>
								        <li><a title="Users" id="list_users_menu" href="#"><span>Users</span></a></li>
								        <?php } ?>
                            </ul>
                        </li> 						
                    </ul>
                           
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <!-- Mobile Menu end -->
    <!-- End Left menu area -->
    <!-- Start Welcome area -->
    <div class="all-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="logo-pro">
                        <a href="#"><img class="main-logo" src="../img/logo/logo.png" alt="Frontier Optical Networks Limited" /></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-advance-area">
            <div class="header-top-area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="header-top-wraper">
                                <div class="row">
                                    <div class="col-lg-1 col-md-0 col-sm-1 col-xs-12">
                                        <div class="menu-switcher-pro">
                                            <button type="button" id="sidebarCollapse" class="btn bar-button-pro header-drl-controller-btn btn-info navbar-btn">
													<i class="fa fa-bars"></i>
												</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-12">
                                        <div class="header-top-menu tabl-d-n">
                                         
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                        <div class="header-right-info">
                                            <ul class="nav navbar-nav mai-top-nav header-right-menu">
                                              
                                            
                                                <li class="nav-item">
                                               <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle">
															<img src="../img/profile.png" alt="<?php echo titleCase($_SESSION['FON_G_FNAME']); ?>" />
															<span class="admin-name">Hi, <?php echo titleCase($_SESSION['FON_G_FNAME']); ?></span>
															<i class="fa fa-angle-down edu-icon edu-down-arrow"></i>
														</a>
                                                    <ul role="menu" class="dropdown-header-top author-log dropdown-menu animated zoomIn">
                                                        <li><a id="list_menu_my_account" href="#"><span class="edu-icon edu-home-admin author-log-ic"></span>My Account</a>
                                                        </li>
                                                   
                                                        <li><a href="../logout?logout"><span class="edu-icon edu-locked author-log-ic"></span>Log Out</a>
                                                        </li>
                                                    </ul>
                                                </li>
       
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- Mobile Menu end -->
                    <div class="row" style="margin:10px; background-color:#fff; padding-top:10px;">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="breadcome-heading">
                                           <h4>FON Gateway | SMS, Short Code & USSD </h4>
                                            <span id="check_online"></span>   
                                     </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <ul class="breadcome-menu">
                                            <li><a href="#">Home</a> <span class="bread-slash">/</span>
                                            </li>
                                            <li><span class="bread-blod">V 1.0.1</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
        </div>
        
        <div class="product-sales-area mg-tb-30" style="padding-left:20px;min-height:500px;">
            <div class="container-fluid">
                <div class="row">
                    <?php
					//include("views/new_sms.html");
					?>
					<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12"  >
                    <div id="wait" style="padding:20px; marging:20px;"><img src="../img/loader.gif" width="40" height="40" /><h4>Loading content, please wait...</h4></div>					
	      				
         <div id="page_content" style="min-height:500px;"></div>	
             <div id="other_content" >
									
					 </div>	
                    
					</div>	
					
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                       <!--<div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 res-mg-t-30 table-mg-t-pro-n tb-sm-res-d-n dk-res-t-d-n">
                            <h4>USSD Codes</h4>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash"></div>
                                </li>
                                <li class="text-right sp-cn-r"><i class="fa fa-level-down" aria-hidden="true"></i> <span class="counter text-success">0</span></li>
                            </ul>
                        </div> -->
                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <h4>SMS Sent</h4>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash2"></div>
                                </li>
                                <li class="text-right graph-two-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-purple"><?php echo $total_sms; ?></span></li>
                            </ul>
                        </div>
                        <div class="white-box analytics-info-cs mg-b-10 res-mg-b-30 tb-sm-res-d-n dk-res-t-d-n">
                            <h4>SMS Received</h4>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash3"></div>
                                </li>
                                <li class="text-right graph-three-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-info"><?php echo $total_outgoing_sms; ?></span></li>
                            </ul>
                        </div>
                        <div class="white-box analytics-info-cs table-dis-n-pro tb-sm-res-d-n dk-res-t-d-n">
                            <h4>Short Codes</h4>
                            <ul class="list-inline two-part-sp">
                                <li>
                                    <div id="sparklinedash4"></div>
                                </li>
                                <li class="text-right graph-three-ctn"><i class="fa fa-level-up" aria-hidden="true"></i> <span class="counter text-info"><?php  echo $total_short_codes;?></span></li>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-copyright-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="footer-copy-right">
                            <p>Copyright &copy <?php echo date('Y'); ?> <a href="https://www.fon.co.ke">Frontier Opticals Networks Limited</a> All rights reserved.</p>
                        </div>
                    </div>
                </div>
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
	   <script src="../js/notifications/Lobibox.js"></script>
   
           <script src="../js/datapicker/bootstrap-datepicker.js"></script>
    <script src="../js/datapicker/datepicker-active.js"></script>
   
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
   <script>
   // Notifications Custom Animation active class
	Lobibox.notify('info', {
                    showClass: 'fadeInDown',
                    hideClass: 'fadeUpDown',
                    msg: 'You can now view the distribution of your contacts or viewers by region under the statistics section'
                });
   </script>

</body>
    <!-- JS Demo -->
<!--Start Show Session Expire Warning Popup here -->
 <div class="modal fade" id="session-expire-warning-modal" aria-hidden="true" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header"> <h4 class="modal-title">Session Expire Warning</h4> </div><div class="modal-body"> Your session will expire in <span id="seconds-timer"></span> seconds. Do you want to extend the session? </div><div class="modal-footer"> <button id="btnOk" type="button" class="btn btn-blue-alt">Ok</button> <button id="btnSessionExpiredCancelled" type="button" class="btn btn-danger" data-dismiss="modal" >Cancel</button> <button id="btnLogoutNow" type="button" class="btn btn-success">Logout now</button> </div></div></div></div><div class="modal fade" id="session-expired-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> <div class="modal-dialog" role="document"> <div class="modal-content"> <div class="modal-header"> <h4 class="modal-title">Session Expired</h4> </div><div class="modal-body"> Your session is expired. </div><div class="modal-footer"> <button id="btnExpiredOk" onClick="sessionExpiredRedirect()" type="button" class="btn btn-blue-alt" data-dismiss="modal" >Ok</button> </div></div></div></div>
<script>
var sessServerAliveTime = 10 * 60 * 60;
var sessionTimeout = 1 * 1800000;
var sessLastActivity;
var idleTimer, remainingTimer;
var isTimout = false;
var sess_intervalID, idleIntervalID;
var sess_lastActivity;
var timer;
var isIdleTimerOn = false;
localStorage.setItem('sessionSlide', 'isStarted');

function sessPingServer() {
    if (!isTimout) {
        //$.ajax({
        //    url: '/Admin/SessionTimeout',
        //    dataType: "json",
        //    async: false,
        //    type: "POST"
        //});

        return true;
    }
}

function sessServerAlive() {
    sess_intervalID = setInterval('sessPingServer()', sessServerAliveTime);
}

function initSessionMonitor() {
    $(document).bind('keypress.session', function (ed, e) {
        sessKeyPressed(ed, e);
    });
    $(document).bind('mousedown keydown', function (ed, e) {

        sessKeyPressed(ed, e);
    });
    sessServerAlive();
    startIdleTime();
}

$(window).scroll(function (e) {
    localStorage.setItem('sessionSlide', 'isStarted');
    startIdleTime();
});

function sessKeyPressed(ed, e) {
    var target = ed ? ed.target : window.event.srcElement;
    var sessionTarget = $(target).parents("#session-expire-warning-modal").length;

    if (sessionTarget != null && sessionTarget != undefined) {
        if (ed.target.id != "btnSessionExpiredCancelled" && ed.target.id != "btnSessionModal" && ed.currentTarget.activeElement.id != "session-expire-warning-modal" && ed.target.id != "btnExpiredOk"
             && ed.currentTarget.activeElement.className != "modal fade modal-overflow in" && ed.currentTarget.activeElement.className != 'modal-header'
    && sessionTarget != 1 && ed.target.id != "session-expire-warning-modal") {
            localStorage.setItem('sessionSlide', 'isStarted');
            startIdleTime();
        }
    }
}

function startIdleTime() {
    stopIdleTime();
    localStorage.setItem("sessIdleTimeCounter", $.now());
    idleIntervalID = setInterval('checkIdleTimeout()', 1000);
    isIdleTimerOn = false;
}

var sessionExpired = document.getElementById("session-expired-modal");
function sessionExpiredClicked(evt) {
    window.location = "../logout?logout";
}

sessionExpired.addEventListener("click", sessionExpiredClicked, false);
function stopIdleTime() {
    clearInterval(idleIntervalID);
    clearInterval(remainingTimer);
}

function checkIdleTimeout() {
     // $('#sessionValue').val() * 60000;
    var idleTime = (parseInt(localStorage.getItem('sessIdleTimeCounter')) + (sessionTimeout)); 
    if ($.now() > idleTime + 60000) {
        $("#session-expire-warning-modal").modal('hide');
        $("#session-expired-modal").modal('show');
        clearInterval(sess_intervalID);
        clearInterval(idleIntervalID);

        $('.modal-backdrop').css("z-index", parseInt($('.modal-backdrop').css('z-index')) + 100);
        $("#session-expired-modal").css('z-index', 1500);
        $('#btnExpiredOk').css('background-color', '#428bca');
        $('#btnExpiredOk').css('color', '#fff');

        isTimout = true;

        sessLogOut();

    }
    else if($.now() > idleTime) {
        ////var isDialogOpen = $("#session-expire-warning-modal").is(":visible");
        if (!isIdleTimerOn) {
            ////alert('Reached idle');
            localStorage.setItem('sessionSlide', false);
            countdownDisplay();

            $('.modal-backdrop').css("z-index", parseInt($('.modal-backdrop').css('z-index')) + 500);
            $('#session-expire-warning-modal').css('z-index', 1500);
            $('#btnOk').css('background-color', '#428bca');
            $('#btnOk').css('color', '#fff');
            $('#btnSessionExpiredCancelled').css('background-color', '#428bca');
            $('#btnSessionExpiredCancelled').css('color', '#fff');
            $('#btnLogoutNow').css('background-color', '#428bca');
            $('#btnLogoutNow').css('color', '#fff');

            $("#seconds-timer").empty();
            $("#session-expire-warning-modal").modal('show');

            isIdleTimerOn = true;
        }
    }
}

$("#btnSessionExpiredCancelled").click(function () {
    $('.modal-backdrop').css("z-index", parseInt($('.modal-backdrop').css('z-index')) - 500);
});

$("#btnOk").click(function () {
    $("#session-expire-warning-modal").modal('hide');
    $('.modal-backdrop').css("z-index", parseInt($('.modal-backdrop').css('z-index')) - 500);
    startIdleTime();
    clearInterval(remainingTimer);
    localStorage.setItem('sessionSlide', 'isStarted');
});

$("#btnLogoutNow").click(function () {
    localStorage.setItem('sessionSlide', 'loggedOut');
    window.location = "../logout?logout";
    sessLogOut();
    $("#session-expired-modal").modal('hide');

});
$('#session-expired-modal').on('shown.bs.modal', function () {
    $("#session-expire-warning-modal").modal('hide');
    $(this).before($('.modal-backdrop'));
    $(this).css("z-index", parseInt($('.modal-backdrop').css('z-index')) + 1);
});

$("#session-expired-modal").on("hidden.bs.modal", function () {
    window.location = "Logout.html";
});
$('#session-expire-warning-modal').on('shown.bs.modal', function () {
    $("#session-expire-warning-modal").modal('show');
    $(this).before($('.modal-backdrop'));
    $(this).css("z-index", parseInt($('.modal-backdrop').css('z-index')) + 1);
});

function countdownDisplay() {

    var dialogDisplaySeconds = 60;

    remainingTimer = setInterval(function () {
        if (localStorage.getItem('sessionSlide') == "isStarted") {
            $("#session-expire-warning-modal").modal('hide');
            startIdleTime();
            clearInterval(remainingTimer);
        }
        else if (localStorage.getItem('sessionSlide') == "loggedOut") {         
            $("#session-expire-warning-modal").modal('hide');
            $("#session-expired-modal").modal('show');
        }
        else {

            $('#seconds-timer').html(dialogDisplaySeconds);
            dialogDisplaySeconds -= 1;
        }
    }
    , 1000);
};

function sessLogOut() {
   // $.ajax({
   //     url: 'Logout.html',
   //     dataType: "json",
  //      async: false,
  //      type: "POST"
 //   });
    
    window.location = "../logout?logout";
}
	</script>
</html>

