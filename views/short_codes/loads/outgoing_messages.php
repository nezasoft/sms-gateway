<?php
//Make a connection
include("../../../connect/connect.php");

           $company_id = $_SESSION['FON_G_COMPANY_ID'];
           //check if user want all records or has wants a filtered result set
           if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
           $val = sanitize_string($_GET['filter']);
           $short_code = $_GET['short_code'];
           $company_id = $_SESSION['FON_G_COMPANY_ID'];
           $filter = 'LIMIT '.$val;
           $url = '?short_codes&action=view_messages&filter='.$val.'&client_id='.$company_id.'&';
           }else{
           $short_code = $_GET['short_code'];
           $company_id = $_SESSION['FON_G_COMPANY_ID'];
           $val = 50;
           $filter = '';
           $url = '?short_codes&action=view_messages&client_id='.$company_id;
           }
           
           if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){         
            $where = "WHERE  om.short_code_id='".$short_code."' AND om.company_id LIKE '%%'";
           }else{
            $where = "WHERE  om.short_code_id='".$short_code."' AND om.company_id='".$_SESSION['FON_G_COMPANY_ID']."'";
           }
		   $query = $conn->prepare("SELECT om.id AS message_id FROM outgoing_messages AS om LEFT JOIN users AS us ON us.id = om.user_id ".$where."");
		   $query->execute();
		   $num_rows = $query->rowCount();

            //save num rows in session
            $_SESSION['FON_SESS_NUM_ROWS'] = $num_rows;
            //save the url in session
            $_SESSION['FON_SESS_FILTER_URL'] = $url;
			//function to paginate the results
		   function displayPaginationBelow($per_page,$page){
			$page_url=$_SESSION['FON_SESS_FILTER_URL'];//page link
			$total = $_SESSION['FON_SESS_NUM_ROWS'];
			$adjacents = "2";
			$page = ($page == 0 ? 1 : $page);
			$start = ($page - 1) * $per_page;
			$prev = $page - 1;
			$next = $page + 1;
			$setLastpage = ceil($total/$per_page);
			$lpm1 = $setLastpage - 1;
			$setPaginate = "";
			if($setLastpage > 1){
			$setPaginate .= "<ul id='pagination' class='pagination pull-right'>";
			$setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
			if ($setLastpage < 7 + ($adjacents * 2))
			{
			for ($counter = 1; $counter <= $setLastpage; $counter++)
			{
			if ($counter == $page)
			$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
			else
			$setPaginate.= "<li><a OnClick='getdata($counter)' href='javascript:void(0);'>$counter</a></li>";
			}
			}
			elseif($setLastpage > 5 + ($adjacents * 2))
			{
			if($page < 1 + ($adjacents * 2))
			{
			for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
			{
			if ($counter == $page)
			$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
			else
			$setPaginate.= "<li><a  OnClick='getdata($counter)' href='javascript:void(0);'>$counter</a></li>";
			}
			$setPaginate.= "<li class='dot'>...</li>";
			$setPaginate.= "<li><a OnClick='getdata($lpm1)' href='javascript:void(0);'>$lpm1</a></li>";
			$setPaginate.= "<li><a  OnClick='getdata($setLastpage)' href='javascript:void(0);'>$setLastpage</a></li>";
			}
			elseif($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata(1)'>1</a></li>";
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata(2)'>2</a></li>";
			$setPaginate.= "<li class='dot'>...</li>";
			for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
			{
			if ($counter == $page)
			$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
			else
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata($counter)'>$counter</a></li>";
			}
			$setPaginate.= "<li class='dot'>..</li>";
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata($lpm1)'>$lpm1</a></li>";
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata($setLastpage)'>$setLastpage</a></li>";
			}
			else
			{
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata(1)'>1</a></li>";
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata(2)'>2</a></li>";
			$setPaginate.= "<li class='dot'>..</li>";
			for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++)
			{
			if ($counter == $page)
			$setPaginate.= "<li><a class='current_page'>$counter</a></li>";
			else
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata($counter)'>$counter</a></li>";
			}
			}
			}
		
			if ($page < $counter - 1){
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata($next)'>Next</a></li>";
			$setPaginate.= "<li><a href='javascript:void(0);' OnClick='getdata($setLastpage)'>Last</a></li>";
			}else{
			$setPaginate.= "<li><a class='current_page'>Next</a></li>";
			$setPaginate.= "<li><a class='current_page'>Last</a></li>";
			}
		
			$setPaginate.= "</ul>\n";
		}
		return $setPaginate;
		}
	       if(isset($_GET['page'])){
            $page  = (int)$_GET['page'];
			
            }else{
            $page = 1;
            }
         $setLimit = $val;
         $pageLimit  = ($page * $setLimit) - $setLimit;
			   $query = $conn->prepare("SELECT om.id AS message_id,om.short_code_id,om.mobile_no, om.message,om.correlator,om.time_sent,om.date_sent,om.delivery_status,us.fname  
		   								           FROM outgoing_messages AS om LEFT JOIN users AS us ON us.id = om.user_id ".$where." ORDER BY om.id DESC LIMIT ".$pageLimit.",".$setLimit."");
			$query->execute();
			$rows = $query->fetchAll(PDO::FETCH_ASSOC);	 
			$displayed_records=0;
      $count = 0;
	      if($num_rows>=1){	
       
         ?>
   
   <script>
$(function() {
	   $("#download").click(function(){
		 $("#table_data").table2excel({
      filename: "Sent Messages_<?php echo date('Ymdhms'); ?>",
			name: "Sent Messages",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
      exclude: ".text-right",
			exclude_inputs: true
		});  
  });
	});
</script>
<hr/>      
         <div class="table-responsive ib-tb">
                                    <table id="table_data" class="table table-hover table-mailbox">
                                     <thead>
                                     <tr>
                                       <th>Mobile No</th>
                                       <th>Message</th>
                                       <th>Sender</th>
                                       <th>Status </th>
                                       <th>Date/Time</th>
                                       <th>Action</th>
                                     </tr>                                    
                                    </thead>
                                    <tbody>
         <?php			
       		foreach($rows as $row){
         //Get the delivery status
        $get_status = $conn->prepare("SELECT * FROM delivery_status WHERE message_id='".$row['message_id']."' LIMIT 1");
        $get_status->execute();
        $get_status_row = $get_status->fetch(PDO::FETCH_ASSOC);
        $message_status = $get_status_row['delivery_status'];
        $count++;
        ?>
                                         
                                            <tr>
                                                <td class=""><span class="label label-info"><?php echo "+".$row['mobile_no']; ?></span></td>
                                                <td><?php  echo TruncateText($row['message'],10," "); ?></td>
                                                <td><span class="label label-warning"><?php  echo titleCase($row['fname']); ?></span></td>
                                                <td><span class="label label-info"><?php echo $message_status; ?></span></td>
                                                <td  style="font-size:12px;"><?php echo $row['date_sent']." ".$row['time_sent']; ?></td>
                                                <td class="text-right"><a href="#" data-toggle="modal" data-target="#PrimaryModalhdbgcl<?php  echo $row['message_id'];?>"  id="view_message_<?php  echo $row['message_id'];?>" style="color:#fff;font-size:12px;" class="btn btn-success widget-btn-2 btn-xs btn-custon-rounded-three"><i class="fa fa-envelope edu-informatio" aria-hidden="true"></i> View</a> <?php if($message_status==''){ ?> <a href="#status_reponse_<?php  echo $row['message_id'];?>"  id="get_status_<?php  echo $row['message_id'];?>" style="color:#fff;font-size:12px;" class="btn btn-warning widget-btn-2 btn-xs btn-custon-rounded-three"><i class="fa fa-info " aria-hidden="true"></i> Get Status</a> <div id="loader_status_<?php  echo $row['message_id'];?>"><img src="../img/loader.gif" width="30" height="30" /><font size="-1">Requesting...</font></div> <?php } ?></td>
                                                <?php if($message_status==''){ ?>
                                                <div id="status_reponse_<?php  echo $row['message_id'];?>"></div>
                                                <script>
                                                $(document).ready(function(){
                                                $("#loader_status_<?php  echo $row['message_id'];?>").hide();
                                                $("#get_status_<?php  echo $row['message_id'];?>").click(function(){
                                                $("#loader_status_<?php  echo $row['message_id'];?>").hide();
                                                message_id = <?php echo $row['message_id']; ?>;
                                                correlator = <?php echo $row['correlator']; ?>;
                                                short_code = <?php echo $row['short_code_id']; ?>;
                                                var myData = 'message_id=' + message_id + '&correlator=' + correlator + '&short_code=' + short_code ;
                                          	    jQuery.ajax({
                                                 type: "POST", // Post / Get method
                                                 url: "../process/getSmsDeliveryStatus.php", //Where form data is sent on submission
                                                 dataType:"text", // Data type, HTML, json etc.
                                                 data:myData, //Form variables
                                                 success:function(response){
                                                 $("#status_response_<?php  echo $row['message_id'];?>").html(response);
                                             		 $("#loader_status_<?php  echo $row['message_id'];?>").hide();    
                                                }
                                                });
                                                
                                                });
                                                });
                                                </script>
                                                <?php } ?>
                                            </tr>
                        <div id="PrimaryModalhdbgcl<?php  echo $row['message_id'];?>" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
				            <div class="modal-dialog">
				                <div class="modal-content">
				                    <div class="modal-header header-color-modal bg-color-1">
				                        <h4 class="modal-title">Text Message</h4>
				                        <div class="modal-close-area modal-close-df">
				                            <a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
				                        </div>
				                    </div>
				                    <div class="modal-body">
				                        <i class="educate-icon educate-checked modal-check-pro"></i>
				                        <h2><?php  echo titleCase($row['fname']); ?></h2>
				                        <p><?php  echo $row['message']; ?></p>
				                    </div>
				                    <div style="margin:10px;" class="alert alert-info" role="alert">
                                <strong>Mobile No: </strong> <?php echo $row['mobile_no']; ?><br/>
                                <strong>Status: </strong> <?php echo $message_status; ?><br/>
                                <strong>Date & Time: </strong> <?php echo $row['date_sent']." ".$row['time_sent']; ?><br/>
                                
                               </div>
				                   
				                </div>
				            </div>
				        </div>
                                            
                 <?php
            				}
			              $displayed_records = (($page-1) * $val ) + $count;
                  ?>
                                        </tbody>
                                    </table>
            <ul class="pagination pagination-sm m-t-0 m-b-0 pull-right" id="paginated_data">
            <?php  	echo displayPaginationBelow($setLimit,$page);   ?>
          </ul>                        
        </div>      
<?php  
	    }else{
		echo '<div class="alert alert-close alert-danger"><div class="alert-content"></div><font color="red"><i class="fa fa-check-circle"></i></font> There are currently no records!</div>';
	   }
  ?>
 <br/><br/><div id="rec_found" class="alert alert-success"><font color="green"><i class="glyph-icon icon-check-circle"></i></font>&nbsp;<?php    echo "Displaying <strong>".$count."</strong> records out of total <strong>".$num_rows."</strong> found ";?></div>
<?php
if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
$filter = $_GET['filter']; 
?>
<script>
function getdata(pageno,filter){
//empty response
$("#loader_message").show();
filter = <?php  echo $filter; ?>;
short_code = <?php echo $short_code; ?>;
	// source of data	
	var myData = 'page=' + pageno + '&filter=' + filter + '&short_code=' + short_code ;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/short_codes/loads/outgoing_messages.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#outgoing_messages_content").html(response);
				        $("#loader_message").hide();     
                }
            });
}

</script>

<?php
}else{
?>
<script>
function getdata(pageno){
//empty response
$("#loader_message").show();
	// source of data	
	var myData = 'page=' + pageno + '&short_code=' + short_code;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/short_codes/loads/outgoing_messages.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#outgoing_messages_content").html(response);
				        $("#loader_message").hide();     
                }
            });
}

</script>
<?php
}
?>
 <script>
  