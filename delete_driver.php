<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM drivers WHERE id=$id");
header("Location: view_drivers.php");
exit();
?>