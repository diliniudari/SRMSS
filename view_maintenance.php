<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$logs = mysqli_query($conn, "
    SELECT m.*, v.registration 
    FROM maintenance_logs m
    LEFT JOIN vehicles v ON m.vehicle_id = v.id
    ORDER BY m.date DESC
");

$total_cost = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT SUM(cost) as total FROM maintenance_logs"));
$total_logs = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM maintenance_logs"))[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Maintenance Logs</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; display: flex; }
        .sidebar { width: 250px; background: #1a73e8; min-height: 100vh; padding: 20px 0; position: fixed; }
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
        .btn-danger { background: #e53935; color: white; }
        .btn:hover { opacity: 0.85; }
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 25px; }
        .card { background: white; border-radius: 10px; padding: 20px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .card .icon { font-size: 30px; margin-bottom: 8px; }
        .card h3 { font-size: 13px; color: #888; margin-bottom: 6px; }
        .card p { font-size: 26px; font-weight: bold; color: #1a73e8; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background: #1a73e8; color: white; padding: 15px; text-align: left; font-size: 14px; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        tr:hover { background: #f9f9f9; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-routine { background: #e8f5e9; color: #2e7d32; }
        .badge-corrective { background: #fff3e0; color: #e65100; }
        .badge-emergency { background: #fce4ec; color: #c62828; }
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
    <a href="../reports/generate_report.php">Reports</a>
	<a href="../maintenance/view_maintenance.php" class="active"> Maintenance</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1> Maintenance Log</h1>
    <p class="subtitle">Track vehicle maintenance activities here.</p>

    <div class="stats">
        <div class="card">
            <div class="icon">🔧</div>
            <h3>Total Maintenance Records</h3>
            <p><?= $total_logs ?></p>
        </div>
        <div class="card">
            <div class="icon">💰</div>
            <h3>Total Maintenance Cost</h3>
            <p>Rs. <?= number_format($total_cost['total'] ?? 0, 2) ?></p>
        </div>
    </div>

    <a href="add_maintenance.php" class="btn btn-primary">+ Add Maintenance Log</a>

    <table>
        <tr>
            <th>#</th>
            <th>Vehicle</th>
            <th>Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Cost (Rs.)</th>
            <th>Next Service</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($logs) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($logs)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['registration'] ?></td>
                <td><?= $row['date'] ?></td>
                <td>
                    <span class="badge badge-<?= $row['type'] ?>">
                        <?= strtoupper($row['type']) ?>
                    </span>
                </td>
                <td><?= $row['description'] ?></td>
                <td>Rs. <?= number_format($row['cost'], 2) ?></td>
                <td><?= $row['next_service_date'] ?></td>
                <td>
                    <a href="delete_maintenance.php?id=<?= $row['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:30px; color:#888;">
                    No maintenance logs found. Add your first log!
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>