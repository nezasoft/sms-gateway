<?php
//Make a connection
include("../../../connect/connect.php");

if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ 
$where = "WHERE sub.company_id LIKE '%%'";
}else{
$where = "WHERE sub.company_id ='".$_SESSION['FON_G_COMPANY_ID']."'";
}
	
	

           //check if user want all records or has wants a filtered result set
           if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
           $val = sanitize_string($_GET['filter']);
           $filter = 'LIMIT '.$val;
           $url = '?settings&action=users&filter='.$val.'&';
           }else{
           $val = 50;
           $filter = '';
           $url = '?settings&action=users&';
           }
		   $query = $conn->prepare("SELECT sub.id AS subscriber_id FROM subscribers AS sub 
       LEFT JOIN company_short_codes AS csc ON csc.id = sub.company_short_code_id 
       LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id ".$where." ORDER BY sub.id DESC");
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
			$query = $conn->prepare("SELECT sub.id AS subscriber_id, sub.mobile_no,sub.date_created,sub.time_created,sub.active,sc.short_code,sc.short_code_description FROM subscribers AS sub 
                                LEFT JOIN company_short_codes AS csc ON csc.id = sub.company_short_code_id 
                                LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id ".$where." 
                                ORDER BY sub.id DESC LIMIT ".$pageLimit.",".$setLimit."");
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
         <th>Subscriber No</th>
   	     <th>Short Code</th>
         <th>Description </th>
			   <th>Date </th>
			   <th>Time</th> 
         <th>Status</th>
			   <th></th>					
            </tr>
            </thead>         
            <tbody>
            <?php			
            foreach($rows as $row){
			      $count++;
            ?>
            <tr>  
            <td><?php echo titleCase($row['subscriber_id']); ?></td>
            <td><?php echo $row['mobile_no']; ?></td>
			    	<td><?php echo $row['short_code']; ?></td>
				    <td><?php echo titleCase($row['short_code_description']); ?></td>
				    <td><?php echo $row['date_created']; ?></td>
				   <td><?php echo $row['time_created']; ?></td>
				   <td><?php echo $row['active']; ?></td>
           <td></td>
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
		echo '<div class="alert alert-close alert-danger"><div class="alert-content"></div><font color="red"><i class="fa fa-check-circle"></i></font> There are currently no subscribers!</div>';

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
	var myData = 'page=' + pageno + '&filter=' + filter;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/settings/loads/short_code_subscriptions.php", //Where form data is sent on submission
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
	var myData = 'page=' + pageno;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/settings/loads/short_code_subscriptions.php", //Where form data is sent on submission
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
 