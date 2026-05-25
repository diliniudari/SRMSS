<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}
include '../config/db.php';

$id = $_GET['id'];
$route = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT r.*, d.name as driver_name, v.registration
    FROM routes r
    LEFT JOIN drivers d ON r.driver_id = d.id
    LEFT JOIN vehicles v ON r.vehicle_id = v.id
    WHERE r.id = $id
"));

// Sri Lanka city coordinates
$cities = [
    'Colombo'       => [6.9271, 79.8612],
    'Kandy'         => [7.2906, 80.6337],
    'Galle'         => [6.0535, 80.2210],
    'Jaffna'        => [9.6615, 80.0255],
    'Matara'        => [5.9485, 80.5353],
    'Badulla'       => [6.9934, 81.0550],
    'Negombo'       => [7.2083, 79.8358],
    'Anuradhapura'  => [8.3114, 80.4037],
    'Trincomalee'   => [8.5874, 81.2152],
    'Batticaloa'    => [7.7170, 81.6924],
    'Ratnapura'     => [6.6828, 80.3992],
    'Kurunegala'    => [7.4863, 80.3647],
    'Dambulla'      => [7.8742, 80.6511],
    'Nuwara Eliya'  => [6.9497, 80.7891],
    'Kalutara'      => [6.5854, 79.9607],
    'Kadawatha'     => [7.0028, 79.9492],
    'Nittambuwa'    => [7.0486, 80.0836],
    'Kegalle'       => [7.2513, 80.3464],
    'Aluthgama'     => [6.4269, 80.0000],
    'Hikkaduwa'     => [6.1395, 80.1054],
    'Vavuniya'      => [8.7514, 80.4971],
    'Panadura'      => [6.7131, 79.9053],
    'Bandarawela'   => [6.8297, 80.9900],
];

// Get coordinates for start and end
$start = $route['start_point'];
$end = $route['end_point'];

$start_lat = $cities[$start][0] ?? 7.8731;
$start_lng = $cities[$start][1] ?? 80.7718;
$end_lat = $cities[$end][0] ?? 7.8731;
$end_lng = $cities[$end][1] ?? 80.7718;

