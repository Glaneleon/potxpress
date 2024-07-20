<div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
    <h2>Products</h2>
        <button type="button" class="my-2 btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
            Add Product
        </button>
        <!-- <button type="button" class="my-2 btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
            Add Product Category
        </button> -->

    <?php
        $productQuery = "SELECT * FROM products";
        $productResult = $conn->query($productQuery);

        if (!$productResult) {
            die("Error fetching products: " . $conn->error);
        }
    ?>

    <div>
        <table class="table table-striped" style="width:100%" id="productstable">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Current Stock</th>
                    <th>Total Sold</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $productResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $product['product_id']; ?></td>
                        <td>
                            
                            <img src=".<?php echo $product['imagefilepath']; ?>" alt="Product Image" class="img-thumbnail mx-3" style="width: 50px; height: 50px;">
                        </td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><?php echo $product['stock_quantity']; ?></td>
                        <td><?php echo $product['sold']; ?></td>
                        <td>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addStockModal<?php echo $product['product_id']; ?>">
                                Add Stock
                            </button>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $product['product_id']; ?>">
                                Edit
                            </button>
                            <button class="btn btn-danger" id="deleteButton<?php echo $product['product_id']; ?>">Delete Item</button>
                        </td>

<!-- edit product modal -->
<div class="modal fade" id="editProductModal<?php echo $product['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product #<?php echo $product['product_id']; ?></h5>
                <img src=".<?php echo $product['imagefilepath']; ?>" alt="Product Image" class="img-thumbnail mx-3" style="width: 50px; height: 50px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm<?php echo $product['product_id']; ?>" enctype="multipart/form-data">
                    <input type="hidden" id="editProductID" name="editProductID" value="<?php echo $product['product_id']; ?>">

                    <div class="form-group mb-3">
                        <label for="productName">Product Name:</label>
                        <input type="text" class="form-control" id="productName" name="productName" value="<?php echo $product['name']; ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="productDescription">Description:</label>
                        <textarea class="form-control" id="productDescription" name="productDescription" required><?php echo $product['description']; ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="productImage">Product Image:</label>
                        <input type="file" class="form-control-file" id="productImage" name="productImage">
                    </div>
                    <div class="form-group mb-3">
                        <label for="productPrice">Price:</label>
                        <input type="number" step="any" class="form-control" id="productPrice" name="productPrice" value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="stockQuantity">Stock Quantity:</label>
                        <input type="number" class="form-control" id="stockQuantity" name="stockQuantity" value="<?php echo $product['stock_quantity']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- add inventory quantity -->
<div class="modal fade" id="addStockModal<?php echo $product['product_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-4">
            <div class="modal-header">
                <h5 class="modal-title" id="addStockModalLabel">Add Stock to <?php echo $product['name']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addStockForm<?php echo $product['product_id']; ?>" enctype="multipart/form-data">
                <input type="hidden" class="form-control" id="addStockProductID" name="addStockProductID" value="<?php echo $product['product_id']; ?>">
                
                <div class="form-group mb-3">
                    <label for="quantity">Quantity:</label>
                    <input type="number"  class="form-control" id="stock_quantity" name="stock_quantity" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Stock</button>
            </form>
        </div>
    </div>
</div>                            
              
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>



<!-- add product modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modified form for AJAX -->
                    <form id="addProductForm" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="productName">Product Name:</label>
                            <input type="text" class="form-control" id="productName" name="productName" required>
                        </div>
                        <!-- product category -->
                        <div class="form-group mb-3">
                            <label for="productCategory">Category:</label>
                            <select class="form-select" aria-labelname="Default select example" id="productCategory" name="productCategory">
                                <option selected>Choose Category</option>
                                <option value='Regular Pots'>Regular Pots</option>
                                <option value='Painted Pots'>Painted Pots</option>
                                <option value='Unique Pots'>Unique Pots</option>
                                <option value='Others'>Others</option>
                            </select>
                            <!-- <textarea class="form-control" id="productCategory" name="productCategory" required></textarea> -->
                            
                        </div>
                        <div class="form-group mb-3">
                            <label for="productDescription">Description:</label>
                            <textarea class="form-control" id="productDescription" name="productDescription" required></textarea>
                        </div>
                        <!-- Image upload button -->
                        <div class="form-group mb-3">
                            <label for="productImage">Product Image:</label>
                            <input type="file" class="form-control-file" id="productImage" name="productImage">
                        </div>
                        <div class="form-group mb-3">
                            <label for="productPrice">Price:</label>
                            <input type="number" step="any" class="form-control" id="productPrice" name="productPrice" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="stockQuantity">Stock Quantity:</label>
                            <input type="number" class="form-control" id="stockQuantity" name="stockQuantity" required>
                        </div>
                        <!-- Add more fields as needed -->
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
</div>

</div>

