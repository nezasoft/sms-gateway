<?php
//Make a connection
include("../../../connect/connect.php");

$group_id = $_GET['group'];
$group_name = $_GET['group_name'];
$get_groups = $conn->prepare("SELECT gc.id AS group_contact_id, g.group_name, c.contact_name,c.mobile_no  FROM group_contacts AS gc LEFT JOIN contacts AS c ON c.id = gc.contact_id LEFT JOIN groups AS g ON g.id = gc.group_id WHERE gc.group_id='".$group_id."' ORDER BY c.contact_name ASC");
$get_groups->execute();
$get_group_rows = $get_groups->fetchAll(PDO::FETCH_ASSOC);
$get_group_row_1 = $get_groups->fetch(PDO::FETCH_ASSOC);
				  
				   //echo $get_group_row['contact_name'].'<br/>';

				   ?>
				     <div class="white-box" id="scroll_content">
                            <h3 class="box-title"><?php echo $group_name; ?></h3><hr/>
                            <ul class="basic-list" id="group_list">
                                <?php
								 foreach($get_group_rows as $get_group_row){
								 $group_contact_id = $get_group_row['group_contact_id'];
								?>
                                <li> <i class="fa fa-info-circle edu-inform" aria-hidden="true"></i> <?php echo $get_group_row['contact_name'].'--'.$get_group_row['mobile_no']; ?> <span id="remove_span_<?php  echo $group_contact_id; ?>" class="pull-right label-success label-7 label"><a  id="remove_<?php  echo $group_contact_id; ?>" href="#"><font color="white">Remove</font></a></span><span class="response_<?php  echo $group_contact_id; ?>"></span></li>
								  <script>
								$(document).ready(function(){
								$("#wait_<?php echo $group_contact_id; ?>").hide();
								$("#remove_<?php echo $group_contact_id; ?>").click(function(e){
								e.preventDefault();
								//$("#remove_span_<?php echo $group_contact_id; ?>").hide();
								 group_contact=<?php echo $group_contact_id; ?>;								 
								 var myData = 'group_contact=' + group_contact;                			
									jQuery.ajax({
												type: "POST", // Post / Get method
												url: "../process/delete_group_contact.php", //Where form data is sent on submission
												dataType:"text", // Data type, HTML, json etc.
												data:myData, //Form variables
												success:function(response){
												$(".response_<?php echo $group_contact_id; ?>").html(response);												 
												}
									});	
                                  $("#group_list li").click(function(e) {
									$(this).remove();
                                   }); 									
								});
								});				
								</script>
								<?php
								}
								?>
                            </ul>
                        </div>


