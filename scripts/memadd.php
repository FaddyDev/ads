<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//Connect to database via another page
include_once("dbconn.php");

?>

<?php
if(isset($_POST['phone'])){

$fname=ucwords(strtolower($_POST['fname'])); //Fred
$lname=ucwords(strtolower($_POST['lname'])); //Makokha
$phone=$_POST['phone'];
$groups=array(); if(isset($_POST['groups'])){$groups=$_POST['groups'];}
$init_hashed_pass = password_hash($phone, PASSWORD_DEFAULT);
$position = 0;

$status = ''; $message = '';
$sqlcount="SELECT * FROM members WHERE phone=? ";
$stmtcount = $conn->prepare($sqlcount);
if($stmtcount->execute(array($phone)))
	{
  if($stmtcount->rowCount() == 0){ //only proceed if phone does not exist
    try 
      {    
      $sql = "INSERT INTO members (fname, lname, phone, password, position)
        VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1, $fname);
        $stmt -> bindParam(2, $lname);
        $stmt -> bindParam(3, $phone);
        $stmt -> bindParam(4, $init_hashed_pass);
        $stmt -> bindParam(5, $position);
        //$stmt->execute();
          if($stmt->execute()){
            $status = 'success'; $message = 'Member added successfully! You can add another.';
          }else{
            $status = 'fail'; $message = 'Operation failed due to an unknown error. Contact the admin.';
          }

          //add the member to selected group(s)
          $sqlmem="SELECT * FROM members WHERE phone=? ";
          $memId = 0;
          $stmtmem = $conn->prepare($sqlmem);
          if($stmtmem->execute(array($phone)))
          {
            while($row = $stmtmem -> fetch()) {
              $memId = $row["id"];
            }
          }

          $leader = 0;

          foreach($groups as $grpId)
          {
            $sqlInsert = "INSERT INTO member_group (fk_member_id, fk_group_id, leader)
            VALUES (?,?,?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert -> bindParam(1, $memId);
            $stmtInsert -> bindParam(2, $grpId);
            $stmtInsert -> bindParam(3, $leader);
            $stmtInsert->execute();
          }

      }
      catch(PDOException $e)
      {
      $status = 'fail'; $message = 'Failed. Database Error: Contact admin';// . $e->getMessage();
      }
    }
    else{
      $status = 'fail'; $message = 'The phone number exists. Chances are the member is already registered here. Confirm first.';
    }
  }
  else{
    $status = 'fail'; $message = 'Operation failed due to an unknown error. Contact the admin.';
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