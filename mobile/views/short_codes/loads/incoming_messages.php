
<?php
//Make a connection
include("../../../../connect/connect.php");

           $company_id = $_SESSION['FON_G_COMPANY_ID'];
          
           if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
           $val = sanitize_string($_GET['filter']);
           $short_code = $_GET['short_code'];
           
           $filter = 'LIMIT '.$val;
           $url = '?short_codes&action=view_messages&filter='.$val.'&client_id='.$company_id.'&';
           }else{
           $short_code = $_GET['short_code'];

           $val = 50;
           $filter = '';
           $url = '?short_codes&action=view_messages&client_id='.$company_id;
           }
            //check if user want all records or has wants a filtered result set
           if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){         
            $where = "WHERE  sc.id='".$short_code."' AND im.company_id LIKE '%%'";
           }else{
            $where = "WHERE  sc.id='".$short_code."' AND im.company_id='".$_SESSION['FON_G_COMPANY_ID']."'";
           }
		   $query = $conn->prepare("SELECT im.id AS message_id FROM incoming_messages AS im LEFT JOIN short_codes AS sc ON sc.id = im.short_code_id ".$where."");
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
			   $query = $conn->prepare("SELECT im.id AS message_id, message,msg_date,msg_time,mobile_no,msg_read,sc.short_code FROM incoming_messages AS im 
											 LEFT JOIN short_codes AS sc ON sc.id = im.short_code_id ".$where." ORDER BY im.id DESC LIMIT ".$pageLimit.",".$setLimit."");
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
      filename: "Received Messages_<?php echo date('Ymdhms'); ?>",
			name: "Received Messages",
			fileext: ".xls",
			exclude_img: true,
			exclude_links: true,
      exclude: ".text-right",
			exclude_inputs: true
		});  
  });
	});
