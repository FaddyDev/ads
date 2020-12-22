
<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//Connect to database via another page
include_once("dbconn.php");

?>

<?php
if(isset($_GET['src'])){

$src=$_GET['src'];
$id=$_GET['id'];
$status=$message='';

//removing member from group | one by one for now; later select many
if($src == 'grpmem'){
$sql="DELETE FROM member_group WHERE id=? ";
$stmt = $conn->prepare($sql);
if($stmt->execute(array($id)))
	{
    $status = 'success'; $message = 'Member removed successfully.';
  }
  else{
    $status = 'fail'; $message = 'Removal failed! Try again or contact the admin.';
  }
    
$conn = null;

$resp = array('status'=>$status,'message'=>$message);
echo json_encode($resp);
}

//deleting groups
if($src == 'group'){
  //$sql="DELETE FROM groups WHERE id=? ";
  $sql="DELETE g.*, mg.* FROM groups g LEFT JOIN member_group mg ON g.id = mg.fk_group_id WHERE g.id = ?";
  $stmt = $conn->prepare($sql);
  if($stmt->execute(array($id)))
    {
      $status = 'success'; $message = 'Group deleted successfully.';
    }
    else{
      $status = 'fail'; $message = 'Removal failed! Try again or contact the admin.';
    }
      
  $conn = null;
  
  $resp = array('status'=>$status,'message'=>$message);
  echo json_encode($resp);
  }

  //deleting members
if($src == 'mem'){
  $sql="DELETE m.*, mg.* FROM members m LEFT JOIN member_group mg ON m.id = mg.fk_member_id WHERE m.id = ?";
  $stmt = $conn->prepare($sql);
  if($stmt->execute(array($id)))
    {
      $status = 'success'; $message = 'Member deleted successfully.';
    }
    else{
      $status = 'fail'; $message = 'Removal failed! Try again or contact the admin.';
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