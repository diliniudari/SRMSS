<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}
include '../config/db.php';

// Get all stats
$total_routes = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM routes"))[0];
$total_drivers = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM drivers"))[0];
$total_vehicles = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM vehicles"))[0];
$total_schedules = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM schedules"))[0];
$completed = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM schedules WHERE status='completed'"))[0];
$delayed = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM schedules WHERE status='delayed'"))[0];
$on_time = mysqli_fetch_row(mysqli_query($conn, 
    "SELECT COUNT(*) FROM schedules WHERE status='on_time'"))[0];

// Fuel stats
$fuel = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT SUM(liters) as total_liters, 
            SUM(cost) as total_cost 
     FROM fuel_logs"));

// Get recent schedules
$schedules = mysqli_query($conn, "
    SELECT s.*, r.start_point, r.end_point,
           d.name as driver_name, v.registration
    FROM schedules s
    LEFT JOIN routes r ON s.route_id = r.id
    LEFT JOIN drivers d ON r.driver_id = d.id
    LEFT JOIN vehicles v ON r.vehicle_id = v.id
    ORDER BY s.departure_time DESC
    LIMIT 10
");

// Get recent fuel logs
$fuel_logs = mysqli_query($conn, "
    SELECT f.*, v.registration
    FROM fuel_logs f
    LEFT JOIN vehicles v ON f.vehicle_id = v.id
    ORDER BY f.date DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Reports</title>
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

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .card .icon { font-size: 28px; margin-bottom: 8px; }
        .card h3 { font-size: 12px; color: #888; margin-bottom: 6px; }
        .card p { font-size: 24px; font-weight: bold; color: #1a73e8; }

        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        .section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            font-size: 18px;
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f4f6f9; color: #555; padding: 12px; text-align: left; font-size: 13px; }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 13px; color: #333; }
        tr:hover { background: #f9f9f9; }

        .badge { padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-on_time { background: #e8f5e9; color: #2e7d32; }
        .badge-delayed { background: #fff3e0; color: #e65100; }
        .badge-completed { background: #e3f2fd; color: #1565c0; }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            cursor: pointer;
            border: none;
            margin-bottom: 25px;
            font-weight: bold;
        }
        .btn-pdf { background: #e53935; color: white; }
        .btn-pdf:hover { opacity: 0.85; }

        .trip-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .trip-card {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .trip-card h3 { font-size: 12px; margin-bottom: 5px; }
        .trip-card p { font-size: 22px; font-weight: bold; }
        .green { background: #e8f5e9; color: #2e7d32; }
        .orange { background: #fff3e0; color: #e65100; }
        .blue { background: #e3f2fd; color: #1565c0; }
		
		
		.icon img{
    width: 50px;
    height: 50px;
    object-fit: contain;
    margin-bottom: 10px;
}
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
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../reports/generate_report.php" class="active"> Reports</a>
	<a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1> Reports & Analytics</h1>
    <p class="subtitle">System overview and performance reports.</p>

    <!-- PDF Download Button -->
    <a href="download_report.php" class="btn btn-pdf">
        📄 Download PDF Report
    </a>

 <!-- OVERVIEW STATS -->
<div class="stats">
    <div class="card">
        <div class="icon">
            <img src="../mapp.png" alt="Routes"
                 style="width:50px; height:55px; object-fit:contain; margin-bottom:8px;">
        </div>
        <h3>Total Routes</h3>
        <p><?= $total_routes ?></p>
    </div>
    <div class="card">
        <div class="icon">
            <img src="../uSe.png" alt="Drivers"
                 style="width:55px; height:55px; object-fit:contain; margin-bottom:8px;">
        </div>
        <h3>Total Drivers</h3>
        <p><?= $total_drivers ?></p>
    </div>
    <div class="card">
        <div class="icon">
            <img src="../bbu.png" alt="Vehicles"
                 style="width:65px; height:55px; object-fit:contain; margin-bottom:8px;">
        </div>
        <h3>Total Vehicles</h3>
        <p><?= $total_vehicles ?></p>
    </div>
    <div class="card">
        <div class="icon">
            <img src="../cala.png" alt="Schedules"
                 style="width:60px; height:55px; object-fit:contain; margin-bottom:8px;">
        </div>
        <h3>Total Schedules</h3>
        <p><?= $total_schedules ?></p>
    </div>
</div>
    <!-- TRIP STATUS -->
    <div class="section">
        <h2>📅 Trip Status Overview</h2>
        <div class="trip-stats">
            <div class="trip-card green">
                <h3>✅ On Time</h3>
                <p><?= $on_time ?></p>
            </div>
            <div class="trip-card orange">
                <h3>⚠️ Delayed</h3>
                <p><?= $delayed ?></p>
            </div>
            <div class="trip-card blue">
                <h3>✓ Completed</h3>
                <p><?= $completed ?></p>
            </div>
        </div>

        <!-- Recent Schedules Table -->
        <table>
            <tr>
                <th>Route</th>
                <th>Driver</th>
                <th>Vehicle</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Status</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($schedules)): ?>
            <tr>
                <td><?= $row['start_point'] ?> → <?= $row['end_point'] ?></td>
                <td><?= $row['driver_name'] ?></td>
                <td><?= $row['registration'] ?></td>
                <td><?= date('d M Y H:i', strtotime($row['departure_time'])) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['arrival_time'])) ?></td>
                <td>
                    <span class="badge badge-<?= $row['status'] ?>">
                        <?= strtoupper($row['status']) ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- FUEL REPORT -->
    <div class="section">
        <h2>⛽ Fuel Consumption Report</h2>
        <div class="trip-stats">
            <div class="trip-card blue">
                <h3>Total Liters</h3>
                <p><?= number_format($fuel['total_liters'] ?? 0, 1) ?> L</p>
            </div>
            <div class="trip-card green">
                <h3>Total Cost</h3>
                <p>Rs.<?= number_format($fuel['total_cost'] ?? 0, 0) ?></p>
            </div>
        </div>

        <table>
            <tr>
                <th>Vehicle</th>
                <th>Date</th>
                <th>Liters</th>
                <th>Cost (Rs.)</th>
                <th>Notes</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($fuel_logs)): ?>
            <tr>
                <td><?= $row['registration'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['liters'] ?> L</td>
                <td>Rs. <?= number_format($row['cost'], 2) ?></td>
                <td><?= $row['notes'] ?? '-' ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

</body>
</html>