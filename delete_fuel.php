<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM fuel_logs WHERE id=$id");
header("Location: view_fuel.php");
exit();
?>