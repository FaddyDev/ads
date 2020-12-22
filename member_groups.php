<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Comms | Groups &mdash; ACK St. Peters Cathedral, Nyeri</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css"/>
 
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
  if(isset($_GET['memid'])){
  $memid = $_GET['memid'];
  $fname = $lname = ''; 
$groups = array();
try { 
   $sql = "SELECT g.id,g.group_name,g.group_category,m.fname,m.lname,mg.leader FROM groups g LEFT JOIN member_group mg on g.id = mg.fk_group_id LEFT JOIN members m ON mg.fk_member_id = m.id WHERE fk_member_id=? GROUP BY g.id"; 
    
    $stmt = $conn->prepare($sql);
    if($stmt->execute(array($memid)))
	{
	 while($row = $stmt -> fetch())
	 {
    $fname = $row['fname']; $lname = $row['lname'];
	   $groups[] = $row;
	 }
  }  
  }
catch(PDOException $e)
    {
    echo "<p class='alert alert-warning' style='text-align: center;'> Error: Operation failed quietly!". /* $e->getMessage().*/" <br>Please try again or contact the admin...</p>";
   }
$conn = null;
 ?>

    <div class="site-blocks-cover inner-page overlay" style="background-image: url(images/bg_groups.jpg);" data-aos="fade" data-stellar-background-ratio="0.5">
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7 text-center"><?php if(count($groups)>0){ ?>
            <h1 class="mb-5"><?php echo $fname.' '.$lname.'\'s'; ?> <strong>Groups</strong></h1>
          <?php } else { echo '<h1>Members\' <strong>Groups</strong></h1>';} ?>
          </div>
        </div>
      </div>
    </div>  
         
    <div class="site-section site-block-3 bg-light">
      <div class="container">
        <div class="row align-items-stretch">
          <div class="col-md-12 text-center">
            <h2 class="display-4 text-black mb-5"><?php if(count($groups)>0){ echo $fname.' '.$lname.'\'s 
              <strong>Groups</strong>';} else{ echo 'Members\' Groups';} ?></h2>
              <p class="justify-content-center"><?php if(count($groups)>0){ echo 'Groups that '.$fname.' '.$lname.' belongs to ';} 
              else{ echo 'Members\' Groups';} ?> 
    | Go to <a href='members.php'>Members</a></p>
        <p class="alert alert-info">Total Groups: <span id="grpcount"><?php echo count($groups); ?></span></p>
          </div>

        <div class="col-md-12 text-center">
        <table id="groups" class="table table-striped table-bordered table-hover" style="width:100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Group Name</th>
                <th>Group Category</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
       <?php  foreach($groups as $index=>$group){ ?>
            <tr>
                <td><?php echo $index+1 ?></td>
                <td><?php echo $group['group_name']; ?></td>
                <td><?php echo $group['group_category']; ?></td>
                <td><?php if($group['leader'] == 1){ echo 'Leader';}
                  else { echo 'Member';} ?></td> 
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
});
</script>
  </body>
</html>