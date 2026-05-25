<?php
session_start();
include '../config/db.php';
checkRole(['admin', 'supervisor']);

$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $vehicles = mysqli_query($conn, "SELECT * FROM vehicles WHERE registration LIKE '%$search%' OR status LIKE '%$search%'");
} else {
    $vehicles = mysqli_query($conn, "SELECT * FROM vehicles");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Vehicles</title>
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
        .btn-warning { background: #f0a500; color: white; }
        .btn-danger { background: #e53935; color: white; }
        .btn:hover { opacity: 0.85; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background: #1a73e8; color: white; padding: 15px; text-align: left; font-size: 14px; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 14px; color: #333; }
        tr:hover { background: #f9f9f9; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-available { background: #e8f5e9; color: #2e7d32; }
        .badge-on_route { background: #fff3e0; color: #e65100; }
        .badge-maintenance { background: #fce4ec; color: #c62828; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2> SRMSS</h2>
    <a href="../dashboard.php"> Dashboard</a>
    <a href="../routes/view_routes.php"> Routes</a>
    <a href="../schedule/view_schedule.php"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php" class="active"> Vehicles</a>
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../reports/generate_report.php"> Reports</a>
	<a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>Vehicle Management</h1>
    <p class="subtitle">Manage all depot vehicles here.</p>

   <?php if ($_SESSION['user']['role'] == 'admin'): ?>
<a href="add_vehicle.php" class="btn btn-primary">+ Add New Vehicle</a>
<?php endif; ?>

<!-- SEARCH BOX -->
<form method="GET" style="display:inline-block; margin-bottom:20px;">
    <div style="display:flex; gap:10px; align-items:center;">
        <input type="text" 
               name="search" 
               value="<?= $search ?>"
               placeholder="Search by registration or status..."
               style="padding:10px 15px; border:1px solid #ddd; border-radius:6px; font-size:14px; width:350px;">
        <button type="submit" 
                style="padding:10px 20px; background:#1a73e8; color:white; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
            🔍 Search
        </button>
        <?php if ($search): ?>
        <a href="view_vehicles.php" 
           style="padding:10px 20px; background:#888; color:white; border-radius:6px; text-decoration:none; font-size:14px;">
            ✕ Clear
        </a>
        <?php endif; ?>
    </div>
</form>

<?php if ($search): ?>
<p style="color:#888; font-size:13px; margin-bottom:15px;">
    Search results for: <strong>"<?= $search ?>"</strong>
</p>
<?php endif; ?>

    <table>
        <tr>
            <th>#</th>
            <th>Registration</th>
            <th>Capacity</th>
            <th>Mileage (km)</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php if (mysqli_num_rows($vehicles) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($vehicles)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['registration'] ?></td>
                <td><?= $row['capacity'] ?> seats</td>
                <td><?= $row['mileage'] ?> km</td>
                <td>
                    <span class="badge badge-<?= $row['status'] ?>">
                        <?= strtoupper($row['status']) ?>
                    </span>
                </td>
            
			<td>
    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
    <a href="edit_vehicle.php?id=<?= $row['id'] ?>" 
       class="btn btn-warning">Edit</a>
    <a href="delete_vehicle.php?id=<?= $row['id'] ?>" 
       class="btn btn-danger"
       onclick="return confirm('Are you sure?')">Delete</a>
    <?php endif; ?>
       </td>
		  
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center; padding:30px; color:#888;">
                    No vehicles found. Add your first vehicle!
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>