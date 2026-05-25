<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM maintenance_logs WHERE id=$id");
header("Location: view_maintenance.php");
exit();
?>