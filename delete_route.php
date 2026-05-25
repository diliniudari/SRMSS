<?php
session_start();
include '../config/db.php';
checkRole(['admin']); //// recreted


$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM routes WHERE id=$id");
header("Location: view_routes.php");
exit();
?>