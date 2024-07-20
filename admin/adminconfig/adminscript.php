    <!-- fragment url -->
    <script>
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
    </script>
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
    <script>
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
    </script>
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
        //Display Riders
        $(document).ready(function() {
            var ridersTable = $('#ridersTable').DataTable({
                ajax: {
                    url: './adminconfig/getall_riders.php',
                    dataSrc: ''
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'contact_number'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<button class="btn btn-primary btn-sm edit-btn" data-id="' + data.id + '">Edit</button> <button class="btn btn-danger btn-sm delete-btn" data-id="' + data.id + '">Delete</button>';
                        }
                    }
                ]
            });

            $('#reloadDataTable').click(function() {
                ridersTable.ajax.reload();
            });

            ////////////////////////////////////////////////////////////////////////////////////////////////////

            // Add Rider
            $('#contactForm').submit(function(event) {
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
                            $('#editRiderModal #riderId').val(response.data.id);
                            $('#editRiderModal #fullName').val(response.data.name);
                            $('#editRiderModal #contactNumber').val(response.data.contact_number);
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
                            ridersTable.ajax.reload();
                            // Update the DataTable row or reload the table
                        } else {
                            alert('Error updating rider');
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
