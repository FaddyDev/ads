<?php
session_start();
unset($_SESSION['is_logged']);
unset($_SESSION['phone']);
unset($_SESSION['fname']);
unset($_SESSION['position']);
unset($_SESSION['id']);
header('Location:../index.php');
?>