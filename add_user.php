<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {
        $query = "INSERT INTO users (name, email, password, role) 
                  VALUES ('$name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = "User added successfully!";
        } else {
            $error = "Error adding user!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Add User</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; display: flex; }
        .sidebar { width: 250px; background: linear-gradient(180deg, #1a73e8 0%, #1558b0 100%); min-height: 100vh; padding: 20px 0; position: fixed; }
        .sidebar h2 { color: white; text-align: center; padding: 10px 0 20px; font-size: 22px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 15px 25px; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .sidebar a.active { background: rgba(255,255,255,0.3); font-weight: bold; }
        .sidebar .logout { position: absolute; bottom: 20px; width: 100%; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px; }
        .main { margin-left: 250px; padding: 30px; width: 100%; }
        .main h1 { color: #333; margin-bottom: 5px; }
        .main p.subtitle { color: #888; margin-bottom: 25px; }
        .form-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 14px; color: #555; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .btn { padding: 10px 25px; border-radius: 6px; border: none; cursor: pointer; font-size: 15px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #1a73e8; color: white; }
        .btn-secondary { background: #888; color: white; margin-left: 10px; }
        .btn:hover { opacity: 0.85; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .error { background: #fce4ec; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .info-box { background: #e3f2fd; color: #1565c0; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SRMSS</h2>
    <a href="../dashboard.php"> Dashboard</a>
    <a href="../routes/view_routes.php"> Routes</a>
    <a href="../schedule/view_schedule.php"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php"> Vehicles</a>
    <a href="../fuel/view_fuel.php">Fuel Logs</a>
    <a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <a href="../reports/generate_report.php"> Reports</a>
    <a href="../users/view_users.php" class="active">👥 Users</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>👥 Add New User</h1>
    <p class="subtitle">Create a new system user account.</p>

    <?php if ($success): ?>
        <div class="success">✅ <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error">❌ <?= $error ?></div>
    <?php endif; ?>

    <div class="info-box">
        ℹ️ Role Permissions:
        <strong>Admin</strong> — Full access |
        <strong>Supervisor</strong> — Schedules + Reports |
        <strong>Clerk</strong> — Fuel Logs + Reports
    </div>

    <div class="form-box">
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" 
                       placeholder="Enter full name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" 
                       placeholder="Enter email address" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" 
                       placeholder="Enter password" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin">Admin</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="clerk">Clerk</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add User</button>
            <a href="view_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>