<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Comms | Members &mdash; ADS Mt. Kenya</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
    #edit-member{
      display: none;
    }
    </style>

    <?php
    include_once("partials/head.php");
    ?>
    
<?php 
if(!isset($_SESSION['is_logged'])){
  echo "<div style='text-align: center;' class='alert alert-warning'>
  <h3>You must <a href='signin.php'>sign in</a> to continue!</h3></div>";
}
else{
?>
    <?php 
  include("scripts/dbconn.php"); //DB
$members = array();  
$groups = $members =array();
try { 
 $sql = "SELECT m.id,m.fname,m.lname,m.phone,count(mg.fk_member_id) grps FROM members m LEFT JOIN member_group mg on m.id = mg.fk_member_id GROUP BY m.id"; 
    $stmt = $conn->prepare($sql);
    if($stmt->execute())
	{
	 while($row = $stmt -> fetch())
	 {
	   $members[] = $row;
	 }
  } 

$sql_groups = "SELECT * FROM groups";  
  $stmt_groups = $conn->prepare($sql_groups);
  if($stmt_groups->execute())
{
 while($row_groups = $stmt_groups -> fetch())
 {
   $groups[] = $row_groups;
 }
}

  }
catch(PDOException $e)
    {
    echo "<p class='alert alert-warning' style='text-align: center;'> Error: Operation failed quietly!".  $e->getMessage()." <br>Please try again or contact the admin...</p>";
   }
	
$conn = null;
 ?>

    <div class="site-blocks-cover inner-page" style="background-image: url(images/bg_members.jpg);" data-aos="fade" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-7">
            <span class="sub-text">Current Members in the Project</span>
            <h1>Our <strong>Project Members</strong></h1>
          </div>
        </div>
      </div>
    </div>  

    <div class="site-section site-block-3 bg-light">
      <div class="container">
        <div class="row align-items-stretch">
          <div class="col-md-12 text-center">
            <h2 class="display-4 text-black mb-5">Registered <strong>Members</strong></h2>
     <p class="justify-content-center">Registered in that they exist in this online database</p>
          </div>


      <?php if($_SESSION["position"] == 1){ //admin only ?>
        <!-- Form-->
        <div class="site-section bg-light" id="edit-member">
      <div class="container">

        <div class="row">
       
          <div class="col-md-12 col-lg-8 mb-5">  

            <form class="p-5 bg-white" id="memeditform">            
            <fieldset><legend>Edit Member | <a id="close" href="#"><i class="fa fa-times" data-toggle="tooltip" title="Close"></i></a></legend>

              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="memeditresponse">
                  
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="fname_edit">Name</label>
                  <input type="text" id="fname_edit" name="fname_edit"  class="input form-control" placeholder="Member's First Name" required>
                </div>
              </div>

              <!-- <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="lname_edit">Last Name</label>
                  <input type="text" id="lname_edit" name="lname_edit"  class="input form-control" placeholder="Member's Last Name" required>
                </div>
              </div> -->

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="phone_edit">Phone</label>
                  <input type="text" id="phone_edit" name="phone_edit" onKeyPress="return numbersonly(event)"  class="input form-control" placeholder="Member's Phone number 07.." required>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12" id="add_hidden">
                  <input type="submit" value="Update Member" class="btn btn-primary">
                </div>
              </div>

            </fieldset>
            </form>
          </div>

          <div class="col-lg-4">
            
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">More Info</h3>
              <p>.....................................</p>
            </div>

            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">Note:</h3>
              
              <p class="mb-0 font-weight-bold">Password</p>
              <p class="mb-4">Phone number will act </p>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /add form-->
      <?php } ?>



        <div class="col-md-12 text-center">
        <table id="members" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <p class="alert alert-info">Total Members: <span id="memcount"><?php echo count($members); ?></span> 
        <?php if($_SESSION["position"] == 1){ //admin only 
        echo '| <a href="scripts/members-export-xlsx.php">Export (.xlsx)</a> | <a href="scripts/members-export-xls.php">Export (.xls)</a>';}
        ?>
        </p>
            <tr>
                <th>#</th>
                <th>Name</th>
                <!-- <th>Last Name</th> -->
                <th>Phone Number</th>
               <th>Groups</th>
                <?php if($_SESSION["position"] == 1){ //admin only 
                echo '<th>Edit</th>';
                echo '<th>Delete</th>';}
                ?>
            </tr>
        </thead>
        <tbody>
       <?php  foreach($members as $index=>$mem){ ?>
            <tr id="row-<?php echo $mem['id']; ?>">
                <td><?php echo $index+1; ?></td>
                <td id="fname-<?php echo $mem['id']; ?>"><?php echo $mem['fname']; ?></td>
                <!-- <td id="lname-<?php echo $mem['id']; ?>"><?php echo $mem['lname']; ?></td> -->
                <td id="phone-<?php echo $mem['id']; ?>"><?php echo $mem['phone']; ?></td> 
                <td><?php echo $mem['grps'];
                if($mem['grps']>0){ echo '  <a href="member_groups.php?memid='.$mem['id'].'">
                  <i class="fa fa-eye text-secondary" data-toggle="tooltip" title="View"></i></a>';} ?></td> 
                
                <?php if($_SESSION['position'] == 1){?>
                <td><a id="btn-<?php echo $mem['id']; ?>" onclick="return edit(<?php echo $mem['id']; ?>);" 
                data-toggle="tooltip" title="Edit member"> 
                <i class="fa fa-edit text-info"></i></a></td>

                <td><a id="btn-<?php echo $mem['id']; ?>" onclick="return confirmdel(<?php echo $mem['id']; ?>);" 
                data-toggle="tooltip" title="Delete member"> 
                <i class="fa fa-trash text-danger"></i></a><?php } ?></td>
            </tr>
       <?php } ?>
            </tbody>
            </table>
       </div>
        </div>
      </div>
    </div>

    
    <div class="promo py-5 bg-primary" data-aos="fade">
      <div class="container text-center">
        <h2 class="d-block mb-0 font-weight-light text-white">
        <?php if($_SESSION["position"] == 1){ //admin only ?>
        Scroll down to add members
        <?php }else{?>         
       <!-- <a href="https://ads-mtkenya.or.ke//" target="_blank" class="text-white d-block">Visit The Main Website</a>-->
        <?php } ?>
        </h2>
      </div>
    </div>
    
    <footer class="site-footer">
      <div class="container">
        

      <?php if($_SESSION["position"] == 1){ //admin only ?>
        <!-- Form-->
        <div class="site-section bg-light">
      <div class="container">

        <div class="row">
       
          <div class="col-md-12 col-lg-8 mb-5">  

            <form class="p-5 bg-white" id="memaddform">            
            <fieldset><legend>Add Member</legend>

              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="response">
                  
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="fname">Name</label>
                  <input type="text" id="fname" name="fname"  class="input form-control" placeholder="Member's Name" required>
                </div>
              </div>

              <!-- <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="lname">Last Name</label>
                  <input type="text" id="lname" name="lname"  class="input form-control" placeholder="Member's Last Name" required>
                </div>
              </div> -->

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="phone">Phone</label>
                  <input type="text" id="phone" name="phone" onKeyPress="return numbersonly(event)"  class="input form-control" placeholder="Member's Phone number 07.." required>
                </div>
              </div>

              
              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="group">Select Group</label>
                  <select multiple id="groups" name="groups"  class="input form-control" required>
                  <!-- <option value="">Group name</option> -->
                  <?php foreach($groups as $grp){ ?>
                  <option value="<?php echo $grp['id']; ?>"><?php echo $grp['group_name']; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12">
                  <input type="submit" value="Add Member" class="btn btn-primary">
                </div>
              </div>

            </fieldset>
            </form>
          </div>

          <div class="col-lg-4">
            
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">More Info</h3>
              <p>Add all Projects names with their numbers to their respective groups . 
              Make sure sure you add them in the right group.</p>
            </div>

            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">Note:</h3>
              
              <p class="mb-0 font-weight-bold">Group</p>
              <p class="mb-4">Only admin who Can See this</p>
              <p class="bg text-info">If the intended group does not exist, create it before adding memmbers.</p>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /add form-->
      <?php } ?>

        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <p>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            Copyright &copy; <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>document.write(new Date().getFullYear());</script> All Rights Reserved | <a href="https://ads-mtkenya.or.ke//" target="_blank" >ADS Mt. Kenya</a>
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

  <script src="js/main.js"></script>
  <script src="js/myscripts.js"></script>
 <!-- Data table -->
  <script src="DataTables-1.10.18/dataTables.min.js"></script>  
  <!--<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>-->
  <script src="DataTables-1.10.18/datatables.1.10.19.jquery.dataTables.min.js"></script>
    
<script>
$(document).ready(function(){
    //$('#members').DataTable();
    $('#members').dataTable( {
    "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ]
    } );

$('.input').keydown(function(){
  $('#response').empty();
});


$('#memaddform').on("submit", function(e){ //or $(�form�).submit(function(e){
e.preventDefault();

var fname = $('#fname').val();
var lname = null;//$('#lname').val();
var phone = $('#phone').val();
var groups = [];
var groups = $('#groups').val();

//show and clear response field
$('#response').show().html("");
if(fname=='' || lname=='' || phone==''){
$('#response').html('<p class="alert alert-warning">  Fill in all fields! </p>');
return false;
}

$.ajax({
url: 'scripts/memadd.php',
method: 'post',
dataType    : 'json',
data: { fname:fname, lname:lname, phone:phone, groups},
success: function(response){
if(response['status'] == 'success'){

//post response 
$('#response').html('<p class="alert alert-success">  '+ response.message +' </p>');
$('#memaddform')[0].reset(); //rest form

//remove response after 3 seconds
setTimeout(function() {
    $("#response").fadeOut(1500);
  },3000);

 //update count
 var current_total = parseInt($('#memcount').html());
 $('#memcount').html(current_total+1);

//append the data to table
$('#members').append('<tr> <td>'+(current_total+1)+'</td> <td>'+fname+'</td>'+
 '<td>'+phone+'</td><td>1</td></tr>');

}
else{
$('#response').html('<p class="alert alert-warning">'+ response.message +'</p>');
}
}
,error: function(data){
$('#response').html('<p class="alert alert-warning">  Error: Contact the admin! </p>');
console.log(data);
}
});

});


$('#close').on('click',function(e){
  e.preventDefault();
  $("#edit-member").hide('slow');
});
});

function confirmdel(n){
  if(confirm('Are you really sure you want to delete this member?'))
  {
      $.ajax({
                url: 'scripts/deletes.php',
                type: "get",
                dataType: 'json',
                data: { src: 'mem', id: n },
                success: function (response) {
                    if(response.status == 'success'){
                    $('#row-' + n).remove();
                    //reset serial numbers
                        var addSerialNumber = function () {
                        var i = 0
                        $('table tr').each(function(index) {
                        $(this).find('td:nth-child(1)').html(index-1+1);
                        });
                        };

                        addSerialNumber();
                    //update count
                    var current_tot = parseInt($('#memcount').text());
                    $('#memcount').html(current_tot-1);
                    }
                    else{
                    $('#response').append('<p class="alert alert-warning">'+ response.message +'</p>');
                    }
                },
                error: function (data, err) {
                    $('#response').append('<p class="alert alert-warning">Delete Failed! '+ err +'</p>');
                    //alert("Delete Failed. Error = "+err);
                    //console.log(data);
                }

            });
  }
  else{return false;}
}


//edit
function edit(n){
 var fname = $('#fname-'+n).text();
//  var lname = $('#lname-'+n).text();
 var phone = $('#phone-'+n).text();
 
$("#edit-member").show('slow');

$('#fname_edit').val(fname);
// $('#lname_edit').val(lname);
$('#phone_edit').val(phone);
$('#add_hidden').prepend('<input type="hidden" name="n" value="'+n+'">');
}

//edit group
$('#memeditform').on("submit", function(e){ //or $(�form�).submit(function(e){
e.preventDefault();
$('#memeditresponse').empty();

var fname = $('#fname_edit').val();
var lname = null;//$('#lname_edit').val();
var phone = $('#phone_edit').val();
var n = $('input[name="n"]').val();

$.ajax({
url: 'scripts/edits.php',
method: 'post',
dataType    : 'json',
data: { id:n, src:'mem', fname:fname, lname:lname, phone:phone},
success: function(response){
if(response['status'] == 'success'){

//post response 
$('#memeditresponse').show().html('<p class="alert alert-success">  '+ response.message +' </p>');
$('#fname-'+n).html(fname + " " + lname);
// $('#lname-'+n).html(lname);
$('#phone-'+n).html(phone);

//remove response after 3 seconds
setTimeout(function() {
    $("#memeditresponse").fadeOut(1500);
  },3000);
  
}
else{
$('#memeditresponse').html('<p class="alert alert-warning">'+ response.message +'</p>');
}
}
,error: function(data){
$('#memeditresponse').html('<p class="alert alert-warning">  Error: Contact the admin! </p>');
//console.log(data);
}
});

});

</script>
  </body>
</html>