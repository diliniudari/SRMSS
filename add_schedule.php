<?php
session_start();
include '../config/db.php';
checkRole(['admin', 'supervisor']);

$routes = mysqli_query($conn, "
    SELECT r.*, d.name as driver_name, v.registration
    FROM routes r
    LEFT JOIN drivers d ON r.driver_id = d.id
    LEFT JOIN vehicles v ON r.vehicle_id = v.id
");

$success = "";
$error = "";
$conflict = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $route_id = $_POST['route_id'];
    $departure = $_POST['departure_time'];
    $arrival = $_POST['arrival_time'];
    $status = 'on_time';

    // ---- CONFLICT DETECTION ----
    // Check if same route already has a schedule 
    // that overlaps with the new time
    $conflict_check = mysqli_query($conn, "
        SELECT s.*, r.start_point, r.end_point 
        FROM schedules s
        JOIN routes r ON s.route_id = r.id
        WHERE s.route_id = '$route_id'
        AND s.status != 'completed'
        AND (
            ('$departure' BETWEEN s.departure_time AND s.arrival_time)
            OR ('$arrival' BETWEEN s.departure_time AND s.arrival_time)
            OR (s.departure_time BETWEEN '$departure' AND '$arrival')
        )
    ");

    if (mysqli_num_rows($conflict_check) > 0) {
        $conflicting = mysqli_fetch_assoc($conflict_check);
        $conflict = "⚠️ Schedule conflict detected! This route already 
                    has a schedule from 
                    " . date('d M Y H:i', strtotime($conflicting['departure_time'])) . 
                    " to " . 
                    date('d M Y H:i', strtotime($conflicting['arrival_time'])) . ".";
    } else {
        // No conflict — save the schedule
        $query = "INSERT INTO schedules 
                  (route_id, departure_time, arrival_time, status) 
                  VALUES ('$route_id','$departure','$arrival','$status')";

        if (mysqli_query($conn, $query)) {
            $success = "Schedule added successfully!";
        } else {
            $error = "Error adding schedule!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Add Schedule</title>
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
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .btn { padding: 10px 25px; border-radius: 6px; border: none; cursor: pointer; font-size: 15px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #1a73e8; color: white; }
        .btn-secondary { background: #888; color: white; margin-left: 10px; }
        .btn:hover { opacity: 0.85; }
        .success { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .error { background: #fce4ec; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .conflict { background: #fff3e0; color: #e65100; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #e65100; font-weight: bold; }
        .info-box { background: #e3f2fd; padding: 15px; border-radius: 6px; margin-bottom: 20px; color: #1565c0; font-size: 13px; }
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
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../reports/generate_report.php">Reports</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>Add New Schedule</h1>
    <p class="subtitle">Create a timetable for a route.</p>

    <?php if ($success): ?>
        <div class="success">✅ <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error">❌ <?= $error ?></div>
    <?php endif; ?>
    <?php if ($conflict): ?>
        <div class="conflict"><?= $conflict ?></div>
    <?php endif; ?>

    <div class="info-box">
        ℹ️ The system will automatically detect 
        and prevent scheduling conflicts for the same route.
    </div>

    <div class="form-box">
        <form method="POST">
            <div class="form-group">
                <label>Select Route</label>
                <select name="route_id" required>
                    <option value="">-- Select Route --</option>
                    <?php while ($r = mysqli_fetch_assoc($routes)): ?>
                        <option value="<?= $r['id'] ?>">
                            <?= $r['start_point'] ?> → <?= $r['end_point'] ?>
                            (Driver: <?= $r['driver_name'] ?>, 
                             Vehicle: <?= $r['registration'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Departure Date & Time</label>
                <input type="datetime-local" 
                       name="departure_time" required>
            </div>
            <div class="form-group">
                <label>Arrival Date & Time</label>
                <input type="datetime-local" 
                       name="arrival_time" required>
            </div>
            <button type="submit" class="btn btn-primary">
                Add Schedule
            </button>
            <a href="view_schedule.php" class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>

</body>
</html>