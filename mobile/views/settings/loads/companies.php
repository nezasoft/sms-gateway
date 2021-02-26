<?php


//Make a connection
include("../../../connect/connect.php");
?>
<script>
if("undefined"==typeof jQuery)throw new Error("Tabledit requires jQuery library.");!function(t){"use strict";t.fn.Tabledit=function(e){if(!this.is("table"))throw new Error("Tabledit only works when applied to a table.");var n=this,i={url:window.location.href,inputClass:"form-control input-sm",toolbarClass:"btn-toolbar",groupClass:"btn-group btn-group-sm",dangerClass:"danger",warningClass:"warning",mutedClass:"text-muted",eventType:"click",rowIdentifier:"id",hideIdentifier:!1,autoFocus:!0,editButton:!0,deleteButton:!0,saveButton:!0,restoreButton:!0,buttons:{edit:{class:"btn btn-sm btn-default",html:'<span class="glyphicon glyphicon-pencil"></span>',action:"edit"},delete:{class:"btn btn-sm btn-default",html:'<span class="glyphicon glyphicon-trash"></span>',action:"delete"},save:{class:"btn btn-sm btn-success",html:"Save"},restore:{class:"btn btn-sm btn-warning",html:"Restore",action:"restore"},confirm:{class:"btn btn-sm btn-danger",html:"Confirm"}},onDraw:function(){},onSuccess:function(){},onFail:function(){},onAlways:function(){},onAjax:function(){}},a=t.extend(!0,i,e),d="undefined",s="undefined",o={identifier:function(){a.hideIdentifier&&n.find("th:nth-child("+parseInt(a.columns.identifier[0])+"1), tbody td:nth-child("+parseInt(a.columns.identifier[0])+"1)").hide(),n.find("tbody td:nth-child("+(parseInt(a.columns.identifier[0])+1)+")").each(function(){var e='<span class="tabledit-span tabledit-identifier">'+t(this).text()+"</span>",n='<input class="tabledit-input tabledit-identifier" type="hidden" name="'+a.columns.identifier[1]+'" value="'+t(this).text()+'" disabled>';t(this).html(e+n),t(this).parent("tr").attr(a.rowIdentifier,t(this).text())})},editable:function(){for(var e=0;e<a.columns.editable.length;e++){n.find("tbody td:nth-child("+(parseInt(a.columns.editable[e][0])+1)+")").each(function(){var n=t(this).text();a.editButton||t(this).css("cursor","pointer");var i='<span class="tabledit-span">'+n+"</span>";if(void 0!==a.columns.editable[e][2]){var d='<select class="tabledit-input '+a.inputClass+'" name="'+a.columns.editable[e][1]+'" style="display: none;" disabled>';t.each(jQuery.parseJSON(a.columns.editable[e][2]),function(t,e){d+=n===e?'<option value="'+t+'" selected>'+e+"</option>":'<option value="'+t+'">'+e+"</option>"}),d+="</select>"}else d='<input class="tabledit-input '+a.inputClass+'" type="text" name="'+a.columns.editable[e][1]+'" value="'+t(this).text()+'" style="display: none;" disabled>';t(this).html(i+d),t(this).addClass("tabledit-view-mode")})}},toolbar:function(){if(a.editButton||a.deleteButton){var t="",e="",i="",d="",s="";0===n.find("th.tabledit-toolbar-column").length&&n.find("tr:first").append('<th class="tabledit-toolbar-column"></th>'),a.editButton&&(t='<button type="button" class="tabledit-edit-button '+a.buttons.edit.class+'" style="float: none;">'+a.buttons.edit.html+"</button>"),a.deleteButton&&(e='<button type="button" class="tabledit-delete-button '+a.buttons.delete.class+'" style="float: none;">'+a.buttons.delete.html+"</button>",s='<button type="button" class="tabledit-confirm-button '+a.buttons.confirm.class+'" style="display: none; float: none;">'+a.buttons.confirm.html+"</button>"),a.editButton&&a.saveButton&&(i='<button type="button" class="tabledit-save-button '+a.buttons.save.class+'" style="display: none; float: none;">'+a.buttons.save.html+"</button>"),a.deleteButton&&a.restoreButton&&(d='<button type="button" class="tabledit-restore-button '+a.buttons.restore.class+'" style="display: none; float: none;">'+a.buttons.restore.html+"</button>");var o='<div class="tabledit-toolbar '+a.toolbarClass+'" style="text-align: left;">\n                                           <div class="'+a.groupClass+'" style="float: none;">'+t+e+"</div>\n                                           "+i+"\n                                           "+s+"\n                                           "+d+"\n                                       </div></div>";n.find("tr:gt(0)").append('<td style="white-space: nowrap; width: 1%;">'+o+"</td>")}}},l=function(e){var n=t(e).parent("tr");t(e).parent("tr").find(".tabledit-input.tabledit-identifier").prop("disabled",!0),t(e).find(".tabledit-input").blur().hide().prop("disabled",!0),t(e).find(".tabledit-span").show(),t(e).addClass("tabledit-view-mode").removeClass("tabledit-edit-mode"),a.editButton&&(n.find("button.tabledit-save-button").hide(),n.find("button.tabledit-edit-button").removeClass("active").blur())},r=function(e){c.reset(e);var n=t(e).parent("tr");n.find(".tabledit-input.tabledit-identifier").prop("disabled",!1),t(e).find(".tabledit-span").hide();var i=t(e).find(".tabledit-input");i.prop("disabled",!1).show(),a.autoFocus&&i.focus(),t(e).addClass("tabledit-edit-mode").removeClass("tabledit-view-mode"),a.editButton&&(n.find("button.tabledit-edit-button").addClass("active"),n.find("button.tabledit-save-button").show())},u=function(e){t(e).each(function(){var e=t(this).find(".tabledit-input"),n=t(this).find(".tabledit-span").text();e.is("select")?e.find("option").filter(function(){return t.trim(t(this).text())===n}).attr("selected",!0):e.val(n),l(this)})},b=function(e){!1!==f(a.buttons.edit.action)&&(t(e).each(function(){var e=t(this).find(".tabledit-input");e.is("select")?t(this).find(".tabledit-span").text(e.find("option:selected").text()):t(this).find(".tabledit-span").text(e.val()),l(this)}),d=t(e).parent("tr"))},c={reset:function(t){n.find(".tabledit-confirm-button").hide(),n.find(".tabledit-delete-button").removeClass("active").blur()},submit:function(e){c.reset(e),t(e).parent("tr").find("input.tabledit-identifier").attr("disabled",!1);var n=f(a.buttons.delete.action);t(e).parents("tr").find("input.tabledit-identifier").attr("disabled",!0),!1!==n&&(t(e).parent("tr").addClass("tabledit-deleted-row"),t(e).parent("tr").addClass(a.mutedClass).find(".tabledit-toolbar button:not(.tabledit-restore-button)").attr("disabled",!0),t(e).find(".tabledit-restore-button").hide(),s=t(e).parent("tr"))},confirm:function(e){n.find("td.tabledit-edit-mode").each(function(){u(this)}),t(e).find(".tabledit-delete-button").addClass("active"),t(e).find(".tabledit-confirm-button").show()},restore:function(e){t(e).parent("tr").find("input.tabledit-identifier").attr("disabled",!1);var n=f(a.buttons.restore.action);t(e).parents("tr").find("input.tabledit-identifier").attr("disabled",!0),!1!==n&&(t(e).parent("tr").removeClass("tabledit-deleted-row"),t(e).parent("tr").removeClass(a.mutedClass).find(".tabledit-toolbar button").attr("disabled",!1),t(e).find(".tabledit-restore-button").hide(),t(e).parent("tr"))}};function f(e){var i=n.find(".tabledit-input").serialize()+"&action="+e;if(!1===a.onAjax(e,i))return!1;var o=t.post(a.url,i,function(t,i,s){e===a.buttons.edit.action&&(d.removeClass(a.dangerClass).addClass(a.warningClass),setTimeout(function(){n.find("tr."+a.warningClass).removeClass(a.warningClass)},1400)),a.onSuccess(t,i,s)},"json");return o.fail(function(t,n,i){e===a.buttons.delete.action?(s.removeClass(a.mutedClass).addClass(a.dangerClass),s.find(".tabledit-toolbar button").attr("disabled",!1),s.find(".tabledit-toolbar .tabledit-restore-button").hide()):e===a.buttons.edit.action&&d.addClass(a.dangerClass),a.onFail(t,n,i)}),o.always(function(){a.onAlways()}),o}return o.identifier(),o.editable(),o.toolbar(),a.onDraw(),a.deleteButton&&(n.on("click","button.tabledit-delete-button",function(e){if(!0!==e.handled){e.preventDefault();var n=t(this).hasClass("active"),i=t(this).parents("td");c.reset(i),n||c.confirm(i),e.handled=!0}}),n.on("click","button.tabledit-confirm-button",function(e){if(!0!==e.handled){e.preventDefault();var n=t(this).parents("td");c.submit(n),e.handled=!0}})),a.restoreButton&&n.on("click","button.tabledit-restore-button",function(e){!0!==e.handled&&(e.preventDefault(),c.restore(t(this).parents("td")),e.handled=!0)}),a.editButton?(n.on("click","button.tabledit-edit-button",function(e){if(!0!==e.handled){e.preventDefault();var i=t(this),a=i.hasClass("active");u(n.find("td.tabledit-edit-mode")),a||t(i.parents("tr").find("td.tabledit-view-mode").get().reverse()).each(function(){r(this)}),e.handled=!0}}),n.on("click","button.tabledit-save-button",function(e){!0!==e.handled&&(e.preventDefault(),b(t(this).parents("tr").find("td.tabledit-edit-mode")),e.handled=!0)})):(n.on(a.eventType,"tr:not(.tabledit-deleted-row) td.tabledit-view-mode",function(t){!0!==t.handled&&(t.preventDefault(),u(n.find("td.tabledit-edit-mode")),r(this),t.handled=!0)}),n.on("change","select.tabledit-input:visible",function(){!0!==event.handled&&(b(t(this).parent("td")),event.handled=!0)}),t(document).on("click",function(t){var e=n.find(".tabledit-edit-mode");e.is(t.target)||0!==e.has(t.target).length||u(n.find(".tabledit-input:visible").parent("td"))})),t(document).on("keyup",function(t){var e=n.find(".tabledit-input:visible"),i=n.find(".tabledit-confirm-button");if(e.length>0)var d=e.parents("td");else{if(!(i.length>0))return;d=i.parents("td")}switch(t.keyCode){case 9:a.editButton||(b(d),r(d.closest("td").next()));break;case 13:b(d);break;case 27:u(d),c.reset(d)}}),this}}(jQuery);
</script>
<?php

           //check if user want all records or has wants a filtered result set
           if(isset($_GET['filter']) && sanitize_string($_GET['filter'])){
           $val = sanitize_string($_GET['filter']);
           $filter = 'LIMIT '.$val;
           $url = '?settings&action=companies&filter='.$val.'&';
           }else{
           $val = 50;
           $filter = '';
           $url = '?settings&action=companies&';
           }
		   $query = $conn->prepare("SELECT c.id AS company_id, company_name, physical_address,status,email,mobile_no,category_name FROM company AS c LEFT JOIN client_category AS cc ON cc.id = c.category_id ORDER BY company_name ASC");
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
			$query = $conn->prepare("SELECT c.id AS company_id, company_name, physical_address,status,email,mobile_no,category_name,DATE_FORMAT(date_created,'%d %b %Y') AS date_created FROM company AS c LEFT JOIN client_category AS cc ON cc.id = c.category_id ORDER BY c.id ASC LIMIT ".$pageLimit.",".$setLimit."");
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
            <th>Company</th>
			<th>Category</th>
            <th>Email</th>
			<th>Mobile No</th>
			<th>Date Created</th> 
			<th></th>					
            </tr>
            </thead>         
            <tbody>
            <?php			
            foreach($rows as $row){
			$count++;
            ?>
            <tr>  
                <td><?php echo titleCase($row['company_id']); ?></td>
				<td><?php echo titleCase($row['company_name']); ?></td>
				<td><?php echo titleCase($row['category_name']); ?></td>
				<td><?php echo $row['email']; ?></td>
				<td><?php echo $row['mobile_no']; ?></td>
				<td><?php echo $row['date_created']; ?></td>
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
$("#loader_company").show();
filter = <?php  echo $filter; ?>;
	// source of data	
	var myData = 'page=' + pageno + '&filter=' + filter;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/settings/loads/companies.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#company_data").html(response);
				$("#loader_company").hide();     
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
$("#loader_company").show();
	// source of data	
	var myData = 'page=' + pageno;
	 jQuery.ajax({
                type: "GET", // Post / Get method
                url: "../views/settings/loads/companies.php", //Where form data is sent on submission
                dataType:"text", // Data type, HTML, json etc.
                data:myData, //Form variables
                success:function(response){
                $("#company_data").html(response);
				$("#loader_company").hide();     
                }
            });
}

