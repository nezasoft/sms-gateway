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
<!-- Frontier Opticals Networks Limited
USSD, Short Code & SMS Platform
Author: Walter Omedo
Date: 28-11-2018
 -->
<?php
include("../connect/connect.php");
if(!isset($_SESSION['FON_G_USER_ID'])){
echo "<script>alert('Session Expired');window.location.href='../';</script>";
}
// Include and instantiate the class.
require_once('../mobile_detect.php');
$detect = new Mobile_Detect;
// Any mobile device (phones or tablets).
if($detect->isMobile()) {
$_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
} 
// Any tablet device.
if( $detect->isTablet()){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
} 
// Exclude tablets.
if($detect->isMobile() && !$detect->isTablet()){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
} 
// Check for a specific platform with the help of the magic methods:
if($detect->isiOS()){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
}
if($detect->isAndroidOS()){
 $_SESSION['FON_IS_MOBILE']=1;
}else{
$_SESSION['FON_IS_MOBILE']=0;
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
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>FON | SMS & USSD Platform</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="../img/favicon.ico">
    <!-- Google Fonts
		============================================ -->

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
    <!-- modernizr JS
		============================================ -->
    <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="../js/vendor/jquery-1.12.4.min.js"></script>
    <script src="../js/exportExcel.js"></script>
</head>

<body>
    <!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
 
    <!-- Start Welcome area -->
    <div class="all-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="logo-pro" style='padding:5px;background-color:#fff;'>
                       
                    </div>
                </div>
            </div>
        </div>
        <div class="header-advance-area">
            <div class="header-top-area">
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
                                <li><a title="New Message" id="new_sms_menu" href="new_message"><span>New Message</span></a></li>
                                <li><a title="Messages" id="list_bulk_sms_menu"  href="?my_account&action=messages"><span>Messages</span></a></li>                            
                            </ul>
                        </li>
                        <?php 
                        }elseif($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){                        
                        ?>
                         <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-bars"></i> <span class="mini-click-non">Bulk SMS</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Message" id="new_sms_menu" href="?my_account&action=new_message"><span>New Message</span></a></li>
                                <li><a title="Messages" id="list_bulk_sms_menu"  href="?my_account&action=messages"><span>Messages</span></a></li>                               
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
                                <li><a title="Short Codes" id="company_short_code_menu" href="?my_account&action=short_codes"><span>Short Codes</span></a></li>
                                <li><a title="Subscriptions" id="subscriptions_menu" href="?my_account&action=subscriptions"><span>Subscriptions</span></a></li>
                            </ul>
                        </li>
                         <?php 
                        }elseif($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){                        
                        ?>
                         <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-exchange"></i> <span class="mini-click-non">Short Codes</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                                <li><a title="New Short Code" id="new_short_code_menu" href="?my_account&action=new_short_code"><span>New Short Code</span></a></li>
                                <li><a title="Short Codes" id="company_short_code_menu" href="?my_account&action=short_codes"><span>Short Codes</span></a></li>
                                <li><a title="Subscriptions" id="subscriptions_menu" href="?my_account&action=subscriptions"><span>Subscriptions</span></a></li>
                            </ul>
                        </li>
                        <?php
                         } 
                         ?> 
                          <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-gears"></i> <span class="mini-click-non">Settings</span></a>
                            <ul class="submenu-angle" aria-expanded="false">
                        
                                <li><a title="Contacts" id="list_contacts_menu" href="?my_account&action=list_contacts"><span>Contacts</span></a></li>
                              <?php if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ ?>
                              <li><a title="Companies" id="list_companies_menu" href="?my_account&action=list_companies"><span>Companies</span></a></li>
                              <li><a title="Product Category" id="list_product_category_menu" href="?my_account&action=list_product_category"><span>Product Category</span></a></li>
        										  <li><a title="Short Codes" id="list_short_codes_menu" href="?my_account&action=list_short_codes"><span>Short Codes</span></a></li>
        										  <li><a title="Access Levels"  id="list_levels_menu" href="?my_account&action=list_access_levels"><span>Access Levels</span></a></li>
 								              <li><a title="Client Categories" id="list_categories_menu" href="?my_account&action=list_categories"><span>Categories</span></a></li>
        								        <?php } ?>
        								        <?php if($_SESSION['FON_G_LEVEL_ID']==4 ||$_SESSION['FON_G_LEVEL_ID']==1){ ?>
        								        <li><a title="Users" id="list_users_menu" href="?my_account&action=list_users"><span>Users</span></a></li>
        								        <?php } ?>
                                    </ul>
                                </li> 
                                <li><a title="My Account" id="my_account_menu" href="?my_account&action=my_account"><span><i class="fa fa-user"></i> My Account</span></a></li>
                                <li><a title="Logout" id="logout_menu" href="../logout?logout"><i class="fa fa-power-off"></i> <span>Logout</span></a></li>						
                            </ul>                          
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
        <!-- Single pro tab review Start-->
        <div class="single-pro-review-area mt-t-30 mg-b-15">
            <div class="container-fluid">
                <div class="row">
                <?php
                if(isset($_GET['my_account']) && $_GET['action']=='list_users'){
                include('views/settings/list_users.html');
                
                }elseif(isset($_GET['my_account']) && $_GET['action']=='list_categories'){
                include('views/settings/list_categories.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='list_access_levels'){
                include('views/settings/list_levels.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='list_short_codes'){
                include('views/settings/list_short_codes.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='list_product_category'){
                include('views/settings/list_product_categories.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='list_contacts'){
                include('views/settings/list_contacts.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='short_codes'){
                include('views/short_codes/list_short_codes.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='view_messages'){
                include('views/short_codes/messages.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='subscriptions'){
                include('views/short_codes/list_subscriptions.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='new_message'){
                include('views/bulksms/new_message.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='messages'){
                include('views/bulksms/messages.html');
                }elseif(isset($_GET['my_account']) && $_GET['action']=='my_account'){
                include('views/settings/my_account.html');
                }
                
                ?>                
                </div>
            </div>
        </div>
        <div class="footer-copyright-area">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="footer-copy-right">
                            <p>Copyright &copy <?php echo date('Y'); ?> <a href="https://www.fon.co.ke/">FON Gateway.</a> </p>
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
    <!-- morrisjs JS
		============================================ -->
    <script src="../js/sparkline/jquery.sparkline.min.js"></script>
    <script src="../js/sparkline/jquery.charts-sparkline.js"></script>
    <!-- calendar JS
		============================================ -->
    <script src="../js/calendar/moment.min.js"></script>
    <script src="../js/calendar/fullcalendar.min.js"></script>
    <script src="../js/calendar/fullcalendar-active.js"></script>
    <!-- maskedinput JS
		============================================ -->
    <script src="../js/jquery.maskedinput.min.js"></script>
    <script src="../js/masking-active.js"></script>
    <!-- datepicker JS
		============================================ -->
    <script src="../js/datepicker/jquery-ui.min.js"></script>
    <script src="../js/datepicker/datepicker-active.js"></script>
    <!-- form validate JS
		============================================ -->
    <script src="../js/form-validation/jquery.form.min.js"></script>
    <script src="../js/form-validation/jquery.validate.min.js"></script>
    <script src="../js/form-validation/form-active.js"></script>
    <!-- dropzone JS
		============================================ -->
    <script src="../js/dropzone/dropzone.js"></script>
    <!-- tab JS
		============================================ -->
    <script src="../js/tab.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="../js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="../js/main.js"></script>


</body>

</html>