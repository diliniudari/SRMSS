<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM vehicles WHERE id=$id");
header("Location: view_vehicles.php");
exit();
?>