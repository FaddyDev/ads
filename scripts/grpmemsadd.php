<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//Connect to database via another page
include_once("dbconn.php");

?>

<?php
if(isset($_POST['group'])){

$group=$_POST['group'];
$leader = 0;//isset($_POST['leader']) ? $_POST['leader'] : 0;
$status = ''; $message = '';
foreach($_POST['member'] as $mem){ //ensure a member is added only once
$sqlcount="SELECT * FROM member_group WHERE fk_group_id =? AND fk_member_id =? ";
$stmtcount = $conn->prepare($sqlcount);
if($stmtcount->execute(array($group, $mem)))
	{
  if($stmtcount->rowCount() == 0){ //only proceed if member does not exist
    try 
      {    
      //if($mem == $leader){$ld=1;}
      $leader = in_array($mem,$_POST['leadership']) ? 1:0;

      $sql = "INSERT INTO member_group (fk_member_id, fk_group_id, leader)
        VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1, $mem);
        $stmt -> bindParam(2, $group);
        $stmt -> bindParam(3, $leader);
        //$stmt->execute();
          if($stmt->execute()){
            $status = 'success'; $message = 'Group members added successfully! You can add another.';
          }else{
            $status = 'fail'; $message = 'Operation failed due to an unknown error. Contact the admin.';
          }
      }
      catch(PDOException $e)
      {
      $status = 'fail'; $message = 'Failed. Database Error: Contact admin';// . $e->getMessage();
      }
    }
    else{
      $status = 'fail'; $message = 'Some group members exist. Others have been added.';
    }
  }
  else{
    $status = 'fail'; $message = 'Operation failed due to an unknown error. Contact the admin.';
  }
}
$conn = null;

$resp = array('status'=>$status,'message'=>$message);
echo json_encode($resp);
}
//if no phone
else{ ?>
    <script>
    window.location.href = '../index.php';
    </script>
<?php }

?>