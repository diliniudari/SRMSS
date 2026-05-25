<?php
session_start();
include '../config/db.php';
checkRole(['admin']);  //// recreate


$id = $_GET['id'];
$route = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM routes WHERE id=$id"));
$drivers = mysqli_query($conn, "SELECT * FROM drivers");
$vehicles = mysqli_query($conn, "SELECT * FROM vehicles");

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start = $_POST['start_point'];
    $end = $_POST['end_point'];
    $stops = $_POST['stops'];
    $distance = $_POST['distance'];
    $driver_id = $_POST['driver_id'];
    $vehicle_id = $_POST['vehicle_id'];

    $query = "UPDATE routes SET 
              start_point='$start',
              end_point='$end',
              stops='$stops',
              distance='$distance',
              driver_id='$driver_id',
              vehicle_id='$vehicle_id'
              WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        $success = "Route updated successfully!";
        $route = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM routes WHERE id=$id"));
    } else {
        $error = "Error updating route!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Edit Route</title>
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
        .form-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 14px; color: #555; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .form-group textarea { height: 80px; resize: vertical; }
        .btn { padding: 10px 25px; border-radius: 6px; border: none; cursor: pointer; font-size: 15px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #1a73e8; color: white; }
        .btn-secondary { background: #888; color: white; margin-left: 10px; }
        .btn:hover { opacity: 0.85; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .error { background: #fce4ec; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2> SRMSS</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../routes/view_routes.php" class="active"> Routes</a>
    <a href="../schedule/view_schedule.php"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php"> Vehicles</a>
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../reports/generate_report.php">Reports</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>✏️ Edit Route</h1>
    <p class="subtitle">Update the route details below.</p>

    <?php if ($success): ?>
        <div class="success">✅ <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error">❌ <?= $error ?></div>
    <?php endif; ?>

    <div class="form-box">
        <form method="POST">
            <div class="form-group">
                <label>Start Point</label>
                <input type="text" name="start_point" 
                       value="<?= $route['start_point'] ?>" required>
            </div>
            <div class="form-group">
                <label>End Point</label>
                <input type="text" name="end_point" 
                       value="<?= $route['end_point'] ?>" required>
            </div>
            <div class="form-group">
                <label>Intermediate Stops</label>
                <textarea name="stops"><?= $route['stops'] ?></textarea>
            </div>
            <div class="form-group">
                <label>Total Distance (km)</label>
                <input type="number" name="distance" 
                       value="<?= $route['distance'] ?>" required>
            </div>
            <div class="form-group">
                <label>Assign Driver</label>
                <select name="driver_id" required>
                    <?php while ($d = mysqli_fetch_assoc($drivers)): ?>
                        <option value="<?= $d['id'] ?>" 
                            <?= $route['driver_id']==$d['id']?'selected':'' ?>>
                            <?= $d['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Assign Vehicle</label>
                <select name="vehicle_id" required>
                    <?php while ($v = mysqli_fetch_assoc($vehicles)): ?>
                        <option value="<?= $v['id'] ?>" 
                            <?= $route['vehicle_id']==$v['id']?'selected':'' ?>>
                            <?= $v['registration'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Route</button>
            <a href="view_routes.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>