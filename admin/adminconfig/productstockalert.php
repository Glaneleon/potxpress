<?php
include('../../config/config.php');

$sql = "SELECT product_id, name, stock_quantity FROM products WHERE stock_quantity = 0";
$outofstock = $conn->query($sql);

$sql = "SELECT product_id, name, stock_quantity FROM products WHERE stock_quantity > 0 AND stock_quantity < 10";
$lessthanten = $conn->query($sql);

if ($outofstock->num_rows > 0 || $lessthanten->num_rows > 0) {
    $products = array();

    // Fetch rows for out of stock products
    while ($row = $outofstock->fetch_assoc()) {
        $products[] = array(
            'success' => true,
            'text' => 'Product #' . $row['product_id'] . ' is out of stock.',
            'id' => $row['product_id'],
            'name' => $row['name'],
            'stock' => $row['stock_quantity']
        );
    }

    // Fetch rows for products with stock less than 10
    while ($row = $lessthanten->fetch_assoc()) {
        $products[] = array(
            'success' => true,
            'text' => 'Product #' . $row['product_id'] . ' has less than 10 in stock.',
            'id' => $row['product_id'],
            'name' => $row['name'],
            'stock' => $row['stock_quantity']
        );
    }

    echo json_encode($products);
} else {
    echo json_encode(array());
}

$conn->close();
?>