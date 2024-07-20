<?php
include('config.php');
include('get_product_details.php');

function getHighestPrice() {
    include('config.php');
    $query = "SELECT MAX(price) as max_price FROM products";
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        return round($row['max_price']);
    } else {
        return 0; // Default to 0 if there's an issue or no products
    }
}

function getInStockProducts() {
    include('config.php');
    $query = "SELECT * FROM products WHERE stock_quantity > 0";
    $result = mysqli_query($conn, $query);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return false;
    }
}

function searchAndSortProducts($searchTerm, $sortByPrice) {
    include('config.php');
    $searchTerm = mysqli_real_escape_string($conn, $searchTerm);

    $query = "SELECT * FROM products WHERE name LIKE '%$searchTerm%' OR description LIKE '%$searchTerm%'";

    if ($sortByPrice === 'priceHighToLow') {
        $query .= " ORDER BY price DESC";
    } elseif ($sortByPrice === 'priceLowToHigh') {
        $query .= " ORDER BY price ASC";
    }

    $result = mysqli_query($conn, $query);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        return false;
    }
}

// Check if search term is provided
if (isset($_POST['searchTerm'])) {
    $stockFilter = $_POST['inStock'];
    $searchTerm = $_POST['searchTerm'];
    $sortByPrice = isset($_POST['sortByPrice']) ? $_POST['sortByPrice'] : null;

    $searchResults = searchAndSortProducts($searchTerm, $sortByPrice);

    if ($searchResults && $stockFilter == 2) {
        // Filter search results for in-stock products
        $inStockSearchResults = array_filter($searchResults, function($product) {
            return $product['stock_quantity'] > 0;
        });

        if ($inStockSearchResults) {
            foreach ($inStockSearchResults as $product) {
                echo '<a href="product.php?id=' . $product['product_id'] . '" class="product-link text-decoration-none">';
                echo '<div class="card mx-2 my-3" style="width: 18rem;">';
                echo '<img class="product-card-img" src="'.$product['imagefilepath'].'">';
                echo '<div class="card-body border-top">';
                echo '<p class="card-text text-dark">' . $product['name'] . '</p>';
                echo '<h4 class="card-title text-orange">₱ ' . $product['price'] . '</h4>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
            }
        }
    } elseif ($searchResults && $stockFilter == 3) {
            // Filter search results for in-stock products
            $inStockSearchResults = array_filter($searchResults, function($product) {
                return $product['stock_quantity'] == 0;
            });
    
            if ($inStockSearchResults) {
                foreach ($inStockSearchResults as $product) {
                    echo '<a href="product.php?id=' . $product['product_id'] . '" class="product-link text-decoration-none">';
                    echo '<div class="card mx-2 my-3" style="width: 18rem;">';
                    echo '<img class="product-card-img" src="'.$product['imagefilepath'].'">';
                    echo '<div class="card-body border-top">';
                    echo '<p class="card-text text-dark">' . $product['name'] . '</p>';
                    echo '<h4 class="card-title text-orange">₱ ' . $product['price'] . '</h4>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                }
            }
    } elseif ($searchResults) {
            foreach ($searchResults as $product) {
                echo '<a href="product.php?id=' . $product['product_id'] . '" class="product-link text-decoration-none">';
                echo '<div class="card mx-2 my-3" style="width: 18rem;">';
                echo '<img class="product-card-img" src="'.$product['imagefilepath'].'">';
                echo '<div class="card-body border-top">';
                echo '<p class="card-text text-dark">' . $product['name'] . '</p>';
                echo '<h4 class="card-title text-orange">₱ ' . $product['price'] . '</h4>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
            }
    } else{
            echo 'No in-stock products found for the search term';
    }}
?>
