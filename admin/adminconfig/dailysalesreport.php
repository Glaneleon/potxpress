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

$sql = 'SELECT 
            order_id_no as "Order No.", 
            DATE(order_date) as "Date", 
            total_amount as "Order Value", 
            payment_received as "Total Paid", 
            payment_mode as "Mode", 
            CONCAT(uers_test.firstname, " ", uers_test.lastname) AS "Customer Name" 
        FROM orders 
        INNER JOIN uers_test 
        ON orders.user_id = uers_test.user_id 
        WHERE orders.status = 4
        AND orders.payment_received IS NOT NULL';

$whereClause = '';
// if (isset($_POST['potCategory'])) {
//     $whereClause .= ' AND DATE(order_date) = :selectedDate ORDER BY order_date DESC';
// }

if (isset($_POST['customerId'])) {
    $customerId = $_POST['customerId'];
    $whereClause .= ' AND uers_test.user_id = :customerId';
}

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

if (isset($_POST['customerId'])) {
    $stmt->bindParam(':customerId', $customerId);
}

if (empty($endDate) || $endDate == null) {
    $stmt->bindParam(':selectedDate', $selectedDate);
} elseif (!empty($startDate) && !empty($endDate)) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
}

$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($orders)) {
    header("Location: ../admin.php?invalid=pdf_generation_failed#orders");
    exit();
}

// for testing db returns
// var_dump($orders);

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
    $pdf->Cell(31, 6, ucfirst($columnHeader), 1, 0, 'C', true); // Add 'true' to fill the cell
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
    foreach ($order as $key => $value) {
        if ($key === 'Order Value' || $key === 'Total Paid') {
            $formattedValue = number_format($value, 2);
            $pdf->Cell(31, 6, $formattedValue, 1, 0, 'R', $fill);
        } else {
            $pdf->Cell(31, 6, strtoupper($value), 1, 0, 'C', $fill);
        }
    }    
    $pdf->Ln();
    $fill = !$fill;
}

// Report Footer
$pdf->Ln();
$pdf->Cell(150, 5, 'Total Order Value:', 0, 0, 'R');
$totalSales = 0;
foreach ($orders as $order) {
    $totalSales += $order['Order Value'];
}
$pdf->Cell(30, 5, 'PHP ' . number_format($totalSales, 2), 0, 1, 'R');

$pdf->Ln();
$pdf->Cell(150, 0, 'Total Payment Received:', 0, 0, 'R');
$totalPayment = 0;
foreach ($orders as $order) {
    $totalPayment += $order['Total Paid'];
}
$pdf->Cell(30, 0, 'PHP ' . number_format($totalPayment, 2), 0, 1, 'R');

$pdf->Cell(0, 10, 'Page ' . $pdf->PageNo(), 0, 0, 'C');

$check = 0;

// Save as PDF
if (!empty($_POST['endDate'])) {
    $filename = '../../dailyreports/' . $startDate . 'to' . $endDate . '-' . $formattedTime . '.pdf';
} else {
    $filename = '../../dailyreports/' . $selectedDate . '-' . $formattedTime . '.pdf';
}

$output = $pdf->Output($filename, 'F');

if ($output === false) {
    header("Location: ../admin.php?error=pdf_generation_failed#orders");
    exit();
} else {
    header("Location: ../admin.php?success=pdf_generated#pdf");
    exit();
}
