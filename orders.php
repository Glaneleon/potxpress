<?php
include('config/config.php');
include('config/get_orders.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders Table</title>
    <link rel="stylesheet" href="assets/styles/ratingstyles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha384-oSNQ5IqKkD9j/m2WpFj/mK9ZZVViW6ST13j+eEz5/woi6DzDPv0zV5B7taKGaUad" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>
<body>
<?php include './config/header.php';
$allOrders = getAllOrders();?>
<div class="container mt-5">
    
    <table id="ordersTable" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Order Date</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
            if ($allOrders) {
                foreach ($allOrders as $order) {
                    echo '<tr>';
                    echo '<td>'.$order['order_date'].'</td>';
                    echo '<td>'.$order['total_amount'].'</td>';
                    echo '<td>';
                    switch ($order['status']) {
                        case 1:
                            echo "Order Placed";
                            break;
                        case 2:
                            echo "In Transit";
                            break;
                        case 3:
                            echo "Delivered";
                            break;
                        default:
                            echo "Unknown";
                            break;
                    }
                    echo '</td>';
                    echo '<td>
                    <button type="button" class="btn btn-warning view-details-btn" data-order-id="'.$order['order_id'].'" data-bs-toggle="modal" data-bs-target="#orderDetailsModal">View Details</button>
                    </td>';
                    echo '</tr>';
                }
            } else {
                echo 'No orders found.';
            }
        ?>

        </tbody>
    </table>
</div>

<!-- view order details modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="orderDetailsContent">
                <!-- data from ajax goes here -->
            </div>
        </div>
    </div>
</div>


<?php include './config/footer.php';?>
<!-- Bootstrap JS Bundle (Popper included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        drawCallback: function(settings) {
            // Reattach event listener for the .view-details-btn buttons
            $('.view-details-btn').off('click').on('click', function() {
                var orderId = $(this).data('order-id');
                $.ajax({
                    url: 'config/get_order_details.php',
                    type: 'POST',
                    data: {orderId: orderId},
                    success: function(response) {
                        console.log(response);
                        // Parse the JSON response
                        var orderDetails = JSON.parse(response);
                        
                        // Construct the HTML content for the modal
                        var modalContent = '<div class="modal-body">';
                        modalContent += '<table class="table">';
                        modalContent += '<thead>';
                        modalContent += '<tr>';
                        modalContent += '<th>Product Image</th>';
                        modalContent += '<th>Name</th>';
                        modalContent += '<th>Quantity</th>';
                        modalContent += '<th>Price</th>';
                        modalContent += '<th>Total</th>';
                        modalContent += '</tr>';
                        modalContent += '</thead>';
                        modalContent += '<tbody>';
                        // Loop through each order detail
                        orderDetails.forEach(function(detail) {
                            modalContent += '<tr>';
                            modalContent += '<td><img src="' + detail.imagefilepath + '" alt="Product Image" class="img-thumbnail" style="width: 50px; height: 50px;"></td>';
                            modalContent += '<td>' + detail.name + '</td>';
                            modalContent += '<td>' + detail.quantity + '</td>';
                            modalContent += '<td>' + detail.price + '</td>';
                            modalContent += '<td>' + (detail.quantity*detail.quantity) + '</td>';
                            modalContent += '</tr>';
                        });
                        modalContent += '</tbody>';
                        modalContent += '</table>';

                        modalContent += '<div mt-5>';
                        modalContent += '<h5>Order Status</h5>';

                        modalContent += '<div class="row my-2">';
                        modalContent += '<div class="col-2">';
                        modalContent += '<img src="assets/icons/order_placed.png" alt="Order Placed" style="width: 30px; height: 30px;">';
                        modalContent += '</div>';
                        modalContent += '<div class="col-6">';
                        modalContent += orderDetails[0].order_placed;
                        modalContent += '</div>';
                        modalContent += '<div class="col-4">';
                        modalContent += 'Order Placed';
                        modalContent += '</div>';
                        modalContent += '</div>';

                        if (orderDetails[0].in_transit){
                            modalContent += '<div class="row my-2">';
                            modalContent += '<div class="col-2">';
                            modalContent += '<img src="assets/icons/domestic_transit.png" alt="In Transit" style="width: 30px; height: 30px;">';
                            modalContent += '</div>';
                            modalContent += '<div class="col-6">';
                            modalContent += orderDetails[0].in_transit
                            modalContent += '</div>';
                            modalContent += '<div class="col-4">';
                            modalContent += 'In Transit';
                            modalContent += '</div>';
                            modalContent += '</div>';
                        }

                        if (orderDetails[0].delivered){
                        modalContent += '<div class="row my-2">';
                        modalContent += '<div class="col-2">';
                        modalContent += '<img src="assets/icons/delivered.png" alt="Delivered" style="width: 30px; height: 30px;">';
                        modalContent += '</div>';
                        modalContent += '<div class="col-6">';
                        modalContent += orderDetails[0].delivered
                        modalContent += '</div>';
                        modalContent += '<div class="col-4">';
                        modalContent += 'Delivered';
                        modalContent += '</div>';
                        modalContent += '</div>';
                        }

                        
                        // Update the modal content and show the modal
                        $('#orderDetailsContent').html(modalContent);
                        $('#orderDetailsModal').modal('show');
                    }
                });
            });
        }
    });
});
</script>
<!-- <script>
    document.querySelectorAll('.star').forEach(star => {
  star.addEventListener('click', () => {
    const value = star.getAttribute('data-value');
    console.log(`You rated this ${value} stars.`);
    // You can do something with the value here, like sending it to a server or updating a database.
    document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
    for (let i = 1; i <= value; i++) {
      document.querySelector(`.star[data-value="${i}"]`).classList.add('active');
    }
  });
});
</script> -->
</body>
</html>
