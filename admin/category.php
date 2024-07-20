<div class="tab-pane fade" id="category" role="tabpanel" aria-labelledby="category-tab">
    <h2>Category</h2>

    <button type="button" class="my-2 btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            Add Product Category
    </button>

    <?php
        $categoryQuery = "SELECT * FROM category";
        $categoryResult = $conn->query($categoryQuery);

        if (!$productResult) {
            die("Error fetching category: " . $conn->error);
        }
    ?>

    <div class="table-responsive">
        <table class="table" id="category_table">
            <thead>
                <th>Category ID</th>
                <th>Category</th>
                <th>Action</th>
            </thead>
            <tbody>
            <?php while ($category = $categoryResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $category['category_id']; ?></td>
                        <td><?php echo $category['category_name']; ?></td>
                        <td>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?php echo $category['category_id']; ?>">
                                Edit
                            </button>
                            <button class="btn btn-danger" id="deleteButton<?php echo $category['category_id']; ?>">Delete Item</button>
                        </td>

<!-- edit product modal -->
<div class="modal fade" id="editCategoryModal<?php echo $category['category_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Cateogry #<?php echo $category['category_id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm<?php echo $category['category_id']; ?>" enctype="multipart/form-data">
                    <input type="hidden" id="editCategoryID" name="editCategoryID" value="<?php echo $category['category_id']; ?>">

                    <div class="form-group mb-3">
                        <label for="productName">Category Name:</label>
                        <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo $category['category_name']; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

                      

                    </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    
<!-- add product modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Modified form for AJAX -->
                    <form id="addCategoryForm" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="categoryName">Category:</label>
                            <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                        </div>
                        
                        <!-- Add more fields as needed -->
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
</div>
    


</div>