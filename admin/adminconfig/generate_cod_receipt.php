<?php
require_once('./dompdf/vendor/autoload.php');

use Dompdf\Dompdf;
use Dompdf\Exception;

// Get data from request with error handling
try {
    $customerName = $_POST['customerName'];
    $orderNo = $_POST['orderNo'];
    $orderDate = $_POST['orderDate'];
    $orderDetails = $_POST['jsonData'];
    if (!is_array($orderDetails)) {
        // Handle error: Data is not an array
        die("Invalid data format: Expected an array");
    }
} catch (Exception $e) {
    // Handle missing data or invalid data types
    error_log("Error fetching data: " . $e->getMessage());
    // Redirect or display an error message to the user
    header('Location: admin.php'); // Replace with your error page
    exit;
}


$totalPrice = 0.00;
$html = '';
$storeCopy = '';

// Create dynamic HTML content for the receipt
$html1 = '
<!DOCTYPE html>
<html>
    <head>
      <title>Order Receipt</title>
        <style>
          body {
              font-family: Arial Unicode MS;
              font-size: 10px;
          }
          .receipt {
            width: 550px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
          }

          header {
            text-align: center;
          }

          table {
            width: 100%;
            border-collapse: collapse;
          }

          th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
          }

          .order-summary {
            text-align: right;
          }
        </style>
    </head>
<body>
  <div class="receipt">
    <header>';
$html4 = '<h2>CUSTOMER COPY RECEIPT</h2>';
$html3 = '<h2>STORE COPY RECEIPT</h2>
      <p style="margin: 30px 5px 0px 5px;">Name: __________________________________</p>
      <p style="margin: 0px 0px 0px 30px; font-size: 0.5rem">SIGNATURE OVER PRINTED NAME</p>
      <p style="margin: 15px 5px;">Date: ___________________________________</p>';

$html2 = '<h1>Order Summary</h1>
      <p>Order Number: #'.$orderNo.'</p>
      <p>Order Date: '.$orderDate.'</p>
      <p>Customer: '.$customerName.'</p>
    </header>
    <section class="order-details">
      <h2>Order Details</h2>
      <table>
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Product Color</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>';

foreach ($orderDetails as $detail) {
    $subtotal = $detail['quantity'] * $detail['price'];
    $totalPrice += $subtotal;
    $quantityUnit = ' piece';
    if ($detail['quantity']>1){
        $quantityUnit = ' pieces';
    }
  $html2 .= '<tr>
    <td>'.$detail['product_name'].'</td>
    <td>'.$detail['product_color'].'</td>
    <td>'. $detail['quantity'].$quantityUnit.'</td>
    <td>₱ '.number_format($detail['price'], 2).'</td>
    <td>₱ '.number_format($detail['quantity'] * $detail['price'], 2).'</td>
  </tr>';
}

$html2 .= '</tbody>
        </table>
      </section>
      <section class="order-summary">
        <h2>Order Summary</h2>
        <p><strong>Total: ₱ '.number_format($totalPrice, 2).'</strong></p>
      </section>
    </div>
  </body>
  </html>'
;

$html = $html1.$html4.$html2;
$storeCopy = $html1.$html3.$html2;

// Instantiate Dompdf
$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->setDefaultFont('DejaVu Serif');
$dompdf->setOptions($options);

$filename = $orderDate . '-' . $orderNo;
$timestamp = microtime(true);
$finalfilename = $filename . '_' . $timestamp;

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Reinstantiate Dompdf
$storedompdf = new Dompdf();
$storeoptions = $storedompdf->getOptions();
$storeoptions->setDefaultFont('DejaVu Serif');
$storedompdf->setOptions($storeoptions);

$storefilename = $orderDate . '-' . $orderNo;
$storetimestamp = microtime(true);
$storefinalfilename = $filename . '_' . $timestamp;

$storedompdf->loadHtml($storeCopy);
$storedompdf->setPaper('A4', 'portrait');
$storedompdf->render();


// Output the PDF
$output = $dompdf->output();
$storeoutput = $storedompdf->output();

if (file_put_contents('../../receipts/'.$finalfilename.'.pdf', $output) != false) {
  if (file_put_contents('../../receipts/storecopy/'.$finalfilename.'-OR.pdf', $storeoutput) != false) {
    $response = ['success' => 'COD Receipt successfully made.'];
    header('Content-Type: application/json');
    echo json_encode($response);
  }
} else {
    // File saving failed
    $response = ['error' => 'Failed to save PDF'];
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($response);
}