<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$id = $_GET['id'];

// Prevent deleting yourself
if ($id == $_SESSION['user']['id']) {
    header("Location: view_users.php?error=cannot_delete_self");
    exit();
}

mysqli_query($conn, "DELETE FROM users WHERE id=$id");
header("Location: view_users.php");
exit();
?>