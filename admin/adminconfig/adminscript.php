</div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; PotXpress 2024</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

    </div>
    <!-- orderspermonthchart -->
    <script src="./adminconfig/graphs/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>

    <script>
        function openImg(clickedImage) {
            var image = clickedImage.src;
            // var source = image.src;
            console.log(image);
            window.open(image);
        }

        function ucfirst(str) {
            if (typeof str !== 'string') {
                return str;
            }
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    </script>

    <script>
        // --- Orders Table Data
        function formatNumber(number, format) {
            const formatter = new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP',
                minimumFractionDigits: 2
            });

            switch (format) {
                case 'currency':
                    return formatter.format(number);
                case 'percent':
                    return (number * 100).toFixed(2) + '%';
                case 'decimal':
                    return number.toFixed(2);
                case 'thousands':
                    return number.toLocaleString('en-US');
                default:
                    return number;
            }
        }

        $(document).ready(function() {

            var table = $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./adminconfig/getall_orders.php",
                    type: "POST",
                    data: function(d) {
                        d.min_date = $('#min_date').val() || null;
                        d.max_date = $('#max_date').val() || null;
                    }
                },
                columns: [{
                        data: "order_id_no"
                    },
                    {
                        data: "firstname",
                        render: function(data, type, row) {
                            return ucfirst(data) + " " + ucfirst(row.lastname);
                        }
                    },
                    {
                        data: "order_date"
                    },
                    {
                        data: "total_amount",
                        render: function(data, type, row) {
                            return formatNumber(data, 'currency');
                        }
                    },
                    {
                        data: 'payment_img',
                        render: function(data, type, row) {
                            if (data) {
                                return '<img id="image" src=".' + data + '" alt= " " class="img-thumbnail mx-3" style="width: 50px; height: 50px;" onclick="openImg(this)">';
                            } else {
                                return '<img id="image" src="../assets/payment/default.png" alt= " " class="img-thumbnail mx-3" style="width: 50px; height: 50px;" onclick="openImg(this)">';
                            }
                        }
                    },
                    {
                        data: "payment_mode",
                        render: function(data, type, row) {
                            switch (data) {
                                case 'cod':
                                    return "Cash-On-Delivery";
                                case 'gcash':
                                    return "GCash";
                                default:
                                    return "Unknown";
                            }
                        }
                    },

                    {
                        data: "status",
                        render: function(data, type, row) {
                            switch (parseInt(data)) {
                                case 1:
                                    return "Order Placed";
                                case 2:
                                    return "Order Confirmed";
                                case 3:
                                    return "<span class='text-warning fw-bold'>In Transit</span>";
                                case 4:
                                    return "<span class='text-success fw-bold'>Delivered</span>";
                                case 5:
                                    return "Invalid Order";
                                case 6:
                                    return "<span class='text-danger fw-bold'>Cancelled</span>";
                                default:
                                    return "Unknown";
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<a href="view_order_details.php?order_id=' + row.order_id + '" class="btn btn-info">View Details</a>';
                        }
                    }
                ],
                error: function(xhr, error, code) {
                    console.error('DataTables error:', error);
                }
            });

            $('#min_date, #max_date').change(function() {
                table.draw();
            });
        });
    </script>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const success = urlParams.get('success');
        const invalid = urlParams.get('invalid');

        if (error) {
            alert('Error generating PDF. Please try again.');
            cleanUrl();
        } else if (success) {
            alert('Generated report successfully. You can view it on the PDF tab.');
            cleanUrl();
        } else if (invalid) {
            alert('No orders found for the selected date. Please try again.');
            cleanUrl();
        }

        function cleanUrl() {
            const url = new URL(window.location.href);
            url.search = '';
            window.history.replaceState({}, document.title, url);
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#productstable').DataTable();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#pdfTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./adminconfig/getall_pdfs.php",
                    type: "POST"
                },
                columns: [{
                        title: "Order ID",
                        data: 'order_id',
                        render: function(data, type, row) {
                            return data ? data : '<span class="text-muted">No Order ID for Reports</span>';
                        }
                    },
                    {
                        title: "Type",
                        data: 'type'
                    },
                    {
                        title: "Created At",
                        data: 'created_at'
                    },
                    {
                        title: "Action",
                        data: 'file_path',
                        render: function(data, type, row) {
                            if (row.type === 'Sales Report') {
                                return '<a class="btn btn-info" href="../dailyreports/' + data + '" target="_blank">View</a>';
                            } else {
                                return '<a class="btn btn-info" href="../receipts/' + data + '" target="_blank">View</a>';
                            }
                        }
                    }
                ],
                order: [],
                search: {
                    "caseInsensitive": true
                }
            });

            // binary search
            $('input[id="orderIdSearch"]').change(function() {
              var orderId = $(this).val();

              $.ajax({
                url: './adminconfig/getall_pdfs.php',
                type: 'POST',
                data: {
                  orderId: orderId
                },
                success: function(data) {
                  alert("Record exist.")
                }
              });
            });


        });
    </script>

    <script>
        $(document).ready( function () {
            $('#category_table').DataTable();
        } );
    </script>
    
    <!-- fragment url -->
    <!-- <script>
        function showDivBasedOnFragment() {
            // Get the fragment identifier from the URL
            var fragment = window.location.hash.substring(1);

            // Remove 'show' and 'active' classes from any element with class "tab-pane fade show active"
            var activeDivs = document.querySelectorAll('.tab-pane.fade.show.active');
            activeDivs.forEach(function(div) {
                div.classList.remove('show', 'active');
            });

            // Show the div with the corresponding ID
            if (fragment) {
                var targetDiv = document.getElementById(fragment);
                if (targetDiv) {
                    targetDiv.classList.add('show', 'active');
                }
            }
        }

        // Call the function on page load
        showDivBasedOnFragment();
    </script> -->
    <!-- show/hide navbar -->
    <script>
        window.addEventListener('DOMContentLoaded', event => {

            // Toggle the side navigation
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                // Uncomment Below to persist sidebar toggle between refreshes
                // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
                //     document.body.classList.toggle('sb-sidenav-toggled');
                // }
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
                });
            }

        });
    </script>
    <!-- show/hide tab -->
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all the navigation links
            var navLinks = document.querySelectorAll('.nav-link');

            // Add click event listener to each navigation link
            navLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    if (this.id === 'logs' || this.id === 'navbarDropdown') {
                        // Do nothing and return from the event handler
                        return;
                    }
                    // Prevent the default link behavior
                    event.preventDefault();

                    // Remove the "show active" class from all links and tab panes
                    navLinks.forEach(function(link) {
                        link.classList.remove('show', 'active');
                    });

                    // Add the "show active" class to the clicked link
                    this.classList.add('show', 'active');

                    // Get the target tab pane ID from the link's href attribute
                    var targetTabPaneId = this.getAttribute('href').substring(1);

                    // Remove the "show active" class from all tab panes
                    var tabPanes = document.querySelectorAll('.tab-pane');
                    tabPanes.forEach(function(pane) {
                        pane.classList.remove('show', 'active');
                    });

                    // Add the "show active" class to the target tab pane
                    document.getElementById(targetTabPaneId).classList.add('show', 'active');
                });
            });

            // Set the dashboard tab as the default active tab
            var dashboardTab = document.getElementById('dashboard-tab');
            dashboardTab.classList.add('show', 'active');

            var dashboardTabPane = document.getElementById('dashboard');
            dashboardTabPane.classList.add('show', 'active');
        });
    </script> -->
    <!-- product crud -->
    <script>
        $(document).ready(function() {

            //CREATE
            $('#addProductForm').submit(function(e) {
                e.preventDefault();

                // Serialize form data
                var formData = new FormData(this);

                // Make an AJAX request
                console.log("Before AJAX request");
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/add_product.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Response from server:', response);
                        // Check the response from the server
                        if (response.success) {
                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Product Added!',
                                text: response.originalString + ' has been successfully added.',
                                // text: 'The product has been successfully added.',
                                didClose: () => {
                                    window.location.reload();
                                }
                            });

                            // Optionally, close the modal or perform additional actions
                            $('#addProductModal').modal('hide');
                        } else {
                            // Handle the case where product addition failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to add the product. Please try againsss.',
                            });
                        }
                    },
                    error: function() {
                        console.error('Error:', error);
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });

            //UPDATE
            $('form[id^="editProductForm"]').submit(function(e) {
                e.preventDefault();

                // Extract product ID from the form's ID
                var productID = this.id.replace('editProductForm', '');

                // Serialize form data
                var formData = new FormData(this);

                // Make an AJAX request
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/edit_product.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.success) {
                            $('#editProductModal' + productID).modal('hide');


                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.data || 'Operation successful.',
                                didClose: () => {
                                    window.location.reload();
                                }
                            });
                        } else {
                            // Handle the case where the operation failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to perform the operation. Please try again.',
                            });
                        }
                    },
                    error: function() {
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });
        });
    </script>
    <script>
        //Delete
        $(document).ready(function() {
            // Attach click event to delete buttons
            $('#productstable').on('click', '[id^="deleteButton"]', function() {
                // Extract the product ID from the button ID
                var productID = this.id.replace('deleteButton', '');

                // Show the confirmation dialog
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make an AJAX request to delete_product.php
                        $.ajax({
                            type: 'POST',
                            url: './adminconfig/delete_product.php',
                            data: {
                                productID: productID
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Handle the response from the server
                                if (response.success) {
                                    // Show success message after deletion
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.data || 'Your file has been deleted.',
                                        icon: "success"
                                    }).then(() => {
                                        // Reload the page after dismissing the success message
                                        window.location.reload();
                                    });
                                } else {
                                    // Show error message if deletion fails
                                    Swal.fire({
                                        title: "Error",
                                        text: response.error || 'Failed to delete the file. Please try again.',
                                        icon: "error"
                                    });
                                }
                            },
                            error: function() {
                                // Show error message for AJAX error
                                Swal.fire({
                                    title: "Error",
                                    text: "An error occurred. Please try again.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    <!-- add stock -->
    <script>
        $(document).ready(function() {

            $('form[id^="addStockForm"]').submit(function(e) {
                console.log("Add Stock Form submitted");
                e.preventDefault();

                // Extract product ID from the form's ID
                var productID = this.id.replace('addStockForm', '');

                // Serialize form data
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: './adminconfig/add_stock.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.success) {
                            $('#addStockModal' + productID).modal('hide');

                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.data || 'Operation successful.',
                                didClose: () => {
                                    window.location.reload();
                                }
                            });
                        } else {
                            // Handle the case where the operation failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to perform the operation. Please try again.',
                            });
                        }
                    },
                    error: function() {
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });
        });
    </script>
    <!-- notification  -->
    <script>
        function productStockNotifications() {
            $.ajax({
                url: './adminconfig/productstockalert.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    $.each(response, function(index, data) {
                        if (data.success) {
                            if (data.stock == "0") {
                                var toast = `
                                <div id="toastnotif" class="position-fixed bottom-0 end-0 p-3" style="z-index: 12">
                                    <div class="toast" role="alert" aria-live="polite" aria-atomic="true" data-bs-delay="10000">
                                        <div class="toast-header bg-danger">
                                            <strong class="me-auto text-light">DANGER!</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                        <div class="toast-body">
                                            <strong>${data.name}</strong>
                                            <p>${data.text}</p>
                                            <p>Current Stock: ${data.stock}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            } else {
                                var toast = `
                                <div id="toastnotif" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                                    <div class="toast" role="alert" aria-live="polite" aria-atomic="true" data-bs-delay="10000">
                                        <div class="toast-header bg-warning">
                                            <strong class="me-auto">WARNING!</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                        <div class="toast-body">
                                            <strong>${data.name}</strong>
                                            <p>${data.text}</p>
                                            <p>Current Stock: ${data.stock}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            }
                            // Append the toast to the body
                            $('body').append(toast);

                            // Show the toast
                            $('.toast').toast('show');

                            setTimeout(function() {
                                $('#' + 'toastnotif').remove();
                            }, 5000);
                        } else {
                            console.log("Error: " + data.text);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Request Error:", status, error);
                }
            });
        }

        $(document).ready(function() {
            setInterval(productStockNotifications, 15000);
            // checkProductStock();
        });
    </script>
    <!-- toast dismissal -->
    <script>
        $(document).ready(function() {
            $('.toast').toast({
                autohide: false
            }).toast('show');
            $('.toast').on('hidden.bs.toast', function() {
                $(this).remove();
            });
        });
    </script>
    <!-- account create/edit -->
    <script>
        $(document).ready(function() {

            //CREATE
            $('#addAccountForm').submit(function(e) {
                e.preventDefault();

                // Serialize form data
                var formData = new FormData(this);

                // Make an AJAX request
                console.log("Before AJAX request");
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/add_account.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Response from server:', response);
                        // Check the response from the server
                        if (response.success) {
                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Account Added!',
                                text: response.originalString + ' has been successfully added.',
                                // text: 'The account has been successfully added.',
                                didClose: () => {
                                    window.location.reload();
                                }
                            });

                            // Optionally, close the modal or perform additional actions
                            $('#addAccountModal').modal('hide');
                        } else {
                            // Handle the case where account addition failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to add the account. Please try again.',
                            });
                        }
                    },
                    error: function() {
                        console.error('Error:', error);
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });

            //UPDATE
            $('form[id^="editAccountForm"]').submit(function(e) {
                e.preventDefault();

                // Extract account ID from the form's ID
                var accountID = this.id.replace('editAccountForm', '');

                // Serialize form data
                var formData = new FormData(this);

                // Make an AJAX request
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/edit_account.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        // Handle the response from the server
                        if (response.success) {
                            $('#editAccountModal' + accountID).modal('hide');


                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.data,
                                didClose: () => {
                                    window.location.reload();
                                }
                            });
                        } else {
                            // Handle the case where the operation failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error,
                            });
                        }
                    },
                    error: function() {
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });
        });
    </script>

    <script>
        //Category
        $(document).ready(function() {

            //CREATE
            $('#addCategoryForm').submit(function(e) {
                e.preventDefault();

                // Serialize form data
                var formData = new FormData(this);

                // Make an AJAX request
                console.log("Before AJAX request");
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/add_category.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log('Response from server:', response);
                        // Check the response from the server
                        if (response.success) {
                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Category Added!',
                                text: response.originalString + ' has been successfully added.',
                                // text: 'The product has been successfully added.',
                                didClose: () => {
                                    window.location.reload();
                                }
                            });

                            // Optionally, close the modal or perform additional actions
                            $('#addCategoryModal').modal('hide');
                        } else {
                            // Handle the case where product addition failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to add the category. Please try again.',
                            });
                        }
                    },
                    error: function() {
                        console.error('Error:', error);
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });

            //UPDATE
            $('form[id^="editCategoryForm"]').submit(function(e) {
                e.preventDefault();

                // Extract product ID from the form's ID
                var categoryID = this.id.replace('editCategoryForm', '');

                // Serialize form data
                var formData = new FormData(this);

                // Make an AJAX request
                $.ajax({
                    type: 'POST',
                    //need to be changed
                    url: './adminconfig/edit_category.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle the response from the server
                        if (response.success) {
                            $('#editCategoryModal' + categoryID).modal('hide');


                            // Display SweetAlert2 success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.data || 'Operation successful.',
                                didClose: () => {
                                    window.location.reload();
                                }
                            });
                        } else {
                            // Handle the case where the operation failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error || 'Failed to perform the operation. Please try again.',
                            });
                        }
                    },
                    error: function() {
                        // Handle AJAX error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.',
                        });
                    }
                });
            });
        });
    </script>

    <script>
        //Delete Category
        $(document).ready(function() {
            // Attach click event to delete buttons
            $('#category_table').on('click', '[id^="deleteButton"]', function() {
                // Extract the product ID from the button ID
                var categoryID = this.id.replace('deleteButton', '');

                // Show the confirmation dialog
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make an AJAX request to delete_product.php
                        $.ajax({
                            type: 'POST',
                            url: './adminconfig/delete_category.php',
                            data: {
                                categoryID: categoryID
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Handle the response from the server
                                if (response.success) {
                                    // Show success message after deletion
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.data || 'Your file has been deleted.',
                                        icon: "success"
                                    }).then(() => {
                                        // Reload the page after dismissing the success message
                                        window.location.reload();
                                    });
                                } else {
                                    // Show error message if deletion fails
                                    Swal.fire({
                                        title: "Error",
                                        text: response.error || 'Failed to delete the file. Please try again.',
                                        icon: "error"
                                    });
                                }
                            },
                            error: function() {
                                // Show error message for AJAX error
                                Swal.fire({
                                    title: "Error",
                                    text: "An error occurred. Please try again.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        //Delete Accounts
        $(document).ready(function() {
            // Attach click event to delete buttons
            $('#accountstable').on('click', '[id^="deleteButton"]', function() {
                // Extract the product ID from the button ID
                var userID = this.id.replace('deleteButton', '');

                // Show the confirmation dialog
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make an AJAX request to delete_product.php
                        $.ajax({
                            type: 'POST',
                            url: './adminconfig/deleteaccount.php',
                            data: {
                                userID: userID
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Handle the response from the server
                                if (response.success) {
                                    // Show success message after deletion
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.data || 'Your file has been deleted.',
                                        icon: "success"
                                    }).then(() => {
                                        // Reload the page after dismissing the success message
                                        window.location.reload();
                                    });
                                } else {
                                    // Show error message if deletion fails
                                    Swal.fire({
                                        title: "Error",
                                        text: response.error || 'Failed to delete the file. Please try again.',
                                        icon: "error"
                                    });
                                }
                            },
                            error: function() {
                                // Show error message for AJAX error
                                Swal.fire({
                                    title: "Error",
                                    text: "An error occurred. Please try again.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>



    <script>
        //Delete customer
        $(document).ready(function() {
            // Attach click event to delete buttons
            $('#customertable').on('click', '[id^="deleteButton"]', function() {
                // Extract the product ID from the button ID
                var userID = this.id.replace('deleteButton', '');

                // Show the confirmation dialog
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make an AJAX request to delete_product.php
                        $.ajax({
                            type: 'POST',
                            url: './adminconfig/deletecustomer.php',
                            data: {
                                userID: userID
                            },
                            dataType: 'json',
                            success: function(response) {
                                // Handle the response from the server
                                if (response.success) {
                                    // Show success message after deletion
                                    Swal.fire({
                                        title: "Deleted!",
                                        text: response.data || 'Your file has been deleted.',
                                        icon: "success"
                                    }).then(() => {
                                        // Reload the page after dismissing the success message
                                        window.location.reload();
                                    });
                                } else {
                                    // Show error message if deletion fails
                                    Swal.fire({
                                        title: "Error",
                                        text: response.error || 'Failed to delete the file. Please try again.',
                                        icon: "error"
                                    });
                                }
                            },
                            error: function() {
                                // Show error message for AJAX error
                                Swal.fire({
                                    title: "Error",
                                    text: "An error occurred. Please try again.",
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

    <!-- Delivery Rider CRUD -->
    <script>
        function ucfirst(str) {
            if (typeof str !== 'string') {
                return str;
            }
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        //Display Riders
        $(document).ready(function() {
            var ridersTable = $('#ridersTable').DataTable({
                ajax: {
                    url: './adminconfig/getall_riders.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id',
                        title: 'ID',
                        render: function(data, type, row) {
                            return 'R' + data;
                        }
                    },
                    {
                        data: null,
                        title: 'Name',
                        render: function(data, type, row) {
                            return ucfirst(row.first_name) + ' ' + ucfirst(row.last_name);
                        }
                    },
                    {
                        data: 'contact_number',
                        title: 'Contact Number'
                    },
                    {
                        data: 'email',
                        title: 'Email'
                    },
                    {
                        data: 'status',
                        title: 'Status',
                        render: function(data, type, row) {
                            if (data === 'active') {
                                return '<span class="text-success fw-bold">' + ucfirst(data) + '</span>';
                            } else if (data === 'terminated') {
                                return '<span class="text-danger fw-bold">' + ucfirst(data) + '</span>';
                            } else {
                                return ucfirst(data);
                            }
                        }
                    },
                    {
                        data: null,
                        title: 'Actions',
                        render: function(data, type, row) {
                            return '<button class="btn btn-primary btn-sm edit-btn" data-id="' + row.id + '">Edit</button> <button class="btn btn-danger btn-sm delete-btn" data-id="' + row.id + '">Delete</button>';
                        }
                    }
                ]
            });

            $('#reloadDataTable').click(function() {
                ridersTable.ajax.reload();
            });

            ////////////////////////////////////////////////////////////////////////////////////////////////////

            // Add Rider
            $('#addRider').submit(function(event) {
                event.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: './adminconfig/add_rider.php',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            $('#contactModal').modal('hide');
                            alert('Congrats! Rider added successfully!');
                            ridersTable.ajax.reload();
                        } else if (response === 'error') {
                            alert('Oops.. There was an error in adding rider. Please try again..');
                        } else if (response === 'contact_number_exists') {
                            alert('Oops.. Contact number is already registered. Please try again.');
                        } else if (response === 'email_exists') {
                            alert('Oops.. Email is already registered. Please try again.');
                        } else if (response === 'license_exists') {
                            alert('Oops.. License number is already registered. Please try again.');
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing your request.');
                    }
                });
            });

            ////////////////////////////////////////////////////////////////////////////////////////////////////

            // Edit Rider
            $(document).on('click', '.edit-btn', function() {
                var editRiderId = $(this).data('id');

                // Fetch rider data using AJAX
                $.ajax({
                    url: './adminconfig/fetch_rider.php',
                    type: 'POST',
                    data: {
                        riderId: editRiderId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const riderData = response.data;

                            $('#editRiderForm #riderId').val(riderData.id);
                            $('#editRiderForm #first_name').val(riderData.first_name);
                            $('#editRiderForm #middle_name').val(riderData.middle_name);
                            $('#editRiderForm #last_name').val(riderData.last_name);
                            $('#editRiderForm #email').val(riderData.email);
                            $('#editRiderForm #date_of_birth').val(riderData.date_of_birth);
                            $('#editRiderForm #address').val(riderData.address);
                            $('#editRiderForm #contact_number').val(riderData.contact_number);
                            $('#editRiderForm #license_number').val(riderData.license_number);
                            $('#editRiderForm #license_expiry_date').val(riderData.license_expiry_date);
                            $('#editRiderForm #vehicle_type').val(riderData.vehicle_type);
                            $('#editRiderForm #vehicle_plate_number').val(riderData.vehicle_plate_number);
                            $('#editRiderForm #status').val(riderData.status);
                            $('#editRiderForm #date_hired').val(riderData.date_hired);
                            $('#editRiderForm #bank_account_number').val(riderData.bank_account_number);
                            $('#editRiderForm #bank_name').val(riderData.bank_name);
                            $('#editRiderForm #emergency_contact_name').val(riderData.emergency_contact_name);
                            $('#editRiderForm #emergency_contact_number').val(riderData.emergency_contact_number);
                            $('#editRiderForm #notes').val(riderData.notes);
                            $('#editRiderModal').modal('show');
                        } else {
                            alert('Error fetching rider data');
                        }
                    },
                    error: function() {
                        alert('An error occurred');
                    }
                });
            });

            // Save edit button click
            $('#saveEdit').click(function() {
                var formData = $('#editRiderForm').serialize();
                $.ajax({
                    type: 'POST',
                    url: './adminconfig/edit_rider.php',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            $('#editRiderModal').modal('hide');
                            alert('Successfully edited rider.');
                            ridersTable.ajax.reload();
                        } else if (response === 'error') {
                            alert('Oops.. There was an error in adding rider. Please try again..');
                        } else if (response === 'contact_number_exists') {
                            alert('Oops.. Contact number is already registered. Please try again.');
                        } else if (response === 'email_exists') {
                            alert('Oops.. Email is already registered. Please try again.');
                        } else if (response === 'license_exists') {
                            alert('Oops.. License number is already registered. Please try again.');
                        }
                    },
                    error: function() {
                        alert('An error occurred');
                    }
                });
            });

            ////////////////////////////////////////////////////////////////////////////////////////////////////

            // Delete Rider
            $(document).on('click', '.delete-btn', function() {
                var deleteRiderId = $(this).data('id');

                $('#deleteRiderModal #riderId').val(deleteRiderId);
                $('#deleteRiderModal').modal('show');

                $('#deleteRider').click(function() {
                    if (confirm("Are you sure you want to delete this rider?")) {
                        $.ajax({
                            type: 'POST',
                            url: './adminconfig/delete_rider.php',
                            data: {
                                riderId: deleteRiderId
                            },
                            success: function(response) {
                                if (response === 'success') {
                                    $('#deleteRiderModal').modal('hide');
                                    alert('Successfully deleted rider from database.');
                                    ridersTable.ajax.reload();
                                } else {
                                    alert('Can\'t delete rider.');
                                }
                            },
                            error: function() {
                                alert('An error occurred');
                            }
                        });
                    }
                });
            });

        });
    </script>
    <!-- /Delivery Rider CRUD -->

    
    </body>

</html>