<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Comms | Messages &mdash; ADS Mt. Kenya</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <?php
include_once("partials/head.php");
?>
    
<?php 
if(!isset($_SESSION['is_logged'])){
  echo "<div style='text-align: center;' class='alert alert-warning'>
  <h3>You must <a href='signin.php'>sign in</a> to continue!</h3></div>";
}
else{
?><?php 
include("scripts/dbconn.php"); //DB
$groups = $members =array();
try { 
$sql='';
if($_SESSION['position'] == 1){$sql = "SELECT * FROM groups";}//admin
else{ //JOIN members on members.id = member_group.fk_member_id
$sql = "SELECT * FROM member_group mg JOIN groups g on g.id = mg.fk_group_id and mg.fk_member_id=".$_SESSION['id']." AND mg.leader != 0 ";
}//$sql = "SELECT * FROM groups";  
  $stmt = $conn->prepare($sql);
  if($stmt->execute())
{
 while($row = $stmt -> fetch())
 {
   $groups[] = $row;
 }
}

$sql_mem = "SELECT * FROM members";  
$stmt_mem = $conn->prepare($sql_mem);
if($stmt_mem->execute())
{
while($row_mem = $stmt_mem -> fetch())
{
 $members[] = $row_mem;
}
}


}
catch(PDOException $e)
  {
  echo "<p class='alert alert-warning' style='text-align: center;'> Error: Operation failed quietly!". /* $e->getMessage().*/" <br>Please try again or contact the admin...</p>";
 }
$conn = null;
?>

    <div class="site-blocks-cover inner-page overlay" style="background-image: url(images/bg_messages.jpg);" data-aos="fade" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7 text-center">
            <h1 class="mb-5">Send <strong>Message</strong></h1>
          </div>
        </div>
      </div>
    </div>  

    <div class="site-section bg-light">
      <div class="container">
        <div class="row">
       
          <div class="col-md-12 col-lg-8 mb-5">
          
            
          <?php if(count($groups) == 0){
            echo '<p class="alert alert-warning"> You cannot send messages! 
            Either you do not belong to any group or you are not a leader in any of your groups!</p>';
          }
          else{?>  
            <div class="row p-5">
                <div class="col-md-12 mb-3 mb-md-0">
                  <button class="btn" id="individuals-existing-btn">Send to existing individuals</button> |
                  <button class="btn" id="individuals-new-btn">Send to new individuals</button>
                  | <button class="btn btn-info" id="groups-btn" style="display: none;">Send to all</button>
                </div>
              </div>        
            <form action="#" class="p-5 bg-white" id="msgsendform">
              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="msgresponse">                  
                </div>
                <div class="col-md-12 mb-3 mb-md-0 bg-warning" id="kill_switch_div"> 
                <button class="btn btn-xs btn-danger" id="kill_switch">Cancel message</button>                  
                </div>
              </div>
            <div class="row form-group" id="all-groups">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="group">Select Group (Group Name | Category) - You can select multiple groups</label>
                  <select id="group" name="group"  class="form-control" multiple required>
                  <?php if($_SESSION['position'] == 1){ echo '<option value="0" selected>All Members</option>'; } //only admin can send to all church members ?>
                  <?php foreach($groups as $grp){ ?>
                  <option value="<?php echo $grp['id']; ?>"><?php echo $grp['group_name'].' | '.$grp['group_category']; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>
              
              
            <div class="row form-group" id="existing-individuals" style="display: none;">
                <div class="col-md-12 mb-3 mb-md-0">
                  <!--<label class="font-weight-bold" for="group">Type phone numbers separated by commas (,)</label>-->
                  <table id="groupMemsMsg" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <p class="alert alert-info">Select recipients.</p>
            <tr>
                <th>Select</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
       <?php  foreach($members as $mem){ ?>
            <tr>
                <td><!-- <input type="hidden" name="member[]" class="mems" value="0">-->
                <input type="checkbox" name="member[]" class="mems" value="<?php  echo $mem['id']; ?>"
                data-fname="<?php  echo $mem['fname']; ?>" data-lname="<?php  echo $mem['lname']; ?>" data-phone="<?php  echo $mem['phone']; ?>"></td>
                <td><?php echo $mem['fname']; ?></td>
                <td><?php echo $mem['lname']; ?></td>
                <td><?php echo $mem['phone']; ?></td> 
                <!--<td> <input type="radio" name="leader" class="leader" value="<?php  //echo $mem['id']; ?>"> </td>-->
            </tr>
       <?php } ?>
            </tbody>
            </table>
            <!-- display selected members here-->
            <p id="selected-mems" style="display: none;">
            <strong>Selected Members:</strong> <span id="selected-count"></span><br>
            </p>
                </div>
              </div>
              
            <div class="row form-group" id="new-individuals" style="display: none;">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="group">Type phone numbers separated by commas (,)</label>
                  <br><span><em>Only numbers(0-9), comma(,) and plus(+) are allowed with no commas at the begging and end.</em></span>
                  <textarea name="indivi-new" id="indivi-new" cols="30" rows="3" class="form-control" XonKeyPress="return numbersCommaonly(event)" placeholder="0700000000,+2547000000,..."></textarea>
                </div>
              </div>
              

              <div class="row form-group">
                <div class="col-md-12">
                  <label class="font-weight-bold" for="message">Message</label> 
                  <textarea name="message" id="message" cols="30" rows="5" class="form-control" placeholder="Type the message here..." required></textarea>
                <span id="chars"></span>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12">
                  <input type="submit" value="Send Message" class="btn btn-primary">
                </div>
              </div>

  
            </form>
                  <?php } ?>
          </div>

          <div class="col-lg-4">
            
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">More Info</h3>
              <p>Select a group then type the message to be sent to the members of the group.</p> 
              <p>Only Admin can send messages.</p>
              <p>The admin can send messages to any group.</p> 
              <p>You can also send messages to individuals who are either in the portal or not.</p>            
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="promo py-5 bg-primary" data-aos="fade">
      <div class="container text-center">
        <h2 class="d-block mb-0 font-weight-light text-white">
        <a href="https://ads-mtkenya.or.ke/" target="_blank" class="text-white d-block">Visit The Main Website</a></h2>
      </div>
    </div>
    
    <footer class="site-footer">
      <div class="container">       

        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <p>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            Copyright &copy; <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>document.write(new Date().getFullYear());</script> All Rights Reserved | <a href="https://www.http://ads-mtkenya.or.ke/" target="_blank" >Anglican Development Services Mt. Kenya </a>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </p>
          </div>
          
        </div>
      </div>
    </footer>
  </div>

  <?php } ?>
  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/jquery.countdown.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/bootstrap-datepicker.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/myscripts.js"></script>
 <!-- Data table -->
  <script src="DataTables-1.10.18/dataTables.min.js"></script>  
  <!--<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>-->
  <script src="DataTables-1.10.18/datatables.1.10.19.jquery.dataTables.min.js"></script>

  <script src="js/main.js"></script>
  <script>
//will use this global variable
var selected_mems = [];
$(document).ready(function(){ 

    //$('#groupMemsMsg').DataTable(); 
    $('#groupMemsMsg').dataTable( {
    "lengthMenu": [ [-1, 10, 25, 50, 100, 500, 1000], ["All", 10, 25, 50, 100, 500, 1000] ]
    } );
//$('#existing-individuals').hide(); 
//$('#new-individuals').hide();
//$('#groups-btn').hide();
$('#kill_switch_div').hide();

$('#individuals-existing-btn').click(function(){
  $('#individuals-new-btn').removeClass('btn-info'); 
  $('#groups-btn').removeClass('btn-info'); 
  $(this).addClass('btn-info');
  $('#existing-individuals').show('slow'); 
  $('#groups-btn').show('slow');
  $('#selected-mems').show('slow'); $('#selected-count').html( selected_mems.length ); 
  $('#all-groups').hide('slow'); 
  $('#new-individuals').hide('slow');
});

$('#individuals-new-btn').click(function(){
  $('#individuals-existing-btn').removeClass('btn-info'); 
  $('#groups-btn').removeClass('btn-info'); 
  $(this).addClass('btn-info');
  $('#new-individuals').show('slow'); 
  $('#groups-btn').show('slow');
  $('#all-groups').hide('slow');
  $('#existing-individuals').hide('slow');
});

$('#groups-btn').click(function(){  
  $('#individuals-existing-btn').removeClass('btn-info'); 
  $('#individuals-new-btn').removeClass('btn-info');
  $(this).addClass('btn-info');
  $('#all-groups').show('slow');
  $('#existing-individuals').hide('slow'); 
  $('#new-individuals').hide('slow'); 
});

//when checkbox is selected
$('.mems').click(function(){  
  var id = $(this).attr('value');
  var fname = $(this).attr('data-fname');
  var lname = $(this).attr('data-lname');
  var phone = $(this).attr('data-phone');
  //check whether the clicking checked or unchecked the box
  //1. it checked, so display the selection and add the id to array
  if($(this).prop("checked") == true){
    var display = fname+' '+lname+' - '+phone;
    var img = 'images/remove.jpg';
    $('#selected-mems').append( $('<span class="selected-span" id="selected-span-'+id+'">-'+display+' <a href="#" class="selected-img" data-id="'+id+'" id="selected-img-'+id+'" title="Remove '+display+'" onclick="return remove(event,'+id+');"> <img src="'+img+'"></a> | </span>') );
    selected_mems.push(id);
    $('#selected-count').html( selected_mems.length );
  }else{ //if unchecking
    var i = selected_mems.indexOf(id);
    selected_mems.splice(i, 1);//remove that id
    $('#selected-count').html( selected_mems.length );
    $('#selected-span-'+id).remove();
  }
});


  var msgLength = 160;
  $('textarea').keyup(function(){
    var currLength = $(this).val().length;
    var msgs = Math.ceil(currLength/msgLength);
    $('#chars').text(currLength+' characters  | '+msgs+' message(s)');
  })


$('#msgsendform').on("submit", function(e){ //or $(�form�).submit(function(e){
e.preventDefault();

var grp_id = []; /*var members = [];*/ var message = ''; var individuals_new = ''; var urlToMsg='';
grp_id = $('#group').val(); //($("#ps-type").val() || []).join(', '); 
message = $('#message').val();
//var members = $('input[name="member"]').val();
individuals_new = $('#indivi-new').val();


//show and clear response field
$('#msgresponse').show().html("");

//if all is active
if( $('#groups-btn').hasClass('btn-info') ){
  selected_mems=individuals_new=[];
  if(grp_id.length == 0){
  $('#msgresponse').html('<p class="alert alert-warning"> Select at least one group </p>');
  return false;
  }
  
  if(grp_id.length > 1 && jQuery.inArray("0", grp_id) !== -1){
  $('#msgresponse').html('<p class="alert alert-warning">  You cannot select <strong>All Church Members</strong> and other groups at a go. Kindly select either <strong>All Church Members</strong> or groups. </p>');
  return false;
  }
} //if existing is active
if( $('#individuals-existing-btn').hasClass('btn-info') ){
  grp_id=individuals_new=[];
  if(selected_mems.length == 0){
  $('#msgresponse').html('<p class="alert alert-warning"> Select at least one recipient </p>');
  return false;
  }
}
 //if new is active
if( $('#individuals-new-btn').hasClass('btn-info') ){
  grp_id=selected_mems=[];
  if(individuals_new === ''){
  $('#msgresponse').html('<p class="alert alert-warning"> Enter at least one number </p>');
  return false;
  }
  individuals_new= individuals_new.trim();//get rid of white spaces
  var regex = /^[0-9,+]*$/;//[^,]+
  if(!regex.test(individuals_new)){
  $('#msgresponse').html('<p class="alert alert-warning"> Only numbers(0-9), comma(,) and plus(+) are allowed with no commas at the begging and end, check the numbers you\'ve entered! </p>');
  return false;
  }
}

if(message===''){
$('#msgresponse').html('<p class="alert alert-warning">  Message field cannot be empty! </p>');
return false;
}

//confirm recipient selection before proceeding
if(!confirm("Are you sure you've selected the appropriate recipient(s) and written the appropriate message?\n If yes, click OK. If unsure, click Cancel, brush through the inputs, then send."))
{
  return false;
}

//urls
urlToMsg = 'scripts/msgsend.php'
if(jQuery.inArray("0", grp_id) !== -1)
{  
  urlToMsg = 'scripts/msgsend-all.php'; //unique one for all messages
}

$('#msgresponse').html('<p class="alert alert-info"> Sending message(s). This may take a while, please be patient. <i class="fa fa-spinner fa-spin fa-20x"></i> </p>');
$('#kill_switch_div').show(); //show cancel button
$.ajax({
url: urlToMsg,//'scripts/msgsend.php'
method: 'post',
dataType    : 'json',
data: { grp_id:grp_id, msg:message, mems:selected_mems, indivs:individuals_new},
success: function(response){
if(response.status == 'success'){

//post response 
$('#msgresponse').html('<p class="alert alert-success"> '+ response.message + 
response.success_msgs+' out of '+ response.total+' message(s) sent successfully! <br>'+ response.fails+' </p>');
$('#msgsendform')[0].reset(); //reset form

$('#chars').html(""); //clear characters field
$('#kill_switch_div').hide(); //hide cancel button

//remove response after 3 seconds
/*setTimeout(function() {
    $("#msgresponse").fadeOut(1500);
  },5000);*/
}
else if(response.status == 'cancelled')
{
  $('#msgresponse').html('<p class="alert alert-danger"> Operation cancelled by user!<br> '+ response.message + 
response.success_msgs+' out of '+ response.total+' message(s) sent successfully! <br>'+ response.fails+' </p>');
}
else{
$('#msgresponse').html('<p class="alert alert-warning">'+ response.message +' <br>'+ response.fails+'</p>');
}
}
,error: function(data){
$('#msgresponse').html('<p class="alert alert-warning">  Error: Contact the admin! </p>');
console.log(data);
}
});

});


//kill switch
$('#kill_switch').click(function(e){
  e.preventDefault();
  $.ajax({
  url: 'scripts/kill_switch.php',
  method: 'get',
});
});

});

//when selected record is clicked for removal
//$('.selected-img').click(function(e){ 
  function remove(e,id){
  //alert("clicked");
  e.preventDefault(); 
  //var id = $(this).attr('data-id');
  var i = selected_mems.indexOf(id);
  selected_mems.splice(i, 1);//remove that id
  $('#selected-count').html( selected_mems.length );
  $('#selected-span-'+id).remove();
}
//});
</script> 
  </body>
</html>