<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//Connect to database via another page
include_once("dbconn.php");

?>

<?php
if(isset($_POST['phone'])){

$phone=$_POST['phone'];
$pass=$_POST['password'];
$status = ''; $message = '';
$sql="SELECT * FROM members WHERE phone=? ";
$stmt = $conn->prepare($sql);
if($stmt->execute(array($phone)))
	{
    if($stmt->rowCount() == 1){
        while($row = $stmt -> fetch())
        {
         if(password_verify($pass,$row['password']) == 1)
          {
           $status = 'success'; $message = 'Sign in successful! You can now go to any page you wish.';
           $_SESSION['is_logged'] = true;
           $_SESSION['phone'] = $row['phone'];
           $_SESSION['fname'] = $row['fname'];
           $_SESSION['position'] = $row['position'];
           $_SESSION['id'] = $row['id'];
          }
          else{
            $status = 'fail'; $message = 'Sign in failed! Check password and try again.';
          }
        }
    }
    else{
      $status = 'fail'; $message = 'Sign in failed! Wrong phone number or password.';
    }
}
else{
  $status = 'fail'; $message = 'Sign in failed! Kindly try again.';
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