<style>#scroll_content{height: 500px;overflow: scroll;width: auto;padding:5px;background:#fff; overflow-x:auto;}</style>
<?php
//Make a connection
include("../../../connect/connect.php");


           //check if user want all records or has wants a filtered result set
           if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
           $val = sanitize_string($_GET['filter']);
           $filter = 'LIMIT '.$val;
           $url = '?settings&action=groups&filter='.$val.'&';
           }else{
           $val = 50;
           $filter = '';
           $url = '?settings&action=groups&';
           }
		   $query = $conn->prepare("SELECT ms.id AS ms_id FROM message_schedules AS ms 
								   LEFT JOIN schedules AS s ON s.id = ms.schedule_id 
								   LEFT JOIN frequency AS f ON f.id = s.frequency_id 
								   LEFT JOIN users AS us ON us.id = ms.user_id
								   WHERE ms.company_id='".$_SESSION['FON_G_COMPANY_ID']."' ORDER BY ms.id DESC ");
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
            //echo $filter;
            $setLimit = $val;
            $pageLimit  = ($page * $setLimit) - $setLimit;
			$query = $conn->prepare("SELECT ms.id AS ms_id,ms.schedule_date,ms.message,ms.schedule_id,s.schedule_name,f.frequency_name,s.start_date,s.end_date,us.fname FROM message_schedules AS ms 
								   LEFT JOIN schedules AS s ON s.id = ms.schedule_id 
								   LEFT JOIN frequency AS f ON f.id = s.frequency_id 
								   LEFT JOIN users AS us ON us.id = ms.user_id
								   WHERE ms.company_id='".$_SESSION['FON_G_COMPANY_ID']."' ORDER BY ms.id DESC LIMIT  ".$pageLimit.",".$setLimit."");
			$query->execute();
			$rows = $query->fetchAll(PDO::FETCH_ASSOC);
		 
			 $displayed_records=0;
	         $count = 0;
	        if($num_rows>=1){		
             ?>
        <table width="100%" class="table table-striped"  id="datatable-fixedcolumns" >
            <thead>
            <tr>
			<th>#</th>
            <th>Message</th>
			<th>Schedule</th>
			<th>Frequency</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>User</th>
					
            </tr>
            </thead>         
            <tbody>
            <?php			
            foreach($rows as $row){
			$count++;
			$schedule_id = $row['ms_id'];
			$schedule_name = $row['schedule_name'];
            ?>
            <tr>  
                <td><?php echo titleCase($row['ms_id']); ?></td>
				<td><?php  echo TruncateText($row['message'],25," "); ?></td>
				<td><?php echo titleCase($row['schedule_name']); ?></td>
				<td><?php echo titleCase($row['frequency_name']); ?></td>
				<td><?php echo $row['start_date']; ?></td>
				<td><?php echo $row['end_date']; ?></td>
				<td><?php echo $row['fname']; ?></td>
				<td><a href="#"  data-toggle="modal" class="btn btn-warning" data-target="#PrimaryModalhdbgcl_<?php echo $schedule_id; ?>" id="group_link_<?php echo $schedule_id; ?>" >View</a></td>
					
				<div id="PrimaryModalhdbgcl_<?php echo $schedule_id; ?>" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
				  <div class="modal-dialog">
				  <div class="modal-content">
				  <div class="modal-header header-color-modal bg-color-1">
                      <h4 class="modal-title">View Schedule Details</h4>                                    
                    </div><br/>
				   <div class="modal-close-area modal-close-df">
					<a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
				  </div> 
				  <span style="padding:30px;" id="wait_<?php echo $schedule_id; ?>"><img src="../img/loader.gif" width="40" height="40" /> Loading...</span> 
				  
				   <div id="page_content_<?php echo $schedule_id; ?>">
			
				   
				   </div>
                   <script>
                       var schedule_name = "<?php echo $schedule_name; ?>";
				   $("#wait_<?php echo $schedule_id; ?>").show();
				   $('#page_content_<?php echo $schedule_id; ?>').load('../views/settings/loads/scheduled_messages_data.php?schedule_id=<?php echo $schedule_id; ?>&schedule_name=' + schedule_name,function(responseTxt, statusTxt, jqXHR){
					  if(statusTxt == "success"){
						$("#wait_<?php echo $schedule_id; ?>").hide();
					  }
					  if(statusTxt == "error"){
					   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
					  }
					 });
				   </script>				   
				   </div>				   
				   </div>                 
                </div>        
            </tr>
            <?php
            }
			$displayed_records = (($page-1) * $val ) + $count;
            ?>
            </tbody>			
        </table>
          <ul class="pagination pagination-sm m-t-0 m-b-0 pull-right" id="paginated_data">
            <?php  	echo displayPaginationBelow($setLimit,$page);   ?>
          </ul>
	 <?php  
	    }else{
		echo '<table width="100%"  id="datatable-fixedcolumns" >
            <thead>
            <tr>					
            </tr>
            </thead>         
            <tbody>
			</tbody>
			</table>';
		echo '<div class="alert alert-close alert-danger"><div class="alert-content"></div><font color="red"><i class="fa fa-check-circle"></i></font> There are currently no records!</div>';

	   }
         ?>
 <br/><br/><br/><br/><div id="rec_found" class="alert alert-success"><font color="green"><i class="glyph-icon icon-check-circle"></i></font>&nbsp;<?php    echo "Displaying <strong>".$count."</strong> records out of total <strong>".$num_rows."</strong> found ";?></div>

<?php
if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
$filter = $_GET['filter']; 
?>
<script>
function getdata(pageno,filter){
//empty response
$("#loader_groups_1").show();
filter = <?php  echo $filter; ?>;
	// source of data	
	var myData = 'page=' + pageno + '&filter=' + filter;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/settings/loads/scheduled_messages.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#groups_data").html(response);
				$("#loader_groups_1").hide();     
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
$("#loader_groups_1").show();
	// source of data	
	var myData = 'page=' + pageno;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/settings/loads/scheduled_messages.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#groups_data").html(response);
				$("#loader_groups_1").hide();     
                }
            });
}

</script>
<?php
}
?>
 

