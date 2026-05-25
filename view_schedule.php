<?php
session_start();
include '../config/db.php';
checkRole(['admin', 'supervisor', 'clerk']); /// recreated

$schedules = mysqli_query($conn, "
    SELECT s.*, r.start_point, r.end_point, 
           d.name as driver_name, v.registration
    FROM schedules s
    LEFT JOIN routes r ON s.route_id = r.id
    LEFT JOIN drivers d ON r.driver_id = d.id
    LEFT JOIN vehicles v ON r.vehicle_id = v.id
    ORDER BY s.departure_time ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Schedules</title>
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
        .btn-success { background: #2e7d32; color: white; }
        .btn-warning { background: #f0a500; color: white; }
        .btn:hover { opacity: 0.85; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background: #1a73e8; color: white; padding: 15px; text-align: left; font-size: 14px; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        tr:hover { background: #f9f9f9; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-on_time { background: #e8f5e9; color: #2e7d32; }
        .badge-delayed { background: #fff3e0; color: #e65100; }
        .badge-completed { background: #e3f2fd; color: #1565c0; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2> SRMSS</h2>
    <a href="../dashboard.php"> Dashboard</a>
    <a href="../routes/view_routes.php"> Routes</a>
    <a href="../schedule/view_schedule.php" class="active"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php"> Vehicles</a>
    <a href="../fuel/view_fuel.php">Fuel Logs</a>
    <a href="../reports/generate_report.php"> Reports</a>
	<a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>Schedule Management</h1>
    <p class="subtitle">Manage all route timetables here.</p>
 
   <?php if ($_SESSION['user']['role'] == 'admin' || 
          $_SESSION['user']['role'] == 'supervisor'): ?>
<a href="add_schedule.php" class="btn btn-primary">+ Add New Schedule</a>
<?php endif; ?>    

    <table>
        <tr>
            <th></th>
            <th>Route</th>
            <th>Driver</th>
            <th>Vehicle</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($schedules) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($schedules)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td>
                    <?= $row['start_point'] ?> 
                    → 
                    <?= $row['end_point'] ?>
                </td>
                <td><?= $row['driver_name'] ?? 'N/A' ?></td>
                <td><?= $row['registration'] ?? 'N/A' ?></td>
                <td><?= date('d M Y H:i', strtotime($row['departure_time'])) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['arrival_time'])) ?></td>
                <td>
                    <span class="badge badge-<?= $row['status'] ?>">
                        <?= strtoupper($row['status']) ?>
                    </span>
                </td>
                <td>
           <?php if ($_SESSION['user']['role'] == 'admin' || 
              $_SESSION['user']['role'] == 'supervisor'): ?>
              <a href="update_status.php?id=<?= $row['id'] ?>&status=completed" 
              class="btn btn-success"
              onclick="return confirm('Mark as completed?')">✓ Done</a>
             <a href="update_status.php?id=<?= $row['id'] ?>&status=delayed" 
             class="btn btn-warning">⚠ Delay</a>
             <?php endif; ?>
             <?php if ($_SESSION['user']['role'] == 'admin'): ?>
             <a href="delete_schedule.php?id=<?= $row['id'] ?>" 
            class="btn btn-danger"
            onclick="return confirm('Are you sure?')">Delete</a>
          <?php endif; ?>
</td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:30px; color:#888;">
                    No schedules found. Add your first schedule!
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>