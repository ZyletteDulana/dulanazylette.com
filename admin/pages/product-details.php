<?php
include_once "../includes/accessChecker.php";

// 1. Safe Query Parameter & Type Casting Check
$rawProductId = $_GET["productId"] ?? null;
$decodedId = $rawProductId ? base64_decode($rawProductId) : false;
$productId = ($decodedId !== false && is_numeric($decodedId)) ? (int)$decodedId : null;

if (!$productId) {
    displayNotification("error", "Invalid Product", "Invalid product!");
    header("Location: ../dashboard.php?page=products-list");
    exit();
}

// 2. Fetch Product Data
$getProductData = $conn->prepare("SELECT * FROM products_table WHERE product_id = ? LIMIT 1");
$getProductData->bind_param("i", $productId);
$getProductData->execute();
$productQueryResult = $getProductData->get_result();

if ($productQueryResult->num_rows === 1) {
    $productData = $productQueryResult->fetch_object();
    $productImage = $productData->product_image ?? "default-product-image.png";
    $productType = $productData->product_type ?? '';
} else {
    displayNotification("error", "Invalid Product", "Invalid product!");
    header("Location: ../dashboard.php?page=products-list");
    exit();
}

// Pre-fetch product inclusions to decouple logic from HTML loop rendering
$getProductItems = $conn->prepare("SELECT 
        pi.product_item_id,
        pi.product_id,
        pi.item_id,
        pi.quantity,
        it.item_name
    FROM product_items_table pi
    INNER JOIN inventory_table it ON it.item_id = pi.item_id
    WHERE pi.product_id = ?
    ORDER BY pi.quantity DESC");
$getProductItems->bind_param("i", $productId);
$getProductItems->execute();
$productItemsList = $getProductItems->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">

                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title d-flex justify-content-between align-items-center">
                            <h4 class="card-title"><?php echo htmlspecialchars($pageTitles[$pageName] ?? 'Edit Product'); ?></h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <h5 class="mb-3">Product Information</h5>

                        <div class="row">
                            <!-- Image Update Section -->
                            <div class="col-md-6">
                                <form action="../process/productManagement.php" method="POST" autocomplete="off" enctype="multipart/form-data">
                                    <input type="hidden" name="productName" value="<?php echo htmlspecialchars($productData->product_name); ?>">
                                    <input type="hidden" name="productId" value="<?php echo htmlspecialchars($productId); ?>">

                                    <div class="mb-3">
                                        <label class="form-label">Product Image</label>
                                        <div class="mb-3 text-center">
                                            <img id="imagePreview"
                                                 src="<?php echo htmlspecialchars($productsPictureFolderPath . $productImage); ?>"
                                                 alt="Product Preview"
                                                 style="max-height:200px; width:200px; height:200px; object-fit:cover;">
                                        </div>

                                        <div class="custom-file mb-1">
                                            <input type="file" class="custom-file-input" id="imageInput" name="productImage" accept=".jpg, .jpeg, .png, .gif" required>
                                            <div class="input-group-prepend">
                                                <label class="custom-file-label" for="imageInput" id="fileName">Choose File</label>
                                            </div>
                                        </div>

                                        <button class="mt-2 btn btn-success w-100" type="submit" name="updateProductPhoto">
                                            <i class="bi bi-pencil-square"></i> Update Product Photo
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Product Details Form -->
                            <div class="col-md-6">
                                <form action="../process/productManagement.php" method="POST" autocomplete="off" data-toggle="validator">
                                    <input type="hidden" name="productId" value="<?php echo htmlspecialchars($productId); ?>">

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="productType">
                                                Product Type <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text" for="productType">Type</label>
                                                </div>
                                                <select id="productType" class="custom-select" name="productType" required>
                                                    <?php
                                                    $categories = [
                                                        "Single", "Combo", "Clubhouse", "Add-ons", "Burgers", 
                                                        "Soda Drinks", "Burgers & Snacks", "Canton", "Rice meals", 
                                                        "Shakes", "Cold drinks", "Hot drinks", "others", 
                                                        "Siomai & Lumpia", "Ingredients", "Meal", "Beverage", "Packaging"
                                                    ];
                                                    foreach ($categories as $cat): ?>
                                                        <option value="<?php echo $cat; ?>" <?php echo ($productType === $cat) ? 'selected' : ''; ?>>
                                                            <?php echo $cat; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                                <input type="text" name="productName" class="form-control" 
                                                       placeholder="Enter Product Name" 
                                                       value="<?php echo htmlspecialchars($productData->product_name); ?>" required />
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group mb-0">
                                                <label class="form-label">Product Price <span class="text-danger">*</span></label>
                                                <input type="number" min="0" step="0.01" name="productPrice" class="form-control" 
                                                       placeholder="Enter Product Price" 
                                                       value="<?php echo htmlspecialchars($productData->product_price); ?>" required />
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <button class="mt-2 btn btn-success w-100" type="submit" name="updateProductDetails">
                                                <i class="bi bi-pencil-square"></i> Update Product Details
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Product Inclusions Table -->
                            <div class="col-md-12 mt-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Product Inclusions</h4>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductItemModal">
                                            <i class="las la-plus mr-2"></i> Add Item
                                        </button>
                                    </div>

                                    <div class="card-body">
                                        <div class="table-responsive rounded">
                                            <table class="table text-center custom-table align-middle">
                                                <thead class="bg-white text-uppercase">
                                                    <tr>
                                                        <th>Item Name</th>
                                                        <th>Item Quantity</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($productItemsList)): ?>
                                                        <?php foreach ($productItemsList as $item): ?>
                                                            <tr>
                                                                <td class="fw-bold"><?php echo htmlspecialchars($item["item_name"]); ?></td>
                                                                <td><?php echo htmlspecialchars($item["quantity"]); ?></td>
                                                                <td>
                                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                                        <button type="button" class="btn btn-warning btn-sm px-2" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#updateItem<?php echo $item["product_item_id"]; ?>" 
                                                                                title="Edit Quantity">
                                                                            <i class="bi bi-pencil-square"></i>
                                                                        </button>

                                                                        <form action="../process/productManagement.php" method="POST" id="removeItemForm_<?php echo $item["product_item_id"]; ?>">
                                                                            <input type="hidden" name="removeItem" value="1">
                                                                            <input type="hidden" name="productName" value="<?php echo htmlspecialchars($productData->product_name); ?>">
                                                                            <input type="hidden" name="productItemId" value="<?php echo htmlspecialchars($item["product_item_id"]); ?>">
                                                                            <input type="hidden" name="productId" value="<?php echo htmlspecialchars($productId); ?>">

                                                                            <button type="button" class="btn btn-danger btn-sm px-2"
                                                                                    onclick="confirmAction(
                                                                                        event, 
                                                                                        this.form, 
                                                                                        'removeItemForm_<?php echo $item['product_item_id']; ?>', 
                                                                                        'Remove Item: <?php echo addslashes(htmlspecialchars($item['item_name'])); ?>?', 
                                                                                        'warning', 
                                                                                        'Are you sure you want to remove this item?', 
                                                                                        'Remove Item', 
                                                                                        '#E08DB4'
                                                                                    )">
                                                                                <i class="bi bi-dash-circle"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-muted">No items assigned to this product yet.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Update Quantities -->