// Get stops coordinates
$stops = explode(',', $route['stops']);
$stop_coords = [];
foreach ($stops as $stop) {
    $stop = trim($stop);
    if (isset($cities[$stop])) {
        $stop_coords[] = [
            'name' => $stop,
            'lat'  => $cities[$stop][0],
            'lng'  => $cities[$stop][1]
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>SRMSS - Route Map</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" 
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    
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

        .route-info {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .info-item h3 {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }

        .info-item p {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        #map {
            height: 500px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            z-index: 1;
        }

        .stops-list {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-top: 20px;
        }

        .stops-list h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .stop-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .stop-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot-start { background: #2e7d32; }
        .dot-stop { background: #1a73e8; }
        .dot-end { background: #e53935; }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .btn-secondary { background: #888; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>SRMSS</h2>
    <a href="../dashboard.php"> Dashboard</a>
    <a href="../routes/view_routes.php" class="active">Routes</a>
    <a href="../schedule/view_schedule.php"> Schedules</a>
    <a href="../drivers/view_drivers.php"> Drivers</a>
    <a href="../vehicles/view_vehicles.php"> Vehicles</a>
    <a href="../fuel/view_fuel.php"> Fuel Logs</a>
    <a href="../maintenance/view_maintenance.php"> Maintenance</a>
    <a href="../reports/generate_report.php"> Reports</a>
    <div class="logout">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</div>

<div class="main">
    <h1>🗺️ Route Map</h1>
    <p class="subtitle">
        Visual map for route: 
        <strong><?= $route['start_point'] ?> → <?= $route['end_point'] ?></strong>
    </p>

    <a href="view_routes.php" class="btn btn-secondary">← Back to Routes</a>

    <!-- ROUTE INFO -->
    <div class="route-info">
        <div class="info-item">
            <h3>Start Point</h3>
            <p>📍 <?= $route['start_point'] ?></p>
        </div>
        <div class="info-item">
            <h3>End Point</h3>
            <p>🏁 <?= $route['end_point'] ?></p>
        </div>
        <div class="info-item">
            <h3>Distance</h3>
            <p>📏 <?= $route['distance'] ?> km</p>
        </div>
        <div class="info-item">
            <h3>Driver</h3>
            <p>👨‍✈️ <?= $route['driver_name'] ?? 'N/A' ?></p>
        </div>
        <div class="info-item">
            <h3>Vehicle</h3>
            <p>🚌 <?= $route['registration'] ?? 'N/A' ?></p>
        </div>
        <div class="info-item">
            <h3>Stops</h3>
            <p>🛑 <?= $route['stops'] ?></p>
        </div>
    </div>

    <!-- MAP -->
    <div id="map"></div>

    <!-- STOPS LIST -->
    <div class="stops-list">
        <h2>🛣️ Route Stops</h2>

        <!-- Start -->
        <div class="stop-item">
            <div class="stop-dot dot-start"></div>
            <div>
                <strong><?= $route['start_point'] ?></strong>
                <span style="color:#888; font-size:12px; margin-left:10px;">
                    Starting Point
                </span>
            </div>
        </div>

        <!-- Intermediate stops -->
        <?php foreach ($stop_coords as $stop): ?>
        <div class="stop-item">
            <div class="stop-dot dot-stop"></div>
            <div>
                <strong><?= $stop['name'] ?></strong>
                <span style="color:#888; font-size:12px; margin-left:10px;">
                    Intermediate Stop
                </span>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- End -->
        <div class="stop-item">
            <div class="stop-dot dot-end"></div>
            <div>
                <strong><?= $route['end_point'] ?></strong>
                <span style="color:#888; font-size:12px; margin-left:10px;">
                    Final Destination
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize map centered on Sri Lanka
    var map = L.map('map').setView([7.8731, 80.7718], 8);

    // Add OpenStreetMap tiles (FREE - no API key needed)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Custom icons
    var greenIcon = L.divIcon({
        html: '🟢',
        className: '',
        iconSize: [25, 25]
    });

    var redIcon = L.divIcon({
        html: '🔴',
        className: '',
        iconSize: [25, 25]
    });

    var blueIcon = L.divIcon({
        html: '🔵',
        className: '',
        iconSize: [25, 25]
    });

    // Start marker
    var startMarker = L.marker(
        [<?= $start_lat ?>, <?= $start_lng ?>], 
        {icon: greenIcon}
    ).addTo(map);
    startMarker.bindPopup(
        '<b>🚌 Start: <?= $route['start_point'] ?></b><br>Starting Point'
    ).openPopup();

    // End marker
    var endMarker = L.marker(
        [<?= $end_lat ?>, <?= $end_lng ?>], 
        {icon: redIcon}
    ).addTo(map);
    endMarker.bindPopup(
        '<b>🏁 End: <?= $route['end_point'] ?></b><br>Final Destination'
    );

    // Route points array for line
    var routePoints = [
        [<?= $start_lat ?>, <?= $start_lng ?>],
        <?php foreach ($stop_coords as $stop): ?>
        [<?= $stop['lat'] ?>, <?= $stop['lng'] ?>],
        <?php endforeach; ?>
        [<?= $end_lat ?>, <?= $end_lng ?>]
    ];

    // Intermediate stop markers
    <?php foreach ($stop_coords as $stop): ?>
    L.marker(
        [<?= $stop['lat'] ?>, <?= $stop['lng'] ?>], 
        {icon: blueIcon}
    ).addTo(map)
    .bindPopup('<b>🛑 Stop: <?= $stop['name'] ?></b><br>Intermediate Stop');
    <?php endforeach; ?>

    // Draw route line
    L.polyline(routePoints, {
        color: '#1a73e8',
        weight: 4,
        opacity: 0.8,
        dashArray: '10, 5'
    }).addTo(map);

    // Fit map to show all markers
    map.fitBounds(routePoints);
</script>

</body>
</html>