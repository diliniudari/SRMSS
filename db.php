<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "srmss_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Role check function
function checkRole($allowed_roles) {
    if (!isset($_SESSION['user'])) {
        header("Location: ../index.php");
        exit();
    }
    $user_role = $_SESSION['user']['role'];
    if (!in_array($user_role, $allowed_roles)) {
        header("Location: ../dashboard.php?error=access_denied");
        exit();
    }
}
?>