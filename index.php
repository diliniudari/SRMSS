<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

include 'config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #1a73e8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            width: 380px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 5px;
            color: #1a73e8;
        }
        .login-box p.subtitle {
            text-align: center;
            color: #888;
            margin-bottom: 25px;
            font-size: 13px;
        }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .login-box button {
            width: 100%;
            padding: 12px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .login-box button:hover { background: #1558b0; }
        .error {
            background: #ffe0e0;
            color: #cc0000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>SRMSS</h2>
    <p class="subtitle">Smart Route Management & Scheduling System</p>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" 
               placeholder="Email Address" required>
        <input type="password" name="password" 
               placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>