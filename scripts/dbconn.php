<?php
$servername = "localhost";
$username = "root";//ofvjtniw_adsnyeri
$password = "";//"ADSnyeri-2020";


try {
    $conn = new PDO("mysql:host=$servername;dbname=adsnyeri", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
catch(PDOException $e)
    {
    echo "<br>" . $e->getMessage();
    }
	
	
//$conn->query("use adsnyeri");
?> 