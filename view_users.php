<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
$total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - User Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; display: flex; }
        .sidebar { width: 250px; background: linear-gradient(180deg, #1a73e8 0%, #1558b0 100%); min-height: 100vh; padding: 20px 0; position: fixed; box-shadow: 3px 0 15px rgba(0,0,0,0.15); }
        .sidebar h2 { color: white; text-align: center; padding: 10px 0 20px; font-size: 22px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .sidebar a { display: block; color: white; text-decoration: none; padding: 15px 25px; font-size: 15px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .sidebar a.active { background: rgba(255,255,255,0.3); font-weight: bold; }
        .sidebar .logout { position: absolute; bottom: 20px; width: 100%; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px; }
        .main { margin-left: 250px; padding: 30px; width: 100%; }
        .main h1 { color: #333; margin-bottom: 5px; }
        .main p.subtitle { color: #888; margin-bottom: 25px; }
        .btn { display: inline-block; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 14px; cursor: pointer; border: none; margin-bottom: 20px; }
        .btn-primary { background: #1a73e8; color: white; }
        .btn-warning { background: #f0a500; color: white; }
        .btn-danger { background: #e53935; color: white; }
        .btn:hover { opacity: 0.85; }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stat-box .icon { font-size: 30px; margin-bottom: 8px; }
        .stat-box h3 { font-size: 12px; color: #888; margin-bottom: 5px; }
        .stat-box p { font-size: 24px; font-weight: bold; color: #1a73e8; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background: #1a73e8; color: white; padding: 15px; text-align: left; font-size: 14px; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        tr:hover { background: #f9f9f9; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-admin { background: #e3f0ff; color: #1a73e8; }
        .badge-supervisor { background: #e8f5e9; color: #2e7d32; }
        .badge-clerk { background: #fff3e0; color: #e65100; }
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
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <a href="../reports/generate_report.php"> Reports</a>
    <a href="../users/view_users.php" class="active"> Users</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>User Management</h1>
    <p class="subtitle">Manage all system users here.</p>

   <!-- STATS -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="icon">
                <img src="../uSER.png" alt="Total Users"
                     style="width:55px; height:55px; object-fit:contain;">
            </div>
            <h3>Total Users</h3>
            <p><?= $total_users ?></p>
        </div>
        <div class="stat-box">
            <div class="icon">
                <img src="../ADmin.png" alt="Admins"
                     style="width:55px; height:55px; object-fit:contain;">
            </div>
            <h3>Admins</h3>
            <p><?= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='admin'"))[0] ?></p>
        </div>
        <div class="stat-box">
            <div class="icon">
                <img src="../SUPER.png" alt="Supervisors"
                     style="width:70px; height:55px; object-fit:contain;">
            </div>
            <h3>Supervisors</h3>
            <p><?= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='supervisor'"))[0] ?></p>
        </div>
        <div class="stat-box">
            <div class="icon">
                <img src="../uSER.png" alt="Clerks"
                     style="width:55px; height:55px; object-fit:contain;">
            </div>
            <h3>Clerks</h3>
            <p><?= mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='clerk'"))[0] ?></p>
        </div>
    </div>

    <a href="add_user.php" class="btn btn-primary">+ Add New User</a>

    <table>
        <tr>
            <th></th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($users) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <strong><?= $row['name'] ?></strong>
                </td>
                <td><?= $row['email'] ?></td>
                <td>
                    <span class="badge badge-<?= $row['role'] ?>">
                        <?= strtoupper($row['role']) ?>
                    </span>
                </td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" 
                       class="btn btn-warning">Edit</a>
                    <?php if ($row['id'] != $_SESSION['user']['id']): ?>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center; padding:30px; color:#888;">
                    No users found!
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>