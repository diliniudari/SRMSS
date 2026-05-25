<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// Include FPDF
require('../fpdf.php');
include '../config/db.php';

// Get stats
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
$fuel = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT SUM(liters) as total_liters, SUM(cost) as total_cost 
     FROM fuel_logs"));

// Get schedules
$schedules = mysqli_query($conn, "
    SELECT s.*, r.start_point, r.end_point, d.name as driver_name
    FROM schedules s
    LEFT JOIN routes r ON s.route_id = r.id
    LEFT JOIN drivers d ON r.driver_id = d.id
    ORDER BY s.departure_time DESC
");

// Get fuel logs
$fuel_logs = mysqli_query($conn, "
    SELECT f.*, v.registration
    FROM fuel_logs f
    LEFT JOIN vehicles v ON f.vehicle_id = v.id
    ORDER BY f.date DESC
");

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// ---- HEADER ----
$pdf->SetFillColor(26, 115, 232);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 15, 'SRMSS - System Report', 0, 1, 'C', true);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, 'Smart Route Management and Scheduling System', 0, 1, 'C', true);
$pdf->Cell(0, 8, 'Generated: ' . date('d M Y H:i'), 0, 1, 'C', true);
$pdf->Ln(5);

// ---- OVERVIEW ----
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'System Overview', 0, 1, 'L', true);
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(95, 8, 'Total Routes: ' . $total_routes, 1, 0, 'L');
$pdf->Cell(95, 8, 'Total Drivers: ' . $total_drivers, 1, 1, 'L');
$pdf->Cell(95, 8, 'Total Vehicles: ' . $total_vehicles, 1, 0, 'L');
$pdf->Cell(95, 8, 'Total Schedules: ' . $total_schedules, 1, 1, 'L');
$pdf->Ln(5);

// ---- TRIP STATUS ----
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'Trip Status Summary', 0, 1, 'L', true);
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(63, 8, 'On Time: ' . $on_time, 1, 0, 'C');
$pdf->Cell(63, 8, 'Delayed: ' . $delayed, 1, 0, 'C');
$pdf->Cell(64, 8, 'Completed: ' . $completed, 1, 1, 'C');
$pdf->Ln(5);

// ---- SCHEDULES TABLE ----
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'Schedule Details', 0, 1, 'L', true);
$pdf->Ln(3);

// Table header
$pdf->SetFillColor(26, 115, 232);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 8, 'Route', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Driver', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Departure', 1, 0, 'C', true);
$pdf->Cell(50, 8, 'Status', 1, 1, 'C', true);

// Table rows
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
while ($row = mysqli_fetch_assoc($schedules)) {
    $route = $row['start_point'] . '->' . $row['end_point'];
    $pdf->Cell(50, 7, $route, 1, 0, 'L');
    $pdf->Cell(40, 7, $row['driver_name'], 1, 0, 'L');
    $pdf->Cell(50, 7, date('d M Y H:i', 
        strtotime($row['departure_time'])), 1, 0, 'L');
    $pdf->Cell(50, 7, strtoupper($row['status']), 1, 1, 'C');
}
$pdf->Ln(5);

// ---- FUEL REPORT ----
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(0, 10, 'Fuel Consumption Report', 0, 1, 'L', true);
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(95, 8, 'Total Liters: ' . 
    number_format($fuel['total_liters'] ?? 0, 1) . ' L', 1, 0, 'L');
$pdf->Cell(95, 8, 'Total Cost: Rs. ' . 
    number_format($fuel['total_cost'] ?? 0, 2), 1, 1, 'L');
$pdf->Ln(5);

// Fuel table header
$pdf->SetFillColor(26, 115, 232);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 8, 'Vehicle', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Date', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Liters', 1, 0, 'C', true);
$pdf->Cell(70, 8, 'Cost (Rs.)', 1, 1, 'C', true);

// Fuel table rows
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
while ($row = mysqli_fetch_assoc($fuel_logs)) {
    $pdf->Cell(50, 7, $row['registration'], 1, 0, 'L');
    $pdf->Cell(40, 7, $row['date'], 1, 0, 'L');
    $pdf->Cell(30, 7, $row['liters'] . ' L', 1, 0, 'C');
    $pdf->Cell(70, 7, 'Rs. ' . number_format($row['cost'], 2), 1, 1, 'L');
}

// ---- FOOTER ----
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 8, 'Generated by SRMSS - Smart Route Management & Scheduling System', 
    0, 1, 'C');

// Output PDF
$pdf->Output('D', 'SRMSS_Report_' . date('Y-m-d') . '.pdf');
exit();
?>