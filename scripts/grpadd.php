<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//Connect to database via another page
include_once("dbconn.php");
//require "../vendor/autoload.php";

?>

<?php
if(isset($_POST['grpname'])){

$grpname=$_POST['grpname'];
$category=$_POST['category'];

$indivs=''; if(isset($_POST['indivs'])){$indivs=$_POST['indivs'];}
$indivs = /*str_replace(' ', '', */trim($indivs); //$indivs = preg_replace('/\s+/', '', $indivs); 
$isTyped = $_POST['isTyped'];
$indivs = rtrim($indivs, ','); /*remove last comma*/ 

if($indivs !== '')
{
  $indivs = explode(",", $indivs); 
}

$status = ''; $message = '';
$sqlcount="SELECT * FROM groups WHERE group_name=? AND group_category=? ";
$stmtcount = $conn->prepare($sqlcount);
if($stmtcount->execute(array($grpname,$category)))
	{
  if($stmtcount->rowCount() !== null){ //only proceed if grpname does not exist
    try 
      {    
      $sql = "INSERT INTO groups (group_name, group_category)
        VALUES (?,?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1, $grpname);
        $stmt -> bindParam(2, $category);
        //$stmt->execute();
          if($stmt->execute()){
            $status = 'success'; $message = 'Group added successfully! You can add another.';

          //fetch the group id
          $sqlGrp="SELECT * FROM groups WHERE group_name=? AND group_category=? ";
          $grpId = 0;
          $stmtGrp = $conn->prepare($sqlGrp);
          if($stmtGrp->execute(array($grpname,$category)))
          {
            while($rowGrp = $stmtGrp -> fetch()) {
              $grpId = $rowGrp["id"];
            }
          }         

          //add members now
          //either typed or uploaded members
          if((int)$isTyped === 1)
          {            
            foreach($indivs as $memNamePhone)
            {
              //we expect this to be at least 11 characters long - 0712345678
              if(strlen($memNamePhone) >= 11)
              {                
                $memNamePhone1 = trim($memNamePhone); //no leading white spaces
                //explode again to get name and phone number
                $explodedNameandPhone = explode("-", $memNamePhone1); 
                $trimmedName = $explodedNameandPhone[0];
                $trimmedPhone = str_replace(' ', '', trim($explodedNameandPhone[1])); 

                //insert member
                $sqlcount1="SELECT * FROM members WHERE phone=? ";
                $stmtcount1 = $conn->prepare($sqlcount1);
                if($stmtcount1->execute(array($trimmedPhone)))
                  {
                  if($stmtcount1->rowCount() == 0)
                  {
                    $position = 0; $lname = null;
                    $init_hashed_pass = password_hash($trimmedPhone, PASSWORD_DEFAULT);
                    $sqlMem = "INSERT INTO members (fname, lname, phone, password, position)
                    VALUES (?,?,?,?,?)";
                    $stmtMem = $conn->prepare($sqlMem);
                    $stmtMem -> bindParam(1, $trimmedName);
                    $stmtMem -> bindParam(2, $lname);
                    $stmtMem -> bindParam(3, $trimmedPhone);
                    $stmtMem -> bindParam(4, $init_hashed_pass);
                    $stmtMem -> bindParam(5, $position);
                    $stmtMem->execute();

                    //add member to group
                    $sqlmem="SELECT * FROM members WHERE phone=? ";
                    $memId = 0;
                    $stmtmem = $conn->prepare($sqlmem);
                    if($stmtmem->execute(array($trimmedPhone)))
                    {
                      while($row = $stmtmem -> fetch()) {
                        $memId = $row["id"];
                      }
                    }

                    $sqlcount2="SELECT * FROM member_group WHERE fk_group_id =? AND fk_member_id =? ";
                    $stmtcount2 = $conn->prepare($sqlcount2);
                    if($stmtcount2->execute(array($grpId, $memId)))
                      {
                      if($stmtcount2->rowCount() == 0)
                      { 
                        $leader = 0;
                        $sqlInsert = "INSERT INTO member_group (fk_member_id, fk_group_id, leader)
                        VALUES (?,?,?)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert -> bindParam(1, $memId);
                        $stmtInsert -> bindParam(2, $grpId);
                        $stmtInsert -> bindParam(3, $leader);
                        $stmtInsert->execute();
                      }
                    }
                  }
                }
              }
            }
          }
          //uploaded members
          else
          {
            require_once '../vendor/phpexcel/Classes/PHPExcel.php';
            $tmpfname = $_FILES['file_data']['tmp_name'];
            $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
            $excelObj = $excelReader->load($tmpfname);
            $worksheet = $excelObj->getSheet(0);
            $lastRow = $worksheet->getHighestRow();

            $sheetData = array();
            for ($row = 2; $row <= $lastRow; $row++) 
            {
              $index = $row-2;                  
              $sheetData[$index][0] = trim($worksheet->getCell('A'.$row)->getValue()); 
              $sheetData[$index][1] = str_replace(' ', '', trim($worksheet->getCell('B'.$row)->getValue())); 
            }
                
            for ($index  = 0; $index < ($lastRow-1); $index++)
            {                                    
              $trimmedName = $sheetData[$index][0];//trim($worksheet->getCell('A'.$row)->getValue()); 
              $trimmedPhone = $sheetData[$index][1];//str_replace(' ', '', trim($worksheet->getCell('B'.$row)->getValue())); 
              
              if($trimmedName !== '' && $trimmedPhone !== '')
              {
                if($trimmedPhone[0] !== '+'){$trimmedPhone  = '+254'.$trimmedPhone;}
                //echo $trimmedName.'->'.$trimmedPhone.'<br>';
                //insert member
                $sqlcount1="SELECT * FROM members WHERE phone=? ";
                $stmtcount1 = $conn->prepare($sqlcount1);
                if($stmtcount1->execute(array($trimmedPhone)))
                {
                  if($stmtcount1->rowCount() == 0)
                  {
                    $position = 0; $lname = null;
                    $init_hashed_pass = password_hash($trimmedPhone, PASSWORD_DEFAULT);
                    $sqlMem = "INSERT INTO members (fname, lname, phone, password, position)
                    VALUES (?,?,?,?,?)";
                    $stmtMem = $conn->prepare($sqlMem);
                    $stmtMem -> bindParam(1, $trimmedName);
                    $stmtMem -> bindParam(2, $lname);
                    $stmtMem -> bindParam(3, $trimmedPhone);
                    $stmtMem -> bindParam(4, $init_hashed_pass);
                    $stmtMem -> bindParam(5, $position);
                    $stmtMem->execute();

                    //add member to group
                    $sqlmem="SELECT * FROM members WHERE phone=? ";
                    $memId = 0;
                    $stmtmem = $conn->prepare($sqlmem);
                    if($stmtmem->execute(array($trimmedPhone)))
                    {
                      while($row = $stmtmem -> fetch()) {
                        $memId = $row["id"];
                      }
                    }

                    $sqlcount2="SELECT * FROM member_group WHERE fk_group_id =? AND fk_member_id =? ";
                    $stmtcount2 = $conn->prepare($sqlcount2);
                    if($stmtcount2->execute(array($grpId, $memId)))
                    {
                      if($stmtcount2->rowCount() == 0)
                      { 
                        $leader = 0;
                        $sqlInsert = "INSERT INTO member_group (fk_member_id, fk_group_id, leader)
                        VALUES (?,?,?)";
                        $stmtInsert = $conn->prepare($sqlInsert);
                        $stmtInsert -> bindParam(1, $memId);
                        $stmtInsert -> bindParam(2, $grpId);
                        $stmtInsert -> bindParam(3, $leader);
                        $stmtInsert->execute();
                      }
                    }
                  }
                }
              }
            }
          }

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
      $status = 'fail'; $message = 'The group name exists. Chances are the group is already added here. Confirm first.';
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
else{ 
  $resp = array('status'=>'fail','message'=>'Failed!');
  echo json_encode($resp);
  /*?>
    <script>
    //window.location.href = '../index.php';
    </script>
<?php*/ }

?>