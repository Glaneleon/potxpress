<?php
require_once('../../config/fpdf/fpdf.php');
require_once('../../config/config.php');

try {
    $conn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Consider logging the error or sending an email notification
    echo "Connection failed: " . $e->getMessage();
    exit;
}

$now = new DateTime();
$dateToday = $now->format('Y-m-d H:i:s');
$milliseconds = $now->format('u');
$formattedTime = str_replace([':', '.', ' '], '', $now->format('YmdHi') . $milliseconds);

$sql = 'SELECT order_id_no as "Order No.", order_date as "Date", total_amount as "Total Amount", payment_received as "Payment Received", payment_mode as "Payment Mode" FROM orders WHERE status = 3 AND payment_received IS NOT NULL';

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
$pdf->SetFont('Times', 'B', 20);
$pdf->Ln();
$pdf->Cell(0, 10, 'Sales Report', 0, 1, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Times', '', 12);
if (!empty($_POST['endDate'])) {
    $pdf->Cell(0, 5, 'Report for Date: From ' . $startDate . ' To '. $endDate, 0, 1);
}else{
    $pdf->Cell(0, 5, 'Report for Date: ' . $selectedDate, 0, 1);
}
$pdf->Ln();
$pdf->SetFont('Times', '', 12);
$pdf->Cell(0, 5, 'Date Generated: ' . $dateToday, 0, 1);
$pdf->Ln();

$columnHeaders = array_keys($orders[0]);

$pdf->SetFillColor(71, 167, 255, 1);

// Table Header
$pdf->SetFont('Times', 'B', 10);
foreach ($columnHeaders as $columnHeader) {
    $pdf->Cell(37, 6, ucfirst($columnHeader), 1, 0, 'C', true); // Add 'true' to fill the cell
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Times', '', 10);
$fill = true; // Alternate row colors
foreach ($orders as $order) {
    $pdf->SetFillColor(255, 255, 255); // Reset fill color for each row
    if ($fill) {
        $pdf->SetFillColor(240, 240, 240); // Alternate row color
    }
    foreach ($order as $value) {
        $pdf->Cell(37, 6, strtoupper($value), 1, 0, '', $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

// Report Footer
$pdf->Ln();
$pdf->Cell(150, 6, 'Total Sales:', 0, 0, 'R');
$totalSales = 0;
foreach ($orders as $order) {
    $totalSales += $order['Total Amount'];
}
$pdf->Cell(30, 6, 'PHP ' . number_format($totalSales, 2), 0, 1, 'R');

$pdf->Ln();
$pdf->Cell(150, 6, 'Total Payment Received:', 0, 0, 'R');
$totalPayment = 0;
foreach ($orders as $order) {
    $totalPayment += $order['Payment Received'];
}
$pdf->Cell(30, 6, 'PHP ' . number_format($totalPayment, 2), 0, 1, 'R');

$pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 0, 'C');

$check = 0;

// Save as PDF
if (!empty($_POST['endDate'])) {
    $filename = '../../dailyreports/' . $startDate . 'to' . $endDate . '-' . $formattedTime . '.pdf';
    $pdf->Output($filename, 'F');
    header("Location: ../admin.php");
    exit();
} else {
    $filename = '../../dailyreports/' . $selectedDate . '-' . $formattedTime . '.pdf';
    $pdf->Output($filename, 'F');
    header("Location: ../admin.php");
    exit();
}