<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include 'config/db.php';

$user = $_SESSION['user'];

// Get counts for stats
$total_routes = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM routes"))[0] ?? 0;
$total_drivers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM drivers"))[0] ?? 0;
$total_vehicles = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM vehicles"))[0] ?? 0;
$total_schedules = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM schedules"))[0] ?? 0;
$total_maintenance = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM maintenance_logs"))[0] ?? 0;
$total_fuel = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM fuel_logs"))[0] ?? 0;

// Trip status counts
$on_time = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM schedules WHERE status='on_time'"))[0] ?? 0;
$delayed = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM schedules WHERE status='delayed'"))[0] ?? 0;
$completed = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM schedules WHERE status='completed'"))[0] ?? 0;

// Available drivers and vehicles
$available_drivers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM drivers WHERE status='available'"))[0] ?? 0;
$available_vehicles = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM vehicles WHERE status='available'"))[0] ?? 0;
$maintenance_vehicles = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM vehicles WHERE status='maintenance'"))[0] ?? 0;

// Fuel stats
$fuel_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(cost) as total_cost, SUM(liters) as total_liters FROM fuel_logs"));

// Recent schedules
$recent_schedules = mysqli_query($conn, "
    SELECT s.*, r.start_point, r.end_point, d.name as driver_name, v.registration
    FROM schedules s
    LEFT JOIN routes r ON s.route_id = r.id
    LEFT JOIN drivers d ON r.driver_id = d.id
    LEFT JOIN vehicles v ON r.vehicle_id = v.id
    ORDER BY s.departure_time DESC
    LIMIT 5
");

// Recent drivers
$recent_drivers = mysqli_query($conn, "SELECT * FROM drivers ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1a73e8 0%, #1558b0 100%);
            min-height: 100vh;
            padding: 0;
            position: fixed;
            box-shadow: 3px 0 15px rgba(0,0,0,0.15);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            text-align: center;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .sidebar-header p {
            color: rgba(255,255,255,0.7);
            font-size: 11px;
            margin-top: 4px;
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .menu-label {
            color: rgba(255,255,255,0.5);
            font-size: 10px;
            font-weight: bold;
            padding: 10px 25px 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 12px 25px;
            font-size: 14px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left: 3px solid white;
        }

        .sidebar a.active {
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: bold;
            border-left: 3px solid white;
        }

        .sidebar .logout-section {
            position: absolute;
            bottom: 0;
            width: 100%;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding: 10px 0;
        }

        /* MAIN CONTENT */
        .main {
            margin-left: 260px;
            padding: 25px;
            width: 100%;
        }

        /* TOP BAR */
        .topbar {
            background: white;
            border-radius: 12px;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 22px;
            color: #333;
        }

        .topbar p {
            font-size: 13px;
            color: #888;
            margin-top: 3px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-badge {
            background: #1a73e8;
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .date-badge {
            background: #f0f2f5;
            color: #555;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
        }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            flex-shrink: 0;
        }

        .icon-blue { background: #e3f0ff; }
        .icon-green { background: #e8f5e9; }
        .icon-orange { background: #fff3e0; }
        .icon-purple { background: #f3e5f5; }
        .icon-red { background: #fce4ec; }
        .icon-teal { background: #e0f7fa; }

        .stat-info h3 {
            font-size: 13px;
            color: #888;
            margin-bottom: 5px;
        }

        .stat-info p {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .stat-info small {
            font-size: 11px;
            color: #aaa;
        }

        /* MIDDLE ROW */
        .middle-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .status-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            text-align: center;
        }

        .status-card h3 {
            font-size: 13px;
            color: #888;
            margin-bottom: 10px;
        }

        .status-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .status-label {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .green-text { color: #2e7d32; }
        .green-bg { background: #e8f5e9; color: #2e7d32; }
        .orange-text { color: #e65100; }
        .orange-bg { background: #fff3e0; color: #e65100; }
        .blue-text { color: #1565c0; }
        .blue-bg { background: #e3f2fd; color: #1565c0; }

        /* BOTTOM ROW */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .table-card h2 {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f2f5;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9ff;
            color: #555;
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f2f5;
            font-size: 13px;
            color: #333;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        .badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-on_time { background: #e8f5e9; color: #2e7d32; }
        .badge-delayed { background: #fff3e0; color: #e65100; }
        .badge-completed { background: #e3f2fd; color: #1565c0; }
        .badge-available { background: #e8f5e9; color: #2e7d32; }
        .badge-on_duty { background: #fff3e0; color: #e65100; }
        .badge-off { background: #fce4ec; color: #c62828; }

        /* FUEL SUMMARY */
        .fuel-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .fuel-box {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e8f0fe;
        }

        .fuel-box h3 {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }

        .fuel-box p {
            font-size: 20px;
            font-weight: bold;
            color: #1a73e8;
        }

        /* QUICK LINKS */
        .quick-links {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            margin-bottom: 25px;
        }

        .quick-links h2 {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f2f5;
        }

        .links-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
        }

        .quick-link {
            background: #f8f9ff;
            border: 1px solid #e8f0fe;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.2s;
        }

        .quick-link:hover {
            background: #1a73e8;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(26,115,232,0.3);
        }

        .quick-link .ql-icon {
            font-size: 25px;
            margin-bottom: 8px;
        }

        .quick-link p {
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
<div class="sidebar-header">
    <h2> SRMSS</h2>
    <p>Smart Route Management System</p>
</div>
<div class="sidebar-menu">
    <div class="menu-label">Main Menu</div>
    <a href="dashboard.php" class="active"> Dashboard</a>
    <a href="routes/view_routes.php"> Routes</a>
    <a href="schedule/view_schedule.php"> Schedules</a>

    <?php if ($_SESSION['user']['role'] == 'admin' || 
              $_SESSION['user']['role'] == 'supervisor'): ?>
    <div class="menu-label">Management</div>
    <a href="drivers/view_drivers.php"> Drivers</a>
    <a href="vehicles/view_vehicles.php"> Vehicles</a>
    <?php endif; ?>

    <div class="menu-label">Logs</div>
    <a href="fuel/view_fuel.php"> Fuel Logs</a>

    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
    <a href="maintenance/view_maintenance.php"> Maintenance</a>
    <?php endif; ?>

    <div class="menu-label">Analytics</div>
    <a href="reports/generate_report.php"> Reports</a>

    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
   <div class="menu-label">System</div>
   <a href="users/view_users.php"> Users</a>
  <?php endif; ?>
</div>

<div class="logout-section">
    <a href="logout.php">🚪 Logout</a>
</div>
</div>

<!-- MAIN CONTENT -->
<div class="main">

    
  <!-- TOP BAR -->
<div class="topbar">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back, <?= $user['name'] ?>! Here is your system overview.</p>
        <p style="
            background: #e3f0ff;
            color: #1a73e8;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 8px;
            display: inline-block;
        ">
           
        </p>
    </div>
    <div class="topbar-right">
        <span class="date-badge">📅 <?= date('d M Y') ?></span>
        <span class="user-badge">👤 <?= strtoupper($user['role']) ?></span>
    </div>
</div>

<!-- ACCESS DENIED MESSAGE -->
<?php if (isset($_GET['error']) && 
          $_GET['error'] == 'access_denied'): ?>
<div style="
    background: #fce4ec;
    color: #c62828;
    padding: 15px 25px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    border-left: 4px solid #c62828;
">
    ⛔ Access Denied! You do not have permission 
    to access that page.
</div>
<?php endif; ?>

 <!-- QUICK LINKS -->
<div class="quick-links">
    <h2> Quick Actions</h2>
    <div class="links-grid">
        <a href="routes/add_route.php" class="quick-link">
            <img src="ADD ROUTE.jpg" alt="Add Route"
                 style="width:55px; height:55px; object-fit:contain; margin-bottom:8px;">
            <p>Add Route</p>
        </a>
        <a href="schedule/add_schedule.php" class="quick-link">
            <img src="ADD SCHEDULEE.jpg" alt="Add Schedule"
                 style="width:55px; height:55px; object-fit:contain; margin-bottom:8px;">
            <p>Add Schedule</p>
        </a>
        <a href="drivers/add_driver.php" class="quick-link">
            <img src="ADD DRIVERRR.jpg" alt="Add Driver"
                 style="width:55px; height:55px; object-fit:contain; margin-bottom:8px;">
            <p>Add Driver</p>
        </a>
        <a href="vehicles/add_vehicle.php" class="quick-link">
            <img src="ADD VEHICLEE.jpg" alt="Add Vehicle"
                 style="width:55px; height:55px; object-fit:contain; margin-bottom:8px;">
            <p>Add Vehicle</p>
        </a>
        <a href="fuel/add_fuel.php" class="quick-link">
            <img src="ADD FUEL LOGGGG.jpg" alt="Add Fuel"
                 style="width:65px; height:60px; object-fit:contain; margin-bottom:8px;">
            <p>Add Fuel Log</p>
        </a>
        <a href="reports/generate_report.php" class="quick-link">
            <img src="GENN.png" alt="Reports"
                 style="width:55px; height:55px; object-fit:contain; margin-bottom:8px;">
            <p>View Reports</p>
        </a>
    </div>
</div>

    <!-- STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue">
			   <img src="mapp.png" alt="Routes"
             style="width:35px; height:35px; object-fit:contain;">		 
			 </div>
            <div class="stat-info">
                <h3>Total Routes</h3>
                <p><?= $total_routes ?></p>
                <small>Active routes in system</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue">
			<img src="uSe.png" alt="Drivers"
             style="width:35px; height:35px; object-fit:contain;">
			 </div>
            <div class="stat-info">
                <h3>Total Drivers</h3>
                <p><?= $total_drivers ?></p>
                <small><?= $available_drivers ?> available now</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue">
		<img src="bbu.png" alt="Vehicles"
             style="width:35px; height:35px; object-fit:contain;">
		</div>
            <div class="stat-info">
                <h3>Total Vehicles</h3>
                <p><?= $total_vehicles ?></p>
                <small><?= $available_vehicles ?> available, <?= $maintenance_vehicles ?> in maintenance</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue">
			   <img src="cala.png" alt="Schedules"
             style="width:35px; height:35px; object-fit:contain;">
			 </div>
            <div class="stat-info">
                <h3>Total Schedules</h3>
                <p><?= $total_schedules ?></p>
                <small>Scheduled trips</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue">
			 <img src="main.png" alt="Maintenance"
             style="width:35px; height:35px; object-fit:contain;">
			</div>
            <div class="stat-info">
                <h3>Maintenance Records</h3>
                <p><?= $total_maintenance ?></p>
                <small>Total maintenance logs</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-blue">
			 <img src="fufu.png" alt="Fuel"
             style="width:35px; height:35px; object-fit:contain;">
			</div>
            <div class="stat-info">
                <h3>Fuel Records</h3>
                <p><?= $total_fuel ?></p>
                <small>Rs. <?= number_format($fuel_stats['total_cost'] ?? 0, 0) ?> total cost</small>
            </div>
        </div>
    </div>

    <!-- TRIP STATUS ROW -->
    <div class="middle-row">
        <div class="status-card">
            <h3>✅ On Time Trips</h3>
            <div class="status-number green-text"><?= $on_time ?></div>
            <span class="status-label green-bg">ON TIME</span>
        </div>
        <div class="status-card">
            <h3>⚠️ Delayed Trips</h3>
            <div class="status-number orange-text"><?= $delayed ?></div>
            <span class="status-label orange-bg">DELAYED</span>
        </div>
        <div class="status-card">
            <h3>✓ Completed Trips</h3>
            <div class="status-number blue-text"><?= $completed ?></div>
            <span class="status-label blue-bg">COMPLETED</span>
        </div>
    </div>

    <!-- BOTTOM ROW - TABLES -->
    <div class="bottom-row">

        <!-- RECENT SCHEDULES -->
        <div class="table-card">
            <h2> Recent Schedules</h2>
            <table>
                <tr>
                    <th>Route</th>
                    <th>Driver</th>
                    <th>Departure</th>
                    <th>Status</th>
                </tr>
                <?php if (mysqli_num_rows($recent_schedules) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($recent_schedules)): ?>
                    <tr>
                        <td><?= $row['start_point'] ?> → <?= $row['end_point'] ?></td>
                        <td><?= $row['driver_name'] ?? 'N/A' ?></td>
                        <td><?= date('d M H:i', strtotime($row['departure_time'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] ?>">
                                <?= strtoupper($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#888; padding:20px;">
                            No schedules yet
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- RECENT DRIVERS -->
        <div class="table-card">
            <h2>Driver Status</h2>

            <!-- FUEL SUMMARY -->
            <div class="fuel-summary">
                <div class="fuel-box">
                    <h3>⛽ Total Fuel Used</h3>
                    <p><?= number_format($fuel_stats['total_liters'] ?? 0, 1) ?> L</p>
                </div>
                <div class="fuel-box">
                    <h3>💰 Total Fuel Cost</h3>
                    <p>Rs.<?= number_format($fuel_stats['total_cost'] ?? 0, 0) ?></p>
                </div>
            </div>

            <table>
                <tr>
                    <th>Driver</th>
                    <th>License</th>
                    <th>Status</th>
                </tr>
                <?php if (mysqli_num_rows($recent_drivers) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($recent_drivers)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['license_number'] ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] ?>">
                                <?= strtoupper($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; color:#888; padding:20px;">
                            No drivers yet
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

</div>

</body>
</html>