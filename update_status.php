<?php
session_start();
include '../config/db.php';
checkRole(['admin', 'supervisor']);// recreated

$id = $_GET['id'];
$status = $_GET['status'];

// Only allow valid statuses
if (in_array($status, ['on_time', 'delayed', 'completed'])) {
    mysqli_query($conn, 
        "UPDATE schedules SET status='$status' WHERE id=$id");
}

header("Location: view_schedule.php");
exit();
?>