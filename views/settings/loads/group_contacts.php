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
		   $query = $conn->prepare("SELECT gc.id AS group_id, g.group_name FROM group_contacts AS gc LEFT JOIN groups AS g ON g.id = gc.group_id WHERE g.company_id='".$_SESSION['FON_G_COMPANY_ID']."' GROUP BY gc.group_id ORDER BY g.group_name ASC");
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
			$query = $conn->prepare("SELECT group_id, g.group_name,count(*) AS contact_count  FROM group_contacts AS gc LEFT JOIN groups AS g ON g.id = gc.group_id WHERE g.company_id='".$_SESSION['FON_G_COMPANY_ID']."' GROUP BY gc.group_id ORDER BY g.group_name ASC LIMIT ".$pageLimit.",".$setLimit."");
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
            <th>Group Name</th>
			<th>No of Contacts</th>
					
            </tr>
            </thead>         
            <tbody>
            <?php			
            foreach($rows as $row){
			$count++;
			$group_id = $row['group_id'];
			$group_name = $row['group_name'];
            ?>
            <tr>  
                <td><?php echo titleCase($row['group_id']); ?></td>
				<td><a href="#"  data-toggle="modal" data-target="#PrimaryModalhdbgcl_<?php echo $group_id; ?>" id="group_link_<?php echo $group_id; ?>" ><?php echo titleCase($row['group_name']); ?></a></td>
				<td><?php echo titleCase($row['contact_count']); ?></td>
		
				<div id="PrimaryModalhdbgcl_<?php echo $group_id; ?>" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
				  <div class="modal-dialog">
				  <div class="modal-content">
				  <div class="modal-header header-color-modal bg-color-1">
                      <h4 class="modal-title">View Group Contacts</h4>                                    
                    </div><br/>
				   <div class="modal-close-area modal-close-df">
					<a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
				  </div> 
				  <span style="padding:30px;" id="wait_<?php echo $group_id; ?>"><img src="../img/loader.gif" width="40" height="40" /> Loading...</span> 
				  
				   <div id="page_content_<?php echo $group_id; ?>">
			
				   
				   </div>
                   <script>
				   $("#wait_<?php echo $group_id; ?>").show();
				   $('#page_content_<?php echo $group_id; ?>').load('../views/settings/loads/group_contacts_data.php?group=<?php echo $group_id; ?>&group_name=<?php echo $group_name;?>',function(responseTxt, statusTxt, jqXHR){
					  if(statusTxt == "success"){
						$("#wait_<?php echo $group_id; ?>").hide();
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
                url: "../views/settings/loads/group_contacts.php", //Where form data is sent on submission
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
                url: "../views/settings/loads/group_contacts.php", //Where form data is sent on submission
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
 
