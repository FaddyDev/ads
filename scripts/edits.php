
<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//Connect to database via another page
include_once("dbconn.php");

?>

<?php
if(isset($_POST['src'])){

$src=$_POST['src'];
$id=$_POST['id'];
$status=$message='';

//editing groups
if($src == 'group'){
  $name=$_POST['name'];
  $cat=$_POST['cat'];
  $sql="UPDATE groups SET group_category=?, group_name=? WHERE id=? ";
  $stmt = $conn->prepare($sql);
  if($stmt->execute(array($cat,$name,$id)))
    {
      $status = 'success'; $message = 'Group details updated successfully.';
    }
    else{
      $status = 'fail'; $message = 'Update failed! Try again or contact the admin.';
    }
      
  $conn = null;
  
  $resp = array('status'=>$status,'message'=>$message);
  echo json_encode($resp);
  }

  //deleting members
if($src == 'mem'){
  $fname=ucwords(strtolower($_POST['fname'])); //Fred
  $lname=ucwords(strtolower($_POST['lname'])); //Makokha
  $phone=$_POST['phone'];
  $sql="UPDATE members SET fname=?, lname=?, phone=? WHERE id=? ";
  $stmt = $conn->prepare($sql);
  if($stmt->execute(array($fname,$lname,$phone,$id)))
    {
      $status = 'success'; $message = 'Member details updated successfully.';
    }
    else{
      $status = 'fail'; $message = 'Update failed! Try again or contact the admin.';
    }
      
  $conn = null;
  
  $resp = array('status'=>$status,'message'=>$message);
  echo json_encode($resp);
  }

}//if no source
else{ ?>
    <script>
    window.location.href = '../index.php';
    </script>
<?php }

?>