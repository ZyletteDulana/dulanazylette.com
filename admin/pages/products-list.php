<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title"><?php echo htmlspecialchars($pageTitles[$pageName] ?? 'Products List'); ?></h4>
                        </div>
                        <?php if ($userType === "Admin"): ?>
                            <a href="dashboard.php?page=add-product" class="btn btn-success add-list">
                                <i class="las la-plus mr-3"></i> Add Product
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="table-responsive rounded mb-3">
                                <table class="data-tables table mb-0 tbl-server-info text-center custom-table productsTable">
                                    <thead class="bg-white text-uppercase">
                                        <tr class="ligth ligth-data">
                                            <th class="export-ignore">Product</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Price</th>
                                            <th>Solds</th>
                                            <th>Product Items</th>
                                            <th>Added At</th>
                                            <th>Added By</th>
                                            <?php if ($userType === "Admin"): ?>
                                                <th class="export-ignore">Action</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>

                                    <tbody class="light-body text-center">
                                        <?php
                                        $getProductsList = $conn->prepare("
                                            SELECT
                                                p.product_id,
                                                p.product_image,
                                                p.product_name,
                                                p.product_type,
                                                p.product_price,
                                                p.product_solds,
                                                p.added_at,
                                                GROUP_CONCAT(i.item_name SEPARATOR ', ') AS product_items,
                                                u.first_name AS added_by_name
                                            FROM products_table p
                                            LEFT JOIN product_items_table pi ON p.product_id = pi.product_id
                                            LEFT JOIN inventory_table i ON pi.item_id = i.item_id
                                            LEFT JOIN users_table u ON p.added_by = u.user_id
                                            GROUP BY p.product_id
                                            ORDER BY p.product_price DESC
                                        ");
                                        $getProductsList->execute();
                                        $productsQueryResult = $getProductsList->get_result();

                                        while ($productData = $productsQueryResult->fetch_assoc()) {
                                            $productId    = (int)$productData["product_id"];
                                            $encodedId    = base64_encode((string)$productId);
                                            $productName  = $productData["product_name"] ?? 'N/A';
                                            $productImage = !empty($productData["product_image"]) ? $productData["product_image"] : "default-product-image.png";
                                            $productItems = !empty($productData["product_items"]) ? $productData["product_items"] : "No linked items";
                                            $addedByName  = !empty($productData["added_by_name"]) ? $productData["added_by_name"] : "System";
                                        ?>
                                            <tr>
                                                <td class="export-ignore">
                                                    <img src="<?php echo htmlspecialchars($productsPictureFolderPath . $productImage); ?>" 
                                                         alt="<?php echo htmlspecialchars($productName); ?>" 
                                                         class="img-fluid rounded avatar-75 mr-3">
                                                </td>

                                                <td class="fw-bold">
                                                    <?php echo htmlspecialchars($productName); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($productData["product_type"] ?? 'Single'); ?>
                                                </td>

                                                <td class="export-currency">
                                                    ₱<?php echo htmlspecialchars(amountFormatter($productData["product_price"] ?? 0)); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($productData["product_solds"] ?? 0); ?>
                                                </td>

                                                <td class="text-truncate"
                                                    style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="<?php echo htmlspecialchars($productItems); ?>">
                                                    <?php echo htmlspecialchars($productItems); ?>
                                                </td>

                                                <td class="export-date">
                                                    <span class="d-none"><?php echo htmlspecialchars($productData["added_at"]); ?></span>
                                                    <?php echo htmlspecialchars(formatTimestamp($productData["added_at"])); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($addedByName); ?>
                                                </td>

                                                <?php if ($userType === "Admin"): ?>
                                                    <td class="export-ignore">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <a class="btn btn-warning btn-sm px-2 mr-2"
                                                               data-toggle="tooltip"
                                                               data-placement="top"
                                                               title="Edit Product"
                                                               href="dashboard.php?page=product-details&productId=<?php echo htmlspecialchars($encodedId); ?>">
                                                                <i class="bi bi-pencil-square mr-0"></i>
                                                            </a>

                                                            <form action="../process/productManagement.php" method="POST" id="deleteProductForm_<?php echo $productId; ?>">
                                                                <input type="hidden" name="deleteProduct" value="1">
                                                                <input type="hidden" name="productName" value="<?php echo htmlspecialchars($productName); ?>">
                                                                <input type="hidden" name="productId" value="<?php echo $productId; ?>">

                                                                <button type="submit"
                                                                        class="btn btn-danger btn-sm px-2"
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="Delete Product"
                                                                        onclick="confirmAction(
                                                                            event,
                                                                            this.form,
                                                                            'deleteProductForm_<?php echo $productId; ?>',
                                                                            'Delete Product: <?php echo htmlspecialchars(addslashes($productName)); ?>?',
                                                                            'warning',
                                                                            'Are you sure you want to delete this product and its data?',
                                                                            'Delete Product',
                                                                            '#E08DB4'
                                                                        )">
                                                                    <i class="ri-delete-bin-line mr-0"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php } ?>
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