</script>
<?php
}
?>
 <script>
 
 <?php
//Get Category
$query = $conn->prepare("SELECT * FROM client_category ORDER BY category_name ASC");
$query->execute();
$categories = $query->fetchAll(PDO::FETCH_ASSOC);
$category= "'{";
foreach($categories as $category_row){
 $category .= '"'.$category_row['id'].'":  "'.$category_row['category_name'].'",';
}
$category .='"'.$category_row['id'].'": "'.$category_row['category_name'].'"';                           
$category .="}'";
 ?>
 
$('#datatable-fixedcolumns').Tabledit({
    //Delete PO Item
    url: '../process/edit_company.php', editButton: true, removeButton: true, columns: {
        identifier: [0, 'ItemID'], editable: [[1, 'CompanyName'],[2, 'Category', <?php echo $category; ?>],[3, 'Email'],[4, 'MobileNo']]
    }
    , buttons: {
        edit: {
            class: 'btn btn-sm btn-default', html: '<span class="fa fa-pencil-square-o"></span>', action: 'edit'
        }
        , delete: {
            class: 'btn btn-sm btn-default', html: '<span class="fa  fa-trash"></span>', action: 'delete'
        }
        , save: {
            class: 'btn btn-sm btn-success', html: 'Save'
        }
        , 
        confirm: {
            class: 'btn btn-sm btn-danger', html: 'Confirm'
        }
    }
    , onDraw: function() {
        return;
    }
    , onSuccess: function() {
        return;
    }
    , onFail: function() {
        return;
    }
    , onAlways: function() {
        return;
    }
    , onAjax: function() {
        return;
    }
}
);
</script>     