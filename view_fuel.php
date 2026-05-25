<?php
session_start();
include '../config/db.php';
checkRole(['admin', 'supervisor', 'clerk']);

$fuel_logs = mysqli_query($conn, "
    SELECT f.*, v.registration 
    FROM fuel_logs f
    LEFT JOIN vehicles v ON f.vehicle_id = v.id
    ORDER BY f.date DESC
");

// Get total fuel cost
$total = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT SUM(cost) as total, SUM(liters) as total_liters FROM fuel_logs"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Fuel Logs</title>
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
         table .btn{
         padding: 6px 12px;
         font-size: 13px;
         margin-bottom: 0;
}

        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .card .icon { font-size: 30px; margin-bottom: 8px; }
        .card h3 { font-size: 13px; color: #888; margin-bottom: 6px; }
        .card p { font-size: 26px; font-weight: bold; color: #1a73e8; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background: #1a73e8; color: white; padding: 15px; text-align: left; font-size: 14px; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2> SRMSS</h2>
    <a href="../dashboard.php"> Dashboard</a>
    <a href="../routes/view_routes.php"> Routes</a>
    <a href="../schedule/view_schedule.php"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php"> Vehicles</a>
    <a href="../fuel/view_fuel.php" class="active"> Fuel Logs</a>
    <a href="../reports/generate_report.php"> Reports</a>
	<a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1> Fuel Log Management</h1>
    <p class="subtitle">Track fuel consumption for all vehicles.</p>

    <!-- SUMMARY CARDS -->
    <div class="stats">
        <div class="card">
            <div class="icon">⛽</div>
            <h3>Total Fuel Used</h3>
            <p><?= number_format($total['total_liters'] ?? 0, 1) ?> L</p>
        </div>
        <div class="card">
            <div class="icon">💰</div>
            <h3>Total Fuel Cost</h3>
            <p>Rs. <?= number_format($total['total'] ?? 0, 2) ?></p>
        </div>
    </div>

        <?php if ($_SESSION['user']['role'] == 'admin' || 
          $_SESSION['user']['role'] == 'clerk'): ?>
       <a href="add_fuel.php" class="btn btn-primary">+ Add Fuel Log</a>
       <?php endif; ?>

    <table>
        <tr>
            <th>#</th>
            <th>Vehicle</th>
            <th>Date</th>
            <th>Liters</th>
            <th>Cost (Rs.)</th>
            <th>Notes</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($fuel_logs) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($fuel_logs)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['registration'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['liters'] ?> L</td>
                <td>Rs. <?= number_format($row['cost'], 2) ?></td>
                <td><?= $row['notes'] ?? '-' ?></td>
                <td>
    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
        <a href="delete_fuel.php?id=<?= $row['id'] ?>" 
           class="btn btn-danger"
           onclick="return confirm('Are you sure?')">
           Delete
        </a>
    <?php else: ?>
        -
    <?php endif; ?>
  </td> 
</tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align:center; padding:30px; color:#888;">
                    No fuel logs found. Add your first log!
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>