<?php
require_once('../../config/fpdf/fpdf.php');
require_once('../../config/config.php');

try {
    $conn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Get user-selected date (assuming it's stored in $_POST['date'])
if (isset($_POST['date'])) {
    $selectedDate = $_POST['date'];
} else {
    echo "Please select a date.";
    exit;
}
$now = new DateTime();
$datetoday = $now->format('Y-m-d H:i:s');
$milliseconds = $now->format('u');
$formattedTime = str_replace([':', '.', ' '], '', $now->format('YmdHi') . $milliseconds);

$sql = 'SELECT order_id AS "ID", order_id_no as "Order No.", ship_id as "Ship ID", order_date as "Date", total_amount as "Total Amount" FROM orders WHERE DATE(order_date) = :selectedDate AND status = 3';

$stmt = $conn->prepare($sql);
$stmt->bindParam(':selectedDate', $selectedDate);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($orders)) {
    echo "No orders found for the selected date.";
    header("Location: ../admin.php");
    exit;
}

$pdf = new FPDF();
$pdf->AddPage();

// Report Header
$pdf->Image('../../assets/icons/PotXpress.png', 160, 8, 40);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Ln();
$pdf->Cell(0, 10, 'Daily Sales Report', 0, 1, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Report for Date: ' . $selectedDate, 0, 1);
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Date Generated: ' . $datetoday, 0, 1);
$pdf->Ln();

// Since you're using SELECT *, we'll dynamically create table headers based on retrieved columns
$columnHeaders = array_keys($orders[0]);  // Get column names from first row

// Table Header
$pdf->SetFont('Arial', 'B', 10);
foreach ($columnHeaders as $columnHeader) {
    $pdf->Cell(37, 6, ucfirst($columnHeader), 1, 0, 'C'); // Capitalize column names
}
$pdf->Ln(); // Move to next line after headers

// Table Data
$pdf->SetFont('Arial', '', 10);
foreach ($orders as $order) {
    foreach ($order as $value) {
        $pdf->Cell(37, 6, $value, 1, 0);
    }
    $pdf->Ln(); // Move to next line after each order
}

// Report Footer
$pdf->Ln();
$pdf->Cell(150, 6, 'Total Sales:', 0, 0, 'R'); // Label for total sales

// Calculate total sales dynamically (assuming 'total_amount' is the relevant column)
$totalSales = 0;
foreach ($orders as $order) {
    $totalSales += $order['Total Amount'];
}

$pdf->Cell(30, 6, 'PHP ' . number_format($totalSales, 2), 0, 1, 'R');

// Additional options for the footer (uncomment if desired)
$pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 0, 'C');  // Add page number

// Save as PDF
$pdf->Output('../../dailyreports/' . $selectedDate . '-' . $formattedTime . '.pdf', 'F');

header("Location: ../admin.php");
exit();
