



 $(document).ready(function(){
 $("#wait").fadeOut();
 
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/short_codes/list_short_codes.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 }); 

 $("#list_companies_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_companies.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 

 $("#list_contacts_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_contacts.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 $("#list_users_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_users.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 $("#list_categories_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_categories.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#list_short_codes_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_short_codes.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 $("#list_levels_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_levels.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#list_product_category_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_product_categories.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#company_short_code_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/short_codes/list_short_codes.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#new_short_code_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/short_codes/new_company_short_code.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
  $("#subscriptions_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/short_codes/subscriptions.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#list_bulk_sms_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/bulksms/bulksms_codes.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#new_sms_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/bulksms/new_message.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 $("#list_menu_my_account").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/my_account.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#list_menu_statistics").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/dashboard.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });

$("#list_system_menu").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_system_settings.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
  
  $("#list_contact_groups").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_contact_groups.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
  
 $("#list_sms_groups").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_message_groups.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });


$("#list_payments").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_payments.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });
 
 $("#list_client_package").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_client_package.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });


$("#list_schedules").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_schedules.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });



 $("#list_scheduled_messages").click(function(){
 $('#other_content').empty();
 $('#page_content').empty();
 $("#wait").show();
 $('#page_content').load('../views/settings/list_scheduled_messages.html',function(responseTxt, statusTxt, jqXHR){
  if(statusTxt == "success"){
    $("#wait").hide();
  }
  if(statusTxt == "error"){
   alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
  }
 });
 });

  
 });