<?php foreach ($productItemsList as $item): ?>
    <div class="modal fade" id="updateItem<?php echo $item["product_item_id"]; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Item: <?php echo htmlspecialchars($item["item_name"]); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="../process/productManagement.php" method="POST" data-toggle="validator">
                    <input type="hidden" name="productName" value="<?php echo htmlspecialchars($productData->product_name); ?>">
                    <input type="hidden" name="productItemId" value="<?php echo htmlspecialchars($item["product_item_id"]); ?>">
                    <input type="hidden" name="productId" value="<?php echo htmlspecialchars($productId); ?>">
                    <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($item["item_id"]); ?>">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Item Quantity</label>
                            <input type="number" name="itemQuantity" class="form-control" min="1" step="1" 
                                   value="<?php echo htmlspecialchars($item["quantity"]); ?>" required />
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="updateItemQuantity">
                            <i class="bi bi-pencil"></i> Update Quantity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal: Add Product Item -->
<div class="modal fade" id="addProductItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item For: <?php echo htmlspecialchars($productData->product_name); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../process/productManagement.php" method="POST" data-toggle="validator">
                <input type="hidden" name="productName" value="<?php echo htmlspecialchars($productData->product_name); ?>">
                <input type="hidden" name="productId" value="<?php echo htmlspecialchars($productId); ?>">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="itemsList">Select Item</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="itemsList">Items</label>
                            </div>
                            <select id="itemsList" class="custom-select" name="itemId" required>
                                <?php
                                $items = $conn->query("SELECT item_id, item_name FROM inventory_table ORDER BY item_name");
                                while ($itemData = $items->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $itemData["item_id"]; ?>">
                                        <?php echo htmlspecialchars($itemData["item_name"]); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="form-label">Item Quantity</label>
                        <input type="number" name="itemQuantity" class="form-control" min="1" step="1" value="1" required />
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="addProductItem">
                        <i class="bi bi-plus-lg"></i> Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>