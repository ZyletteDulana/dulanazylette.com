<?php
    include_once "../includes/accessChecker.php";
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title"><?php echo htmlspecialchars($pageTitles[$pageName] ?? 'Add Product'); ?></h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="../process/productManagement.php" method="POST" autocomplete="off" data-toggle="validator" enctype="multipart/form-data">

                            <h5 class="mb-3">Product Information</h5>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Product Image</label>
                                    
                                    <!-- Image Preview -->
                                    <div class="mb-3 text-center">
                                        <img id="imagePreview"
                                             src="<?php echo htmlspecialchars($productsPictureFolderPath . "default-product-image.png"); ?>"
                                             alt="Product Preview"
                                             class="img-thumbnail"
                                             style="max-height: 200px; width: 200px; object-fit: cover;">
                                    </div>

                                    <!-- File Input -->
                                    <div class="custom-file">
                                        <input type="file"
                                               class="custom-file-input"
                                               id="imageInput"
                                               name="productImage"
                                               accept=".jpg, .jpeg, .png, .gif">
                                        <label class="custom-file-label" for="imageInput" id="fileName">Choose File</label>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="productType">
                                        Product Type <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="productType">Type</label>
                                        </div>
                                        <select id="productType" class="custom-select" name="productType" required>
                                            <option value="" selected disabled>Select Product Type</option>
                                            <option value="Single">Single</option>
                                            <option value="Combo">Combo</option>
                                            <option value="Clubhouse">Clubhouse</option>
                                            <option value="Add-ons">Add-ons</option>
                                            <option value="Burgers">Burgers</option>
                                            <option value="Soda Drinks">Soda Drinks</option>
                                            <option value="Burgers & Snacks">Burgers & Snacks</option>
                                            <option value="Canton">Canton</option>
                                            <option value="Rice meals">Rice meals</option>
                                            <option value="Shakes">Shakes</option>
                                            <option value="Cold drinks">Cold drinks</option>
                                            <option value="Hot drinks">Hot drinks</option>
                                            <option value="others">others</option>
                                            <option value="Siomai & Lumpia">Siomai & Lumpia</option>
                                            <option value="Ingredients">Ingredients</option>
                                            <option value="Meal">Meal</option>
                                            <option value="Beverage">Beverage</option>
                                            <option value="Packaging">Packaging</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="productName"
                                               class="form-control"
                                               placeholder="Enter Product Name"
                                               data-errors="Please Enter Product Name."
                                               required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="form-label">Product Price</label>
                                        <input type="number"
                                               min="0"
                                               step="0.01"
                                               name="productPrice"
                                               class="form-control"
                                               placeholder="Enter Product Price">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-2">
                                    <h5 class="mb-3">Items in Product</h5>

                                    <div class="row g-3" id="items-container">
                                        <div class="col-md-4 item-row mb-3">
                                            <div class="card p-3 shadow-sm d-flex flex-column h-100 mb-0">
                                                <div class="mb-2">
                                                    <label class="form-label">Select Item</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text">Items</label>
                                                        </div>
                                                        <select class="custom-select items-select" name="itemId[]" required>
                                                            <option value="" selected disabled>Select Item</option>
                                                            <?php
                                                            $items = $conn->prepare("SELECT item_id, item_name FROM inventory_table ORDER BY item_name ASC");
                                                            $items->execute();
                                                            $itemsQueryResult = $items->get_result();

                                                            while ($itemData = $itemsQueryResult->fetch_assoc()) {
                                                            ?>
                                                                <option value="<?php echo (int)$itemData["item_id"]; ?>">
                                                                    <?php echo htmlspecialchars($itemData["item_name"]); ?>
                                                                </option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between mt-2">
                                                    <div class="flex-grow-1 mr-2">
                                                        <label class="form-label">Quantity</label>
                                                        <input type="number" name="itemQuantity[]" value="1" min="1" class="form-control items-quantity" required>
                                                    </div>

                                                    <button type="button" class="btn text-danger align-self-end remove-item" title="Remove Item">
                                                        <i class="bi bi-trash fs-5"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary mb-3 mt-2" id="add-item">
                                        <i class="bi bi-plus-lg mr-1"></i> Add Another Item
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success mr-2" name="addNewProduct">
                                    <i class="bi bi-plus-lg mr-1"></i> Add Product
                                </button>

                                <button type="reset" class="btn btn-danger">
                                    <i class="bi bi-x-lg mr-1"></i> Clear
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('items-container');
        const addButton = document.getElementById('add-item');

        // Dynamically append extra item fields
        addButton.addEventListener('click', function() {
            const firstRow = container.querySelector('.item-row');
            if (!firstRow) return;

            const clonedRow = firstRow.cloneNode(true);
            clonedRow.querySelector('select').selectedIndex = 0;
            clonedRow.querySelector('.items-quantity').value = 1;

            container.appendChild(clonedRow);
        });

        // Delegate click for remove item button (supports clicking on icon directly)
        container.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-item');
            if (removeBtn) {
                if (container.querySelectorAll('.item-row').length > 1) {
                    removeBtn.closest('.item-row').remove();
                }
            }
        });

        // Image file preview handler
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const fileNameLabel = document.getElementById('fileName');

        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const [file] = this.files;
                if (file) {
                    imagePreview.src = URL.createObjectURL(file);
                    fileNameLabel.textContent = file.name;
                }
            });
        }
    });
</script>