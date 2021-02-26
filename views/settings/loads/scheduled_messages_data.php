<?php
//Make a connection
include("../../../connect/connect.php");


$schedule_id = $_GET['schedule_id'];
$schedule_name = $_GET['schedule_name'];
$get_schedules = $conn->prepare("SELECT * FROM scheduled_messages WHERE message_schedule_id='".$schedule_id."' ORDER BY id ASC");
$get_schedules->execute();
$get_schedule_rows = $get_schedules->fetchAll(PDO::FETCH_ASSOC);
$get_schedule_row_1 = $get_schedules->fetch(PDO::FETCH_ASSOC);
				  
				   //echo $get_group_row['contact_name'].'<br/>';

				   ?>
				     <div class="white-box" id="scroll_content">
                            <h3 class="box-title"><?php echo $schedule_name; ?></h3><hr/>
                            <ul class="basic-list" id="group_list">
                                <?php
								 foreach($get_schedule_rows as $get_schedule_row){
								 $message_schedule_id = $get_schedule_row['id'];
								?>
                                <li> <i class="fa fa-info-circle edu-inform" aria-hidden="true"></i> <?php echo $get_schedule_row['schedule_date'].'--'.$get_schedule_row['schedule_time'].'--'.$get_schedule_row['status']; ?> <span id="remove_span_<?php  echo $message_schedule_id; ?>" class="pull-right label-success label-7 label"><a  id="remove_<?php  echo $message_schedule_id; ?>" href="#"><font color="white">Remove</font></a></span><span class="response_<?php  echo $message_schedule_id; ?>"></span></li>
								  <script>
								$(document).ready(function(){
								$("#wait_<?php echo $message_schedule_id; ?>").hide();
								$("#remove_<?php echo $message_schedule_id; ?>").click(function(e){
								e.preventDefault();
						
								 message_schedule=<?php echo $message_schedule_id; ?>;								 
								 var myData = 'message_schedule=' + message_schedule;                			
									jQuery.ajax({
												type: "POST", // Post / Get method
												url: "../process/delete_message_schedule.php", //Where form data is sent on submission
												dataType:"text", // Data type, HTML, json etc.
												data:myData, //Form variables
												success:function(response){
												$(".response_<?php echo $message_schedule_id; ?>").html(response);												 
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



