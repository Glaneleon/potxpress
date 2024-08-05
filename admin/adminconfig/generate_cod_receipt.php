<?php
require_once('./dompdf/vendor/autoload.php');
require_once('../../config/config.php');

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function format_date($date_string) {
  $date = DateTime::createFromFormat('F d, Y', $date_string);
  return $date->format('Y-m-d');
}

use Dompdf\Dompdf;
use Dompdf\Exception;

// Get data from request with error handling
try {
  $type = $_POST['type'];
  $customerName = $_POST['customerName'];
  $orderNo = $_POST['orderNo'];
  $orderId = $_POST['orderId'];
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
  header('Location: view_order_details.php?order_id='+$orderId); // Replace with your error page
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
$cod = '<h2>CUSTOMER COPY RECEIPT</h2>';
$gcash = '<h2>GCASH RECEIPT</h2>';
$orcod = '<h2>STORE COPY RECEIPT</h2>';
$html2 = '<h1>Order Summary</h1>
      <p>Order Number: #' . $orderNo . '</p>
      <p>Order Date: ' . $orderDate . '</p>
      <p>Customer: ' . $customerName . '</p>
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
  if ($detail['quantity'] > 1) {
    $quantityUnit = ' pieces';
  }
  $html2 .= '<tr>
    <td>' . $detail['product_name'] . '</td>
    <td>' . $detail['product_color'] . '</td>
    <td>' . $detail['quantity'] . $quantityUnit . '</td>
    <td>₱ ' . number_format($detail['price'], 2) . '</td>
    <td>₱ ' . number_format($detail['quantity'] * $detail['price'], 2) . '</td>
  </tr>';
  }

$html2 .= '</tbody>
        </table>
      </section>
      <section class="order-summary">
        <h2>Order Summary</h2>
        <p><strong>Total: ₱ ' . number_format($totalPrice, 2) . '</strong></p>
      </section>
    </div>';
$signature = '<div style="margin-left: 430px; margin-top: 100px;">
      <p style="margin: 30px 5px 0px 5px;">Name: __________________________________</p>
      <p style="margin: 0px 0px 0px 50px; font-size: 0.5rem">SIGNATURE OVER PRINTED NAME</p>
      <p style="margin: 15px 5px;">Date: ___________________________________</p></div>';
 $htmllast ='</body>
  </html>';

$html = $html1 . $cod . $html2 . $htmllast;
$storeCopy = $html1 . $orcod . $html2 . $signature . $htmllast;
$gcash = $html1 . $gcash . $html2 . $htmllast;

$orderDate = format_date($orderDate);

if ($type === 'cod') {
  // Instantiate Dompdf
  $dompdf = new Dompdf();
  $options = $dompdf->getOptions();
  $options->setDefaultFont('DejaVu Serif');
  $dompdf->setOptions($options);

  $filename = $orderDate . '-' . $orderId;
  $timestamp = time();
  $finalfilename = $filename . '_' . $timestamp;

  $dompdf->loadHtml($html);
  $dompdf->setPaper('A4', 'portrait');
  $dompdf->render();

  // Reinstantiate Dompdf
  $storedompdf = new Dompdf();
  $storeoptions = $storedompdf->getOptions();
  $storeoptions->setDefaultFont('DejaVu Serif');
  $storedompdf->setOptions($storeoptions);

  $storefilename = $orderDate . '-' . $orderId;
  $storetimestamp = time();
  $storefinalfilename = $storefilename . '_' . $storetimestamp;

  $storedompdf->loadHtml($storeCopy);
  $storedompdf->setPaper('A4', 'portrait');
  $storedompdf->render();


  // Output the PDF
  $output = $dompdf->output();
  $storeoutput = $storedompdf->output();

  if (file_put_contents('../../receipts/' . $finalfilename . '.pdf', $output) != false) {
    $type = "COD Receipt";
    $file_path = $finalfilename . '.pdf';

    $sql = "INSERT INTO pdfs (type, file_path, order_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $type, $file_path, $orderId);

    if ($stmt->execute()) {
      $stmt->close();
    } else {
      $response = ['error' => 'Failed to input receipt record to database.'];
      header('Content-Type: application/json');
      echo json_encode($response);
    }

    if (file_put_contents('../../receipts/storecopy/' . $finalfilename . '-OR.pdf', $storeoutput) != false) {
      $type = "Official COD Receipt";
      $file_path = 'storecopy/' .$finalfilename . '-OR.pdf';

      $sql = "INSERT INTO pdfs (type, file_path, order_id) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssi", $type, $file_path, $orderId);

      if ($stmt->execute()) {
        $stmt->close();
      } else {
        $response = ['error' => 'Failed to input official receipt record to database.'];
        header('Content-Type: application/json');
        echo json_encode($response);
      }

      $response = ['success' => 'COD Receipt successfully made.'];
      header('Content-Type: application/json');
      echo json_encode($response);
    }
  } else {
    $response = ['error' => 'Failed to save PDF'];
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($response);
  }
} elseif ($type === 'gcash') {
  // Instantiate Dompdf
  $gdompdf = new Dompdf();
  $goptions = $gdompdf->getOptions();
  $goptions->setDefaultFont('DejaVu Serif');
  $gdompdf->setOptions($goptions);

  $gfilename = $orderDate . '-' . $orderId;
  $gtimestamp = time();
  $gfinalfilename = $gfilename . '_' . $gtimestamp;

  $gdompdf->loadHtml($gcash);
  $gdompdf->setPaper('A4', 'portrait');
  $gdompdf->render();

  $goutput = $gdompdf->output();

  if (file_put_contents('../../receipts/' . $gfinalfilename . '-GC.pdf', $goutput) != false) {
    $type = "GCash Receipt";
    $file_path = $gfinalfilename . '-GC.pdf';

    $sql = "INSERT INTO pdfs (type, file_path, order_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $type, $file_path, $orderId);

    if ($stmt->execute()) {
      $stmt->close();
    } else {
      $response = ['error' => 'Failed to input GCash receipt record to database.'];
      header('Content-Type: application/json');
      echo json_encode($response);
    }

    $response = ['success' => 'Successfully generated GCash receipt.'];
    header('Content-Type: application/json');
    echo json_encode($response);

  } else {
    $response = ['error' => 'Failed to save PDF'];
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode($response);
  }
} else {
  $response = ['error' => 'Invalid Request.'];
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode($response);
}
