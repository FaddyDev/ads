<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Comms | Members &mdash; ACK St. Peters Cathedral, Nyeri</title>
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
?>
    <?php 
  include("scripts/dbconn.php"); //DB
  if(isset($_GET['groupid'])){
  $groupid = $_GET['groupid'];
  $leader = 0;
  $group_name = $group_category = ''; 
$members = array();
try { 
 $sql = "SELECT m.id AS mid,m.fname,m.lname,m.phone,g.group_name,g.group_category,mg.leader,mg.id FROM members m JOIN member_group mg on m.id = mg.fk_member_id LEFT JOIN groups g ON mg.fk_group_id = g.id WHERE fk_group_id=?  GROUP BY m.id"; 
    $stmt = $conn->prepare($sql);
    if($stmt->execute(array($groupid)))
	{
	 while($row = $stmt -> fetch())
	 {
    $group_name = $row['group_name'];$group_category = $row['group_category'];
	   $members[] = $row;
	 }
  } 
  
  //check if the logged in user is a leader of this group 
  $sql_leader = "SELECT * FROM member_group WHERE fk_group_id=? AND fk_member_id=? AND leader=?";
  $ld=1;
  $stmt_leader = $conn->prepare($sql_leader);
  if($stmt_leader->execute(array($groupid,$_SESSION['id'],$ld))){
    $leader = $stmt_leader->rowCount();
  }

  }
catch(PDOException $e)
    {
    echo "<p class='alert alert-warning' style='text-align: center;'> Error: Operation failed quietly!".  /*$e->getMessage()*/" <br>Please try again or contact the admin...</p>";
   }
	
$conn = null;
 ?>

    <div class="site-blocks-cover inner-page" style="background-image: url(images/bg_members.jpg);" data-aos="fade" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-7"><?php if(count($members)>0){ ?>
            <span class="sub-text"><?php echo $group_name; ?> members</span>
            <h1>The <?php echo $group_name; ?>  <strong><?php echo $group_category; ?></strong></h1>
          <?php } else { echo '<h1>Group <strong>Membership</strong></h1>';} ?>
          </div>
        </div>
      </div>
    </div>  

    <div class="site-section site-block-3 bg-light">
      <div class="container">
        <div class="row align-items-stretch">
          <div class="col-md-12 text-center">
            <h2 class="display-4 text-black mb-5"><?php echo $group_name; ?> <strong><?php echo $group_category; ?></strong></h2>
     <p class="justify-content-center"><?php if(count($members)>0) echo 'Members of the '.$group_name.' '.$group_category.' |'; ?> 
     Go back to <a href='groups.php'>groups</a></p>
          </div>
        <div class="col-md-12 text-center">
        <table id="members" class="table table-striped table-bordered table-hover" style="width:100%">
        <thead>
        <p class="alert alert-info" id="response">Total Members: <span id="memcount"><?php echo count($members); ?></span></p>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Position</th>
                <?php if($_SESSION['position'] == 1 || $leader == 1){
                echo '<th>Remove</th>';}?>
            </tr>
        </thead>
        <tbody>
       <?php  foreach($members as $index=>$mem){ ?>
            <tr id="row-<?php echo $mem['id']; ?>">
                <td><?php echo $index+1; ?></td>
                <td><?php echo $mem['fname']; ?></td>
                <td><?php echo $mem['lname']; ?></td>
                <td><?php echo $mem['phone']; ?></td> 
               <!-- <td><?php //echo $mem['leader']; ?></td>  -->
                <td><?php if($mem['leader'] == 1){ echo 'Leader';}
                  else { echo 'Member';} 
                    //<i class="fa fa-times text-secondary" data-toggle="tooltip" title="Not A Leader"></i>?></td>
                <?php if($_SESSION['position'] == 1 || $leader == 1){?>
                <td><a id="btn-<?php echo $mem['id']; ?>" onclick="return confirmdel(<?php echo $mem['id']; ?>);" 
                data-toggle="tooltip" title="Remove from group"> 
                <i class="fa fa-trash text-danger"></i></a>
                <?php }
                // else{?> <!--<i class="fa fa-ban" data-toggle="tooltip" title="You're not the group leader"></i> <?php //} ?>--></td>
            </tr>
       <?php } ?>
            </tbody>
            </table>
       </div>
        </div>
      </div>
    </div>

  <?php } else { ?>
    
    <div class="promo py-5 bg-primary" data-aos="fade">
      <div class="container text-center">
        <h2 class="d-block mb-0 font-weight-light text-white">       
        <a href="index.php" class="text-white d-block">Unauthorized access! Go Home!</a>
        </h2>
      </div>
    </div>
  <?php } ?>
    
    <footer class="site-footer">
      <div class="container">
        
        
        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <p>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            Copyright &copy; <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>document.write(new Date().getFullYear());</script> All Rights Reserved | <a href="https://www.ackstpeterscathedralnyeri.org/" target="_blank" >ACK St. Peters Cathedral, Nyeri</a>
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
    $('#members').DataTable();
});


function confirmdel(n){
  if(confirm('Are you really sure you want to remove this member from the group?'))
  {
      //var href = $('#btn-'+n).attr("data-href");
      //var href = '<a href="scripts/deletes.php?src=grpmem&id='+n+'">';
      $.ajax({
                //url: href,
                url: 'scripts/deletes.php',
                type: "get",
                dataType: 'json',
                data: { src: 'grpmem', id: n },
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

</script>
  </body>
</html>