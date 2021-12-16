<?php if(session_status()==PHP_SESSION_NONE){
session_start();} 
require_once '../vendor/autoload.php';

use AfricasTalking\SDK\AfricasTalking;
//Connect to database via another page
//class msgsend{
  //var_dump($_POST);
  if(isset($_POST['msg'])){
    $msg=$_POST['msg'];
    $grp_id=array(); if(isset($_POST['grp_id'])){$grp_id=$_POST['grp_id'];}
    $mems=array(); if(isset($_POST['mems'])){$mems=array_map('intval', $_POST['mems']);}
    $indivs=''; if(isset($_POST['indivs'])){$indivs=$_POST['indivs'];}
    $indivs = str_replace(' ', '', trim($indivs)); $indivs = preg_replace('/\s+/', '', $indivs); 
    $indivs = rtrim($indivs, ','); /*remove last comma*/ 
    smssending($msg,$grp_id,$mems,$indivs);
  }

function smssending($msg,$grp_id,$mems,$indivs){
include_once("dbconn.php");
//if(isset($_POST['msg'])){
//require_once('../partials/AfricasTalking/src/AfricasTalking.php');


//$msg=$_POST['msg'];
//$grp_id=$_POST['grp_id'];

$status = ''; $fails = 'Message not sent to: '; $message = '';$total =0;$sucs=0;$selected=0;

$sql = ''; $stmtresponse=true;

$recipients=array();
$sender = $_SESSION['id'];
//if new individuals
$new=0;
//echo 'total=>'.$indivs; exit;
if($indivs !== '')
{
  $indivs = explode(",", $indivs); 
  $new=1;
}

//if existing individuals
if(count($mems) > 0)
{
  $selected=1;
  $sql="SELECT id,phone FROM members WHERE id IN (".implode(',', $mems).") ";
}

//all church members
if(in_array('0',$grp_id) && count($grp_id)==1){
  $sql="SELECT id,phone FROM members ORDER BY id";
}

//other groups
if(!in_array('0',$grp_id) && count($grp_id)>0){
$sql="SELECT members.id,phone FROM member_group LEFT JOIN members ON members.id=member_group.fk_member_id WHERE fk_group_id  in (".implode(',', $grp_id).") ";
}
//echo 'new, selected, or group sql:'.$sql; exit;

if($new == 1){
  //if new just send - no saving message
  foreach($indivs as $ind)
  {
    $total+=1;
    $ind = str_replace(' ', '', trim($ind)); $ind = preg_replace('/\s+/', '', $ind); $ind = rtrim($ind, ',');
    //$recipients[] = $ind;
           //Send message
           //check for presence of kil switch and only run in it's absence
           if(!file_exists('stop.txt')){
          $ret = sendMessage($ind,$msg); 
           }else
           {
             //if kill switch exists, kill the processes here, then disable the kill switch.
             $status='cancelled'; 
             unlink('stop.txt');
              break;
           }
           if($ret){
             $sucs+=1; $status='success';$message = 'Message(s) sent.';
           }else{ $status = 'failed'; $fails .= $ind.' | '; $message = 'Message not sent to ('.$ind.')! Operation failed due to an unknown error. Contact the admin.';}

  }
}
else{//not new
$stmt = $conn->prepare($sql);  
$stmtresponse = $stmt->execute();
if($stmtresponse)
	{
  $total = $stmt->rowCount();
  if($total > 0){ //only proceed if group exists
    try 
      {
        while($row = $stmt -> fetch())
        {
           $phone = $row['phone'];
           if($phone[0] !== '+'){$phone = '+254'.$phone;}
           $recipient = $row['id'];
           $grp=0;
           if($selected==0){$grp=$grp_id[0];}//just take the first value of id 
           //Send message
           $state='';
           //check for presence of kil switch and only run in it's absence
           if(!file_exists('stop.txt')){
          $ret = sendMessage($phone,$msg); 
           }else
           {
             //if kill switch exists, kill the processes here, then disable the kill switch.
             $status='cancelled'; 
             unlink('stop.txt');
              break;
           }
          if($ret){
            $sucs+=1; $state='Success'; 
          }else{ $state = 'Failed'; $fails .= $phone.' | ';}
          $sqlinsert = "INSERT INTO messages (sender_id, recipient_id, group_id,message,status)
        VALUES (?,?,?,?,?)";
        $stmtinsert = $conn->prepare($sqlinsert);
        $stmtinsert -> bindParam(1, $sender);
        $stmtinsert -> bindParam(2, $recipient);
        $stmtinsert -> bindParam(3, $grp);
        $stmtinsert -> bindParam(4, $msg);
        $stmtinsert -> bindParam(5, $state);
        //$stmt->execute();
          if($stmtinsert->execute()){
            $status = 'success'; 
          }else{
            $status = 'fail'; $message = 'Message could not be saved to db! ('.$phone.') Operation failed due to an unknown error. Contact the admin.';
          }
        }     
      }
      catch(PDOException $e)
      {
      $status = 'fail'; $message = 'Failed. Database Error: Contact admin';// . $e->getMessage();
      }
    }
    else{
      $status = 'fail'; $message = 'Group has no members or does not exist.';
    }
  }
  else{
    $status = 'fail'; $message = 'DB fetch operation failed due to an unknown error. Contact the admin.';
  }
}//not new
$conn = null;

if($fails == 'Message not sent to: '){$fails='';}
$resp = array('status'=>$status,'message'=>$message,'total'=>$total,'success_msgs'=>$sucs,'fails'=>$fails);
echo json_encode($resp);
//return json_encode($resp);
}


function sendMessage($rcpt,$msg) {
  // Set your app credentials
  $username   = "YOURUSERNAME";
  $apikey     = "YOURAPIKEY";
  
  // Initialize the SDK
  $AT         = new AfricasTalking($username, $apikey);
  
  // Get the SMS service
  $sms        = $AT->sms();
    // Set the numbers you want to send to in international format
    $recipients = $rcpt;

    // Set your message
    $themessage    = $msg;

    // Set your shortCode or senderId
    $from       = "YOURSENDERID";
  $rt = true;
    try {
        // Thats it, hit send and we'll take care of the rest
        $result = $sms->send([
            'to'      => $recipients,
            'message' => $themessage,
            'from'    => $from
        ]);

        //print_r($result);
        $rt = true;
    } catch (Exception $e) {
        //echo "Error: ".$e.getMessage();
        $rt = false;
    }
    return $rt;
}
//}
?>
