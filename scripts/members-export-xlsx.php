<?php if(session_status()==PHP_SESSION_NONE){
session_start();} 

if($_SESSION["position"] != 1){echo 'Oops! Operation not allowed. Use the browser\'s <strong>Back</strong> to go back.'; exit;}



// CREATE PHPSPREADSHEET OBJECT
require "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// CREATE A NEW SPREADSHEET + SET METADATA
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
->setCreator('ACK ST. PeterS Cathedral - Comms')
->setLastModifiedBy('ACK ST. PeterS Cathedral - Comms')
->setTitle('Congregants')
->setSubject('ACK ST. PeterS Cathedral - Congregants')
->setDescription('Anglican Development Services, Nyeri Members.');
//->setKeywords('demo php spreadsheet')
//->setCategory('demo php file');
 
// NEW WORKSHEET
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Congregants on Comms Portal');
$sheet->setCellValue('A1','Anglican Development Services, Nyeri Members');
$sheet->setCellValue('A2','Name');
$sheet->setCellValue('B2','Phone');

try { 
  $servername = "localhost";
$username = "ofvjtniw_cathedral";
$password = "Cathedral_comms_2018";
    $conn = new PDO("mysql:host=$servername;dbname=ofvjtniw_cathedral", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM members"; 
       $stmt = $conn->prepare($sql);
       if($stmt->execute())
       {
        $i = 3;
        while ($row = $stmt->fetch(PDO::FETCH_NAMED)) {
        $sheet->setCellValue('A'.$i, $row['fname']/*.' '.$row['lname']*/);
          $sheet->setCellValue('B'.$i, $row['phone']);
          $i++;
        }
       } 
     }
   catch(PDOException $e)
       {
       echo "<p class='alert alert-warning' style='text-align: center;'> Error: Operation failed quietly!".  $e->getMessage()." <br>Please try again or contact the admin...</p>";
       }



// OUTPUT
$writer = new Xlsx($spreadsheet);

// THIS WILL SAVE TO A FILE ON THE SERVER
// $writer->save('test.xlsx');

// OR FORCE DOWNLOAD
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ACK St. Peters Cathedral Congregants (Comms Portal).xlsx"');
header('Cache-Control: max-age=0');
//header('Expires: Fri, 11 Nov 2011 11:11:11 GMT');
//header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');
$writer->save('php://output');
?>