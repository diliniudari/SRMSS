<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM schedules WHERE id=$id");
header("Location: view_schedule.php");
exit();
?>