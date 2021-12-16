<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Comms | Groups &mdash; ADS Mt. Kenya</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
    #edit-group{
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
$groups = $members = $group_addmems=array();
try { 
 
  //$sql = "SELECT * FROM members JOIN member_group on members.id = member_group.fk_member_id JOIN groups on groups.id = member_group.fk_group_id ";
  //$sql = "SELECT * FROM groups";
  $sql = "SELECT g.id,g.group_name,g.group_category,count(mg.fk_group_id) mems FROM groups g LEFT JOIN member_group mg on g.id = mg.fk_group_id GROUP BY g.id"; 
    
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
while($row = $stmt_mem -> fetch())
{
  $members[] = $row;
}
}


//fetch groups to add members to
$sql_addmems='';
if($_SESSION['position'] == 1){$sql_addmems = "SELECT * FROM groups";}//admin
else{ //JOIN members on members.id = member_group.fk_member_id
$sql_addmems = "SELECT g.id,g.group_name,g.group_category FROM member_group mg JOIN groups g on g.id = mg.fk_group_id and mg.fk_member_id=".$_SESSION['id']." AND mg.leader != 0 ";
} 
  $stmt_addmems = $conn->prepare($sql_addmems);
  if($stmt_addmems->execute())
{
 while($row = $stmt_addmems -> fetch())
 {
   $group_addmems[] = $row;
 }
}

}
catch(PDOException $e)
    {
    echo "<p class='alert alert-warning' style='text-align: center;'> Error: Operation failed quietly!". /* $e->getMessage().*/" <br>Please try again or contact the admin...</p>";
   }
$conn = null;


  /*foreach ($groups as $grpkey => $grpvalue) {
    $grpID = $grpvalue['id'];
    echo 'group ID '.$grpID.'<br>';
    if($grpID>=14 && $grpID<=20)
    {
    echo 'Required group ID:'.$grpID.'<br>';
      $i=0;
        foreach ($members as $memkey => $memvalue) {
          $memID = $memvalue['id'];
        //insert 100 members to the group
          if($i<100){
          if(!in_array($memID, $addedMembers)){
            $addedMembers[]=$memID;$leader=0;
            $sql = "INSERT INTO member_group (fk_member_id, fk_group_id, leader)
            VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt -> bindParam(1, $memID);
            $stmt -> bindParam(2, $grpID);
            $stmt -> bindParam(3, $leader);
            $stmt->execute();
            $i++;
          }   
          }       
        }
    }

  }
  echo 'done';*/


 ?>

    <div class="site-blocks-cover inner-page overlay" style="background-image: url(images/bg_groups.jpg);" data-aos="fade" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7 text-center">
            <h1 class="mb-5">ADS <strong>Programs/Groups</strong></h1>
          </div>
        </div>
      </div>
    </div>  
         
    <div class="site-section site-block-3 bg-light">
      <div class="container">
        <div class="row align-items-stretch">
          <div class="col-md-12 text-center">
            <h2 class="display-4 text-black mb-5">Various Groups</h2>
        <p class="alert alert-info">Total Groups: <span id="grpcount"><?php echo count($groups).' | 
          <a href="member_groups.php?memid='.$_SESSION['id'].'">
          My Groups</a> | <a href="#addmems">Add Members to Groups</a> '; ?></span></p>
          </div>

        


       <?php if($_SESSION["position"] == 1){ //admin only ?>
        <!-- Edit Form-->
        <div class="site-section bg-light" id="edit-group">
      <div class="container">

        <div class="row">
       
          <div class="col-md-12 col-lg-8 mb-5">  

            <form class="p-5 bg-white" id="grpeditform" >
            <fieldset><legend>Edit Group | <a id="close" href="#"><i class="fa fa-times" data-toggle="tooltip" title="Close"></i></a></legend>

              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="grpeditresponse">
                  
                </div>
              </div>
              

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="category_edit">Group Category</label>
                  <select id="category_edit" name="category_edit"  class="input form-control" required>
                  <option value="Committee">Committee</option>
                  <option value="Department">Department</option>
                  <option value="Home-Based Fellowship">Home-Based Fellowship</option>
                  <option value="Service">Service</option>
                  </select>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="grpname_edit">Group Name</label>
                  <input type="text" id="grpname_edit" name="grpname_edit"  class="input form-control" placeholder="Group Name" required>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12" id="add_hidden">
                  <input type="submit" value="Update Group" class="btn btn-primary">
                </div>
              </div>

            </fieldset>
            </form>
          </div>

          <div class="col-lg-4">
            
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">More Info</h3>
              <p></p>
            </div>

            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">Note:</h3>
              
              <p class="mb-0 font-weight-bold">Inputs</p>
              <p class="mb-4">Category is the nature of the group e.g ProJects. Group name is the specific group,
              e.g. Murang'a Project.</p>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /edit form-->
      <?php } ?>




        <div class="col-md-12 text-center">
        <table id="groups" class="table table-striped table-bordered table-hover" style="width:100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Group Name</th>
                <th>Group Category</th>
                <th>Group Members</th>
                <?php if($_SESSION["position"] == 1){ //admin only 
                echo '<th>Edit</th>';
                echo '<th>Delete</th>';}
                ?>
            </tr>
        </thead>
        <tbody>
       <?php  foreach($groups as $index=>$group){ ?>
            <tr id="row-<?php echo $group['id']; ?>">
                <td><?php echo $index+1 ?></td>
                <td id="grpname-<?php echo $group['id']; ?>"><?php echo $group['group_name']; ?></td>
                <td id="grpcat-<?php echo $group['id']; ?>"><?php echo $group['group_category']; ?></td> 
                <td><?php echo $group['mems'];
                if($group['mems']>0){ echo '  <a href="group_members.php?groupid='.$group['id'].'">
                <i class="fa fa-eye text-secondary" data-toggle="tooltip" title="View"></i></a>';} ?></td>
                
                <?php if($_SESSION['position'] == 1){?>
                <td><a id="btn-<?php echo $group['id']; ?>" onclick="return edit(<?php echo $group['id']; ?>);" 
                data-toggle="tooltip" title="Edit group"> 
                <i class="fa fa-edit text-info"></i></a></td>
                
                <td><a id="btn-<?php echo $group['id']; ?>" onclick="return confirmdel(<?php echo $group['id']; ?>);" 
                data-toggle="tooltip" title="Delete group"> 
                <i class="fa fa-trash text-danger"></i></a></td>
                <?php } ?>
            </tr>
       <?php } ?>
            </tbody>
            </table>
            </div>
        </div>
      </div>
    </div>   

        </div>
      </div>
    </div>
    

     
     <div class="promo py-5 bg-primary" data-aos="fade">
      <div class="container text-center">
        <h2 class="d-block mb-0 font-weight-light text-white">
        <?php if($_SESSION["position"] == 1){ //admin only ?>
        Scroll down to add groups
        <?php }else{?>         
        <a href="https://ads-mtkenya.or.ke//" target="_blank" class="text-white d-block">Visit The Main Website</a>
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

            <form class="p-5 bg-white" id="grpaddform" method="POST" action="scripts/grpadd.php" enctype="multipart/form-data">
            <!--  method="POST" action="scripts/grpadd.php"  -->
            <fieldset><legend>Add Group</legend>

              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="response">
                  
                </div>
              </div>
              

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="category">Group Category</label>
                  <select id="category" name="category"  class="input form-control" required>
                  <option value="">Select Category</option>
                  <option value="Committee">Staff</option>
                  <option value="Department">Project</option>
                  <option value="Home-Based Fellowship">StakeHolders</option>
                  <option value="Service">Committee Members</option>
                  </select>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="grpname">Group Name</label>
                  <input type="text" id="grpname" name="grpname"  class="input form-control" placeholder="Group Name" required>
                </div>
              </div> 

            <div class="row p-5">
                <div class="col-md-12 mb-3 mb-md-0">
                  <button class="btn btn-info" id="new-individual-btn">Enter details by typing</button> |
                  <button class="btn" id="upload-btn">Upload an excel file</button>
                </div>
              </div> 
              
              <div class="row form-group" id="new-individual">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="group">Type name then hyphen (-) then phone numbers separated by commas (,)</label>
                  <br><span><em>Follow the given format strictly;</em></span>
                  <!-- <br><span><em>Only numbers(0-9), comma(,) and plus(+) are allowed with no commas at the begging and end.</em></span> -->
                  <textarea name="indivi-new" id="indivi-new" cols="30" rows="3" class="form-control" XonKeyPress="return numbersCommaonly(event)" placeholder="ADS Nyeri - 0700000000,ADS Kenya - +2547000000,..."></textarea>
                </div>
              </div>
              
              <div class="row form-group" id="upload-div" style="display: none;">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="group">Upload an excel file with the details</label>
                  <br><span><em>(The file MUST have two columns: A with names and B with phone numbers. Actual records to start at line 2. Line 1 is assumed to contain the title.
                    All numbers should start with 7 (because when you type 07... 0 is automatically hidden))</em></span>
                  <!-- <br><span><em>Only numbers(0-9), comma(,) and plus(+) are allowed with no commas at the begging and end.</em></span> -->
                  <input type="file" name="file_data" id="membersFile" class="form-control" required/>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12">
                  <input type="submit" id="AddGroup" value="Add Group" class="btn btn-primary">
                </div>
              </div>

            </fieldset>
            </form>
          </div>

          <div class="col-lg-4">
            
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">More Info</h3>
              <p>Add  groups here. The groups consist of different Departments</p>
            </div>

            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">Note:</h3>
              
              <p class="mb-0 font-weight-bold">Inputs</p>
              <p class="mb-4">Category is the nature of the group e.g Project. Group name is the specific group,
              e.g. Water Project.</p>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /add form-->
      <?php } ?>


<!-- Add members to group -->
<?php if($_SESSION["position"] == 1 || count($group_addmems)>0){ //admin only ?>
<div class="promo py-5 bg-primary" id="addmems" data-aos="fade">
      <div class="container text-center">
        <h2 class="d-block mb-0 font-weight-light text-white">
        Scroll down to add members to groups   
        </h2>
      </div>
    </div>     

    
        <!-- Form-->
        <div class="site-section bg-light">
      <div class="container">

        <div class="row">
       
          <div class="col-md-12 col-lg-8 mb-5">  

            <form class="p-5 bg-white" id="grpaddmemsform">
            <fieldset><legend>Add Members to Group</legend>

              <div class="row">
                <div class="col-md-12 mb-3 mb-md-0" id="grpresponse">
                  
                </div>
              </div>
              

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
                  <label class="font-weight-bold" for="group">Group Name | Category</label>
                  <select id="group" name="group"  class="input form-control" required>
                  <option value="">Select Group</option>
                  <?php foreach($group_addmems as $grp){ ?>
                  <option value="<?php echo $grp['id']; ?>"><?php echo $grp['group_name'].' | '.$grp['group_category']; ?></option>
                  <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row form-group">
                <div class="col-md-12 mb-3 mb-md-0">
        <table id="groupMems" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <p class="alert alert-info">Select the members of the group then indicate who the chair/leader is.</p>
            <tr>
                <th>Select</th>
                <th>Name</th>
                <!-- <th>Last Name</th> -->
                <th>Phone Number</th>
                <th data-toggle="tooltip" title="Check to select the leader/chair of the group among the selected members.">Leader?</th>
            </tr>
        </thead>
        <tbody>
       <?php  foreach($members as $mem){ ?>
            <tr>
                <td><!-- <input type="hidden" name="member[]" class="mems" value="0">-->
                <input type="checkbox" name="member[]" class="mems" value="<?php  echo $mem['id']; ?>"></td>
                <td><?php echo $mem['fname']; ?></td>
                <!-- <td><?php echo $mem['lname']; ?></td> -->
                <td><?php echo $mem['phone']; ?></td> 
                <!--<td> <input type="radio" name="leader" class="leader" value="<?php  //echo $mem['id']; ?>"> </td>-->
                <td><!-- <input type="hidden" name="member[]" class="mems" value="0">-->
                <input type="checkbox" name="leadership[]" class="mems" value="<?php  echo $mem['id']; ?>"></td>
            </tr>
       <?php } ?>
            </tbody>
            </table>

              <div class="row form-group">
                <div class="col-md-12">
                  <input type="submit" value="Add Members" class="btn btn-primary">
                </div>
              </div>
            </form>

            </fieldset>
            </form>
          </div>

          <div class="col-lg-4">
            
            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">More Info</h3>
              <p>....................................................................</p>
            </div>

            <div class="p-4 mb-3 bg-white">
              <h3 class="h5 text-black mb-3">Note:</h3>
              
              <p class="mb-0 font-weight-bold">Inputs</p>
              <p class="mb-4">..............................................................................</p>

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
            Copyright &copy; <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>document.write(new Date().getFullYear());</script> All Rights Reserved | <a href="https://ads-mtkenya.or.ke//" target="_blank" >ACK St. Peters Cathedral, Nyeri</a>
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
 <!-- Data table -->
  <script src="DataTables-1.10.18/dataTables.min.js"></script>  
  <!--<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>-->
  <script src="DataTables-1.10.18/datatables.1.10.19.jquery.dataTables.min.js"></script>

  <script src="js/main.js"></script>
     
<script>
$(document).ready(function(){
    $('#groups').DataTable();
    $("#groups").css("width","100%");
    $('#groupMems').DataTable();
/*$('.input').keydown(function(){
  $('#response').empty();
});
*/

var isTyped = 1;

$('#new-individual-btn').click(function(e){
  e.preventDefault();
  $('#new-individual').show('slow'); 
  $('#upload-btn').removeClass('btn-info'); 
  $(this).addClass('btn-info'); 
  isTyped = 1;
  $('#upload-div').hide('slow');
});

$('#upload-btn').click(function(e){
  e.preventDefault();
  $('#upload-div').show('slow'); 
  $('#new-individual-btn').removeClass('btn-info'); 
  $(this).addClass('btn-info');
  isTyped = 0;
  $('#new-individual').hide('slow');
});

//$('#grpaddform').on("submit", function(e){ //or $(�form�).submit(function(e){

$('#AddGroup').on("click", function(e){
e.preventDefault();
$('#response').empty();

var category = $('#category :selected').val();
var grpname = $('#grpname').val();
var individuals_new = '';//$('#indivi-new').val().trim();//get rid of white spaces
individuals_new = $('#indivi-new').val().trim();//get rid of white spaces

//show and clear response field
$('#response').show().html("");
if(category=='' || grpname==''){
$('#response').html('<p class="alert alert-warning">  Fill in all fields! </p>');
return false;
}

if(isTyped==1 && individuals_new==''){
$('#response').html('<p class="alert alert-warning"> The contacts list cannot be empty. Enter some names and numbers.</p>');
return false;
}

var file_data = $("#membersFile").prop("files")[0];

if(isTyped==0 && file_data==null){
$('#response').html('<p class="alert alert-warning"> No file uploaded. Check!</p>');
return false;
}


$('#response').html('<p class="alert alert-info"> Processing request, please wait...</p>');

var formData = new FormData();
formData.append("category", category);
formData.append("grpname", grpname);
formData.append("indivs", individuals_new);
formData.append("isTyped", isTyped);
formData.append("file_data", file_data);

$.ajax({
url: 'scripts/grpadd.php',
method: 'post',
dataType    : 'json',
processData: false, 
contentType: false,
//data: { category:category, grpname:grpname, indivs:individuals_new, file_data:file_data, isTyped:isTyped},
data: formData,
success: function(response){
if(response['status'] == 'success'){

//post response 
$('#response').html('<p class="alert alert-success">  '+ response.message +' </p>');
$('#grpaddform')[0].reset(); //rest form

//remove response after 3 seconds
setTimeout(function() {
    $("#response").fadeOut(1500);
  },3000);

 //update count
 var current_total = parseInt($('#grpcount').html());
 $('#grpcount').html(current_total+1);

//append the data to table
$('#groups').append('<tr> <td>'+(current_total+1)+'</td> <td>'+grpname+'</td> <td>'+category+'</td>'+
 '<td>0</td></tr>');

}
else{
$('#response').html('<p class="alert alert-warning">'+ response.message +'</p>');
}
}
,error: function(data){
$('#response').html('<span class="alert alert-warning">  Error: Contact the admin! </span>');
console.log(data);
}
});

});


//add members to group
$('#grpaddmemsform').on("submit", function(e){ //or $(�form�).submit(function(e){
e.preventDefault();
$('#grpresponse').empty();

var group = $('#group :selected').val();
/*var members = $('input[name="member[]"] :checked').map(function(){ 
                    return this.value; 
                }).get();
var leader = $('input[name="leader"] :checked').val(); //to sort
var leader = 0;*/


//show and clear response field
$('#grpresponse').show().html("");
if(group==''){
$('#grpresponse').html('<p class="alert alert-warning"> Failed! Ensure you\'ve selected a group and indicated a leader. </p>');
return false;
}

//alert(members); return false;
$.ajax({
url: 'scripts/grpmemsadd.php',
method: 'post',
dataType    : 'json',
//data: { group:group, leader:leader, 'members[]':members},
data: $('#grpaddmemsform').serialize(),
success: function(response){
if(response['status'] == 'success'){

//post response 
$('#grpresponse').html('<p class="alert alert-success">  '+ response.message +' </p>');
$('#grpaddmemsform')[0].reset(); //rest form

//remove response after 3 seconds
setTimeout(function() {
    $("#grpresponse").fadeOut(1500);
  },3000);
  
}
else{
$('#grpresponse').html('<p class="alert alert-warning">'+ response.message +'</p>');
}
}
,error: function(data){
$('#grpresponse').html('<p class="alert alert-warning">  Error: Contact the admin! </p>');
//console.log(data);
}
});

});

$('#close').on('click',function(e){
  e.preventDefault();
  $("#edit-group").hide('slow');
});
});

function confirmdel(n){
  if(confirm('Are you really sure you want to delete this group?'))
  {
      $.ajax({
                url: 'scripts/deletes.php',
                type: "get",
                dataType: 'json',
                data: { src: 'group', id: n },
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
                    var current_tot = parseInt($('#grpcount').text());
                    $('#grpcount').html(current_tot-1);
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
 var grpname = $('#grpname-'+n).text();
 var grpcat = $('#grpcat-'+n).text();
 
$("#edit-group").show('slow');

$('#grpname_edit').val(grpname);
$('#category_edit').prepend('<option value="'+grpcat+'" selected>'+grpcat);
$('#add_hidden').prepend('<input type="hidden" name="n" value="'+n+'">');
}

//edit group
$('#grpeditform').on("submit", function(e){ //or $(�form�).submit(function(e){
e.preventDefault();
$('#grpeditresponse').empty();

var grpcat = $('#category_edit :selected').val();
var grpname = $('#grpname_edit').val();
var n = $('input[name="n"]').val();
$.ajax({
url: 'scripts/edits.php',
method: 'post',
dataType    : 'json',
data: { id:n, src:'group', cat:grpcat, name:grpname},
success: function(response){
if(response['status'] == 'success'){

//post response 
$('#grpeditresponse').show().html('<p class="alert alert-success">  '+ response.message +' </p>');
$('#grpname-'+n).html(grpname);
$('#grpcat-'+n).html(grpcat);

//remove response after 3 seconds
setTimeout(function() {
    $("#grpeditresponse").fadeOut(1500);
  },3000);
  
}
else{
$('#grpeditresponse').html('<p class="alert alert-warning">'+ response.message +'</p>');
}
}
,error: function(data){
$('#grpeditresponse').html('<p class="alert alert-warning">  Error: Contact the admin! </p>');
//console.log(data);
}
});

});


//});//doc.ready
</script>
  </body>
</html>