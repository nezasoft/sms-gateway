<?php
//Make a connection
include("../../../connect/connect.php");


           //check if user want all records or has wants a filtered result set
           if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
           $val = sanitize_string($_GET['filter']);
           $company_id = sanitize_string($_GET['company']);
           $filter = 'LIMIT '.$val;
           $url = '?short_codes&action=view_short_codes&filter='.$val.'&client_id='.$company_id.'&';
           }else{
           $company_id = sanitize_string($_GET['company']);
           $val = 10;
           $filter = '';
           $url = '?short_codes&action=view_short_codes&client_id='.$company_id;
           }
		      $query = $conn->prepare("SELECT csc.id AS company_short_code_id FROM company_short_codes AS csc 
                                  LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id
                                  LEFT JOIN products AS p ON p.id = sc.product_id
                                  LEFT JOIN company AS c ON c.id = csc.company_id
                                  WHERE csc.company_id='".$company_id."' AND sc.product_id='5'");
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
			$query = $conn->prepare("SELECT csc.id AS company_short_code_id,csc.short_code_id,csc.response_message,csc.keyword,csc.endpoint,csc.correlator,csc.date_created,sc.short_code,sc.short_code_description,sc.service_id, p.product_name, c.company_name, us.fname, us.lname FROM company_short_codes AS csc 
                                   LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id
                                   LEFT JOIN products AS p ON p.id = sc.product_id
                                   LEFT JOIN company AS c ON c.id = csc.company_id
                                   LEFT JOIN users AS us ON us.id = csc.user_id 
                                   WHERE csc.company_id='".$company_id."' AND p.id='5'
                                   ORDER BY csc.id DESC LIMIT ".$pageLimit.",".$setLimit."");
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
       <th>Short Code</th>
			<th>Keyword</th>
			<th>Service ID</th>
			<th>Service Type</th>
			<th>Action</th>					
        </tr>
        </thead>         
         <tbody>
         <?php			
        foreach($rows as $row){
			$count++;
            ?>
            <tr>  
            <td><?php echo $row['company_short_code_id']; ?></td>
            <td><?php echo $row['short_code']; ?></td>
				<td><?php echo $row['keyword']; ?></td>
				<td><?php echo $row['service_id']; ?></td>
				<td><?php echo $row['product_name']; ?></td>
            <td>
            <a href="#" data-toggle="modal" data-target="#PrimaryModalhdbgcl_<?php  echo $row['company_short_code_id'];?>" id="show_modal_<?php  echo $row['company_short_code_id'];?>" class="btn btn-info widget-btn-2 btn-xs btn-custon-rounded-three"><i class="fa fa-info-circle edu-informatio" aria-hidden="true"></i> View</a>
            <a href="#"  id="show_inbox_<?php  echo $row['company_short_code_id'];?>" class="btn btn-success widget-btn-2 btn-xs btn-custon-rounded-three"><i class="fa fa-location-arrow edu-informatio" aria-hidden="true"></i> Subscriptions</a>
            <a href="#" id="deactivate_account_<?php  echo $row['company_short_code_id'];?>" class="btn btn-danger widget-btn-2 btn-xs btn-custon-rounded-three"><i class="fa fa-power-off" aria-hidden="true"></i> Deactivate</a><span id="loader_<?php  echo $row['company_short_code_id'];?>"><img src="../img/loader.gif" width="30" height="30" />Deactivating...</span> 
            </td>
            <div id="PrimaryModalhdbgcl_<?php  echo $row['company_short_code_id'];?>" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header header-color-modal bg-color-1">
                                        <h4 class="modal-title"><?php echo $row['company_name'].'-'.$row['short_code'].'-'.$row['keyword']; ?></h4>
                                        <div class="modal-close-area modal-close-df">
                                            <a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
                                        </div>
                                    </div>                                  
                                        <div style="margin:10px;" class="alert alert-info" role="alert">
                                        <strong>Service ID: </strong> <?php echo $row['service_id']; ?><br/>
                                        <strong>Keyword: </strong> <?php echo $row['keyword']; ?><br/>
                                        <strong>End Point: </strong> <?php echo $row['endpoint']; ?><br/>
                                        <strong>Correlator: </strong> <?php echo $row['correlator']; ?><br/>
                                        <strong>Product Name: </strong> <?php echo $row['product_name']; ?><br/>
                                        <strong>Details: </strong> <?php echo $row['short_code_description']; ?><br/>
                                        <strong>Created By: </strong> <?php echo titleCase($row['fname'].' '.$row['lname']); ?> <strong>ON</strong> <?php echo $row['date_created']; ?><br/>                                     
                                      </div>                               
                                </div>
                            </div>
                        </div>
                        <span id="response_<?php  echo $row['company_short_code_id'];?>"></span>
            <script>
            $(document).ready(function(){
            $("#loader_<?php  echo $row['company_short_code_id'];?>").hide();
            $("#show_inbox_<?php  echo $row['company_short_code_id'];?>").click(function(){
            company = <?php echo $company_id; ?>;
            short_code = <?php echo $row['short_code_id']; ?>;
            service_id = "<?php echo $row['service_id'];?>";
 
          $('#other_content').empty();
 				  $('#page_content').empty();
              $("#wait").show();
              $('#page_content').load('../views/short_codes/list_subscriptions.html?company='+company+'&short_code='+short_code+'&service_id='+service_id,function(responseTxt, statusTxt, jqXHR){
              if(statusTxt == "success"){
              $("#wait").hide();
              }
             if(statusTxt == "error"){
             alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
             }
              });
              
              
            });
            
            $("#deactivate_account_<?php  echo $row['company_short_code_id'];?>").click(function(e){
            e.preventDefault();
            $("#response_<?php  echo $row['company_short_code_id'];?>").empty();
            $("#loader_<?php  echo $row['company_short_code_id'];?>").show();
            service_id = "<?php echo $row['service_id']; ?>";
            correlator = "<?php echo $row['correlator']; ?>";
            var myData = 'service_id=' + service_id + '&correlator=' + correlator; 
            //alert(myData);
	         jQuery.ajax({
                type: "POST", // Post / Get method
                url: "../process/stopSmsNotification.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#response_<?php  echo $row['company_short_code_id'];?>").html(response);
                $("#loader_<?php  echo $row['company_short_code_id'];?>").hide();
                $("#response_<?php  echo $row['company_short_code_id'];?>").fadeOut(8000);    
                }
            });

            });
            });
            </script>
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
$("#loader_subscriptions").show();
filter = <?php  echo $filter; ?>;
	// source of data	
	company = <?php echo $company_id; ?>;
	var myData = 'page=' + pageno + '&filter=' + filter + '&company=' + company;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/short_codes/loads/subscriptions.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#subscriptions_data").html(response);
                $("#loader_subscriptions").hide();     
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
$("#loader_subscriptions").show();
	// source of data	
	company = <?php  echo $company_id;?>;
	var myData = 'page=' + pageno + '&company=' + company;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/short_codes/loads/subscriptions.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#subscriptions_data").html(response);
				        $("#loader_subscriptions").hide();     
                }
            });
}

</script>
<?php
}
?>
 <script>
  