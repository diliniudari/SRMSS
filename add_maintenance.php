<?php
session_start();
include '../config/db.php';
checkRole(['admin']);

$vehicles = mysqli_query($conn, "SELECT * FROM vehicles");
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $date = $_POST['date'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $next_service = $_POST['next_service_date'];

    $query = "INSERT INTO maintenance_logs 
              (vehicle_id, date, type, description, cost, next_service_date) 
              VALUES ('$vehicle_id','$date','$type','$description','$cost','$next_service')";

    if (mysqli_query($conn, $query)) {
        // Update vehicle status if emergency
        if ($type == 'emergency' || $type == 'corrective') {
            mysqli_query($conn, 
                "UPDATE vehicles SET status='maintenance' 
                 WHERE id=$vehicle_id");
        }
        $success = "Maintenance log added successfully!";
    } else {
        $error = "Error adding maintenance log!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Add Maintenance</title>
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
        .warning-box { background: #fff3e0; color: #e65100; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SRMSS</h2>
    <a href="../dashboard.php">Dashboard</a>
    <a href="../routes/view_routes.php"> Routes</a>
    <a href="../schedule/view_schedule.php"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php"> Vehicles</a>
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../maintenance/view_maintenance.php" class="active"> Maintenance</a>
    <a href="../reports/generate_report.php">Reports</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>🔧 Add Maintenance Log</h1>
    <p class="subtitle">Record a maintenance activity for a vehicle.</p>

    <?php if ($success): ?>
        <div class="success">✅ <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error">❌ <?= $error ?></div>
    <?php endif; ?>

    <div class="warning-box">
        ⚠️ Note: If maintenance type is Corrective or Emergency, 
        the vehicle status will automatically be set to "Maintenance".
    </div>

    <div class="form-box">
        <form method="POST">
            <div class="form-group">
                <label>Select Vehicle</label>
                <select name="vehicle_id" required>
                    <option value="">-- Select Vehicle --</option>
                    <?php while ($v = mysqli_fetch_assoc($vehicles)): ?>
                        <option value="<?= $v['id'] ?>">
                            <?= $v['registration'] ?> 
                            (<?= $v['capacity'] ?> seats)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" 
                       value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
                <label>Maintenance Type</label>
                <select name="type" required>
                    <option value="routine">Routine Service</option>
                    <option value="corrective">Corrective Maintenance</option>
                    <option value="emergency">Emergency Repair</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" 
                          placeholder="e.g. Oil change, brake pad replacement..."
                          required></textarea>
            </div>
            <div class="form-group">
                <label>Cost (Rs.)</label>
                <input type="number" name="cost" 
                       step="0.01"
                       placeholder="e.g. 5000" required>
            </div>
            <div class="form-group">
                <label>Next Service Date</label>
                <input type="date" name="next_service_date" required>
            </div>
            <button type="submit" class="btn btn-primary">
                Add Maintenance Log
            </button>
            <a href="view_maintenance.php" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>

</body>
</html>