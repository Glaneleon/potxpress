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

$now = new DateTime();
$datetoday = $now->format('Y-m-d H:i:s');
$milliseconds = $now->format('u');
$formattedTime = str_replace([':', '.', ' '], '', $now->format('YmdHi') . $milliseconds);

$sql = 'SELECT order_id AS "ID", order_id_no as "Order No.", ship_id as "Ship ID", order_date as "Date", total_amount as "Total Amount" FROM orders WHERE status = 3';

$whereClause = '';

if (empty($_POST['endDate'])) {
    $selectedDate = $_POST['date'];
    $whereClause .= ' AND DATE(order_date) = :selectedDate ORDER BY order_date DESC';
} elseif (isset($_POST['date']) && !empty($_POST['endDate'])) {
    $startDate = $_POST['date'];
    $endDate = $_POST['endDate'];
    $whereClause .= ' AND DATE(order_date) BETWEEN :startDate AND :endDate ORDER BY order_date DESC';
}

$sql .= $whereClause;

$stmt = $conn->prepare($sql);

if (empty($endDate) || $endDate == null) {
    $stmt->bindParam(':selectedDate', $selectedDate);
} elseif (!empty($startDate) && !empty($endDate)) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
} else{
    echo "Error binding values to query.";
}


$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($orders)) {
    echo "No orders found for the selected date.";
    header("Location: ../admin.php");
    exit();
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
if (!empty($_POST['endDate'])) {
    $pdf->Cell(0, 5, 'Report for Date: From ' . $startDate . ' To '. $endDate, 0, 1);
}else{
    $pdf->Cell(0, 5, 'Report for Date: ' . $selectedDate, 0, 1);
}
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Date Generated: ' . $datetoday, 0, 1);
$pdf->Ln();

$columnHeaders = array_keys($orders[0]);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
foreach ($columnHeaders as $columnHeader) {
    $pdf->Cell(37, 6, ucfirst($columnHeader), 1, 0, 'C');
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
foreach ($orders as $order) {
    foreach ($order as $value) {
        $pdf->Cell(37, 6, $value, 1, 0);
    }
    $pdf->Ln();
}

// Report Footer
$pdf->Ln();
$pdf->Cell(150, 6, 'Total Sales:', 0, 0, 'R');

$totalSales = 0;
foreach ($orders as $order) {
    $totalSales += $order['Total Amount'];
}

$pdf->Cell(30, 6, 'PHP ' . number_format($totalSales, 2), 0, 1, 'R');

$pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 0, 'C');

$check = 0;

// Save as PDF
if (!empty($_POST['endDate'])) {
    $filename = '../../dailyreports/' . $startDate . 'to' . $endDate . '-' . $formattedTime . '.pdf';
    $check = 1;
} else {
    $filename = '../../dailyreports/' . $selectedDate . '-' . $formattedTime . '.pdf';
    $check = 2;
}

$pdf->Output($filename, 'F');
if($check = 1 || $check = 2){
    header("Location: ../admin.php");
    exit();
} else{
    echo "Failed to save.";
}