</script> <hr/>
         <div class="table-responsive ib-tb">
                                    <table class="table table-hover table-mailbox" id="table_data">
                                    <thead>
                                     <tr>
                                       <th>Mobile No</th>
                                       <th>Message</th>
                                       <th>Short Code </th>
                                       <th>Date/Time</th>
                                       <th>Action</th>
                                     </tr>
                                    
                                    </thead>
                                        <tbody>
                                         <?php			
         											         foreach($rows as $row){
			                                 $count++;
                                         ?>                                        
                                            <tr <?php if($row['msg_read']=='N'){echo 'class="unread active"';} ?> >
                                                <td class=""><span class="label label-info"><?php echo $row['mobile_no']; ?></span></td>
                                                <td><?php  echo $row['message']; ?></td>
                                                <td><?php  echo $row['short_code']; ?></td>         
                                                <td  style="font-size:12px;"><?php echo $row['msg_date']." ".$row['msg_time']; ?></td>
                                                <td  class="text-right"><a href="#" data-toggle="modal" data-target="#PrimaryModalhdbgcl<?php  echo $row['message_id'];?>"  id="view_message_<?php  echo $row['message_id'];?>" style="color:#fff;font-size:12px;" class="btn btn-success widget-btn-2 btn-xs btn-custon-rounded-three"><i class="fa fa-envelope edu-informatio" aria-hidden="true"></i> View</a></td>
                                            </tr>
                                            
	                                            <script>
	                                            $(document).ready(function(){
	                                            $("#loader_reply_<?php  echo $row['message_id'];?>").hide();
	                                            $("#reply_box_<?php  echo $row['message_id'];?>").hide();
	                                            $(".reply_message_error_<?php  echo $row['message_id'];?>").hide();
	                                            
	                                            $("#view_message_<?php  echo $row['message_id'];?>").click(function(){
	                                             message_id = <?php echo $row['message_id'];?>;
	                                             //Send request to server
	                                             var myData = 'message_id=' + message_id;
																	 jQuery.ajax({
               												 type: "POST", // Post / Get method
               												 url: "../process/update_message_read_status.php", //Where form data is sent on submission
               												 dataType:"text", // Data type, HTML, json etc.
               												 data:myData, //Form variables
               												 success:function(response){
               												 $("#response_<?php  echo $row['message_id'];?>").html(response);
				  												       
                												 }
                 												 });
                                                       
	                                            });
	                                            //Lets invoke a reply request
                 												 $("#reply_message_<?php  echo $row['message_id'];?>").click(function(){
                 												 	//Hide alert div
                 												 	$("#alert_<?php  echo $row['message_id'];?>").hide();
                 												 	//Show reply box div
                 												 	$("#reply_box_<?php  echo $row['message_id'];?>").show();
                 												 
                 												 }); 
                 												 //Cancel the reply request
                 												  $("#cancel_reply_<?php  echo $row['message_id'];?>").click(function(){
                 												 	//Hide alert div
                 												 	$("#alert_<?php  echo $row['message_id'];?>").show();
                 												 	//Show reply box div
                 												 	$("#reply_box_<?php  echo $row['message_id'];?>").hide();
                 												 
                 												 });
                 										//Lets reply to this message
                 										$("#save_reply_<?php  echo $row['message_id'];?>").click(function(){
                 											$("#response_<?php  echo $row['message_id'];?>").empty();
	                                             message_id = <?php echo $row['message_id'];?>;
	                                             mobile_no ="<?php echo $row['mobile_no'];?>";
	                                             short_code ="<?php echo $row['short_code'];?>";
	                                             reply_message = $("#reply_message_text_<?php  echo $row['message_id'];?>") .val();
	                                            
	                                             error = false;
	                                             
	                                             if(reply_message===''){
	                                               error = true;
	                                               $("#reply_message_text_<?php  echo $row['message_id'];?>").css("border-color","red");
	                                               $(".reply_message_error_<?php  echo $row['message_id'];?>").show();
	                                             }else{
	                                               error = false;
	                                               $("#reply_message_text_<?php  echo $row['message_id'];?>").css("border-color","green");
	                                               $(".reply_message_error_<?php  echo $row['message_id'];?>").hide();
	                                             }
	                                             
	                                             if(error == false){
	                                             	$("#save_reply_<?php  echo $row['message_id'];?>").hide();
	                                             	$("#loader_reply_<?php  echo $row['message_id'];?>").show();
	                                             	//Send request to server
	                                             	var myData = 'message_id=' + message_id + '&mobile_no=' + mobile_no + '&reply_message=' + reply_message + '&short_code=' + short_code;
																	     jQuery.ajax({
               												 type: "POST", // Post / Get method
               												 url: "../process/reply_message.php", //Where form data is sent on submission
               												 dataType:"text", // Data type, HTML, json etc.
               												 data:myData, //Form variables
               												 success:function(response){
               												 $("#response_<?php  echo $row['message_id'];?>").html(response);
				  												    $("#loader_reply_<?php  echo $row['message_id'];?>").hide(); 
				  												    $("#save_reply_<?php  echo $row['message_id'];?>").show();
                												 }
                 												 });
	                                             
	                                             }
	                                             
                 										});	 
	                                            });
	                                            </script>
                        <div id="PrimaryModalhdbgcl<?php  echo $row['message_id'];?>" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
				            <div class="modal-dialog">
				            <span id="response_<?php  echo $row['message_id'];?>"></span>
				                <div class="modal-content">
				                    <div class="modal-header header-color-modal bg-color-1">
				                        <h4 class="modal-title">Text Message</h4>
				                        <div class="modal-close-area modal-close-df">
				                            <a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
				                        </div>
				                    </div>
				                    <div class="modal-body">
				                        <i class="educate-icon educate-checked modal-check-pro"></i>
				                        <h2><?php  echo $row['mobile_no']; ?></h2>
				                        <p><?php  echo $row['message']; ?></p>
				                      <a  class="btn btn-xs btn-info" href="#"  style="color:#fff;"id="reply_message_<?php  echo $row['message_id'];?>"><i class="fa fa-reply"></i> Reply</a>
				                    </div>
                               
				                    <div style="margin:10px;" id="alert_<?php  echo $row['message_id'];?>" class="alert alert-info" role="alert">
                                <strong>Mobile No: </strong> <?php echo $row['mobile_no']; ?><br/>
                                <strong>Status: </strong> <?php if($row['msg_read']=='N'){echo 'Unread';}else{ echo 'Read';} ?> <br/>
                                <strong>Date & Time: </strong> <?php echo $row['msg_date']." ".$row['msg_time']; ?><br/>
                               </div>
                               <hr/>
                               <div  style="margin:10px;" id="reply_box_<?php  echo $row['message_id'];?>">
                               <strong>To:</strong>  <?php  echo $row['mobile_no']; ?><br/><br/>
                               <strong>Message:</strong>
                               <div  class="form-group edit-ta-resize res-mg-t-15">
                                  <textarea maxlength="160"   name="reply_message" id="reply_message_text_<?php  echo $row['message_id'];?>"></textarea>
                                  <div style="color:red;" class="reply_message_error_<?php  echo $row['message_id'];?>">*Enter message</div>
                                <div style="color:red; font-weight:bolder;"  id="char_count_<?php  echo $row['message_id'];?>"></div>
                                </div>
                                <script>
                                $(document).ready(function(){
                                 var text_max = 160;
                                 $("#char_count_<?php  echo $row['message_id'];?>").html(text_max + ' characters remaining');
                                 $("#reply_message_text_<?php  echo $row['message_id'];?>").keyup(function() {
                                 var text_length = $("#reply_message_text_<?php  echo $row['message_id'];?>").val().length;
                                 var text_remaining = text_max - text_length;
                                  $("#char_count_<?php  echo $row['message_id'];?>").html(text_remaining + " characters remaining");
                                 });
                                 });
                                </script>
                                <div id="loader_reply_<?php  echo $row['message_id'];?>"><img src="../img/loader.gif" width="40" height="40" /><strong>Sending...</strong></div>           
                                <a href="#" style="color:#fff;" id="save_reply_<?php  echo $row['message_id'];?>" class="btn btn-xs btn-success"><i class="fa fa-send"></i> Send</a> 
				                    <a href="#" style="color:#fff;" id="cancel_reply_<?php  echo $row['message_id'];?>" class="btn btn-xs btn-danger"><i class="fa fa-close"></i> Cancel</a>
				                   
				                   </div>
				                   <span style="margin-left:12px; font-size:12px;" class="label label-info"><i class="fa fa-eye"></i> Seen By</span>
				                     <div style="margin:10px;"  class="alert alert-success" role="alert">
				                     <?php 
				                     //Lets get the no of people who have read this message
				                     $query_reads = $conn->prepare("SELECT m_r.id AS read_id,m_r.read_date,m_r.read_time,us.fname FROM message_reads AS m_r  
				                     LEFT JOIN users AS us ON us.id= m_r.user_id WHERE m_r.message_id='".$row['message_id']."' ORDER BY m_r.id DESC ");
				                     $query_reads->execute();
				                     $query_reads_rows = $query_reads->fetchAll(PDO::FETCH_ASSOC);
				                     foreach($query_reads_rows as $query_reads_row){
				                     ?>
                                <strong>User: </strong> <?php echo titleCase($query_reads_row['fname']).'@'.$query_reads_row['read_date'].' '.$query_reads_row['read_time']; ?><br/>
                                <?php } ?>
                                
                               </div>
                               <hr/>
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
short_code = <?php  echo $short_code; ?>;
	// source of data	
	var myData = 'page=' + pageno + '&filter=' + filter + '&short_code=' + short_code ;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "views/short_codes/loads/incoming_messages.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#incoming_messages_content").html(response);
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
                url: "views/short_codes/loads/incoming_messages.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#incoming_messages_content").html(response);
				    $("#loader_message").hide();     
                }
            });
}

</script>
<?php
}
?>
 <script>
  