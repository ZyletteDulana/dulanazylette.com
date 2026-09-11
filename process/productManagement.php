<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/databaseConnector.php";

/**
 * Utility function to set session notification and redirect
 */
function notifyAndRedirect(string $type, string $title, string $message, string $location): void {
    $_SESSION['notification'] = [
        'type'    => $type,
        'title'   => $title,
        'message' => $message
    ];
    header("Location: " . $location);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = $_SESSION["userId"] ?? $_SESSION["user_id"] ?? null;

    // ----------------------------------------------------
    // 1. ADD NEW PRODUCT
    // ----------------------------------------------------
    if (isset($_POST["addNewProduct"])) {
        $productType = trim($_POST["productType"] ?? 'Single');
        $productName = trim($_POST["productName"] ?? '');
        $rawPrice    = trim($_POST["productPrice"] ?? '');

        $productPrice = (is_numeric($rawPrice) && (float)$rawPrice >= 0) ? (float)$rawPrice : 0.00;

        $rawItemIds      = $_POST["itemId"] ?? $_POST["itemIds"] ?? [];
        $rawItemQuantity = $_POST["itemQuantity"] ?? [];

        $file          = $_FILES["productImage"] ?? null;
        $fileName      = $file["name"] ?? '';
        $fileTmp       = $file["tmp_name"] ?? '';
        $fileError     = $file["error"] ?? UPLOAD_ERR_NO_FILE;
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (empty($userId)) {
            notifyAndRedirect("error", "Session Expired", "Your session has expired. Please log in again.", "../index.php");
        } 
        
        if (empty($productType) || empty($productName)) {
            notifyAndRedirect("error", "Invalid Input", "Please enter a product name and select a product type.", "../admin/dashboard.php?page=add-product");
        }

        try {
            $conn->begin_transaction();

            // Handle Image Upload
            $uniqueFileName = null;
            if ($fileError === UPLOAD_ERR_OK && in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $uploadDir = "../uploads/product-images/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $uniqueFileName = "product_image_" . time() . "_" . uniqid() . "." . $fileExtension;
                move_uploaded_file($fileTmp, $uploadDir . $uniqueFileName);
            }

            // Check for duplicate product names
            $productValidator = strtolower($productName);
            $validateProduct  = $conn->prepare("SELECT product_id FROM products_table WHERE LOWER(product_name) = ? LIMIT 1");
            $validateProduct->bind_param("s", $productValidator);
            $validateProduct->execute();

            if ($validateProduct->get_result()->num_rows > 0) {
                $conn->rollback();
                notifyAndRedirect("error", "Product Exists", "Product name already exists!", "../admin/dashboard.php?page=add-product");
            }

            // Insert Base Product
            $addNewProduct = $conn->prepare("INSERT INTO products_table (product_type, product_name, product_price, product_image, added_by) VALUES (?, ?, ?, ?, ?)");
            $addNewProduct->bind_param("ssdsi", $productType, $productName, $productPrice, $uniqueFileName, $userId);
            $addNewProduct->execute();

            $productId = $conn->insert_id;

            // Insert Associated Items
            $itemIds      = is_array($rawItemIds) ? array_values($rawItemIds) : [$rawItemIds];
            $itemQuantity = is_array($rawItemQuantity) ? array_values($rawItemQuantity) : [$rawItemQuantity];

            if (!empty($itemIds)) {
                $insertProductItem = $conn->prepare("INSERT INTO product_items_table (product_id, item_id, quantity) VALUES (?, ?, ?)");

                foreach ($itemIds as $index => $rawItemId) {
                    $itemId = intval($rawItemId);
                    $qty    = isset($itemQuantity[$index]) ? intval($itemQuantity[$index]) : 1;
                    $qty    = ($qty > 0) ? $qty : 1;

                    if ($itemId > 0) {
                        $insertProductItem->bind_param("iii", $productId, $itemId, $qty);
                        $insertProductItem->execute();
                    }
                }
                $insertProductItem->close();
            }

            $conn->commit();
            notifyAndRedirect("success", "Product Added", "Product added successfully!", "../admin/dashboard.php?page=products-list");

        } catch (Throwable $e) {
            $conn->rollback();
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=add-product");
        }
    }

    // ----------------------------------------------------
    // 2. UPDATE PRODUCT DETAILS
    // ----------------------------------------------------
    else if (isset($_POST["updateProductDetails"])) {
        $productId   = intval($_POST["productId"] ?? 0);
        $productName = trim($_POST["productName"] ?? '');
        $productType = trim($_POST["productType"] ?? 'Single');
        $rawPrice    = trim($_POST["productPrice"] ?? '');

        $productPrice = (is_numeric($rawPrice) && (float)$rawPrice >= 0) ? (float)$rawPrice : 0.00;
        $encodedId    = base64_encode((string)$productId);

        if (empty($userId)) {
            notifyAndRedirect("error", "Session Expired", "Your session has expired. Please log in again.", "../index.php");
        }
        
        if (empty($productName) || $productId <= 0) {
            notifyAndRedirect("error", "Invalid Input", "Invalid product details.", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }

        try {
            $conn->begin_transaction();

            $productValidator = strtolower($productName);
            $validateProduct  = $conn->prepare("SELECT product_id FROM products_table WHERE LOWER(product_name) = ? AND product_id != ? LIMIT 1");
            $validateProduct->bind_param("si", $productValidator, $productId);
            $validateProduct->execute();

            if ($validateProduct->get_result()->num_rows > 0) {
                $conn->rollback();
                notifyAndRedirect("error", "Product Exists", "Product name already exists!", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
            }

            $updateProduct = $conn->prepare("UPDATE products_table SET product_type = ?, product_name = ?, product_price = ? WHERE product_id = ?");
            $updateProduct->bind_param("ssdi", $productType, $productName, $productPrice, $productId);
            $updateProduct->execute();

            $conn->commit();
            notifyAndRedirect("success", "Product Updated", "Product updated successfully!", "../admin/dashboard.php?page=edit-product&productId=$encodedId");

        } catch (Throwable $e) {
            $conn->rollback();
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }
    }

    // ----------------------------------------------------
    // 3. ADD PRODUCT ITEM (INCLUSION)
    // ----------------------------------------------------
    else if (isset($_POST["addProductItem"])) {
        $productId = intval($_POST["productId"] ?? 0);
        $itemId    = intval($_POST["itemId"] ?? 0);
        $quantity  = intval($_POST["itemQuantity"] ?? 1);
        $encodedId = base64_encode((string)$productId);

        if ($productId <= 0 || $itemId <= 0 || $quantity <= 0) {
            notifyAndRedirect("error", "Invalid Input", "Please select a valid item and quantity.", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }

        try {
            $checkItem = $conn->prepare("SELECT product_item_id, quantity FROM product_items_table WHERE product_id = ? AND item_id = ? LIMIT 1");
            $checkItem->bind_param("ii", $productId, $itemId);
            $checkItem->execute();
            $result = $checkItem->get_result();

            if ($result->num_rows > 0) {
                $row       = $result->fetch_assoc();
                $newQty    = $row['quantity'] + $quantity;
                $updateStmt = $conn->prepare("UPDATE product_items_table SET quantity = ? WHERE product_item_id = ?");
                $updateStmt->bind_param("ii", $newQty, $row['product_item_id']);
                $updateStmt->execute();
            } else {
                $insertStmt = $conn->prepare("INSERT INTO product_items_table (product_id, item_id, quantity) VALUES (?, ?, ?)");
                $insertStmt->bind_param("iii", $productId, $itemId, $quantity);
                $insertStmt->execute();
            }

            notifyAndRedirect("success", "Item Added", "Item inclusion added successfully!", "../admin/dashboard.php?page=edit-product&productId=$encodedId");

        } catch (Throwable $e) {
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }
    }

    // ----------------------------------------------------
    // 4. UPDATE ITEM QUANTITY
    // ----------------------------------------------------
    else if (isset($_POST["updateItemQuantity"])) {
        $productId     = intval($_POST["productId"] ?? 0);
        $productItemId = intval($_POST["productItemId"] ?? 0);
        $quantity      = intval($_POST["itemQuantity"] ?? 1);
        $encodedId     = base64_encode((string)$productId);

        if ($productId <= 0 || $productItemId <= 0 || $quantity <= 0) {
            notifyAndRedirect("error", "Invalid Input", "Invalid quantity value.", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }

        try {
            $updateQty = $conn->prepare("UPDATE product_items_table SET quantity = ? WHERE product_item_id = ? AND product_id = ?");
            $updateQty->bind_param("iii", $quantity, $productItemId, $productId);
            $updateQty->execute();

            notifyAndRedirect("success", "Quantity Updated", "Item quantity updated successfully!", "../admin/dashboard.php?page=edit-product&productId=$encodedId");

        } catch (Throwable $e) {
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }
    }

    // ----------------------------------------------------
    // 5. REMOVE PRODUCT ITEM
    // ----------------------------------------------------
    else if (isset($_POST["removeItem"])) {
        $productId     = intval($_POST["productId"] ?? 0);
        $productItemId = intval($_POST["productItemId"] ?? 0);
        $encodedId     = base64_encode((string)$productId);

        if ($productId <= 0 || $productItemId <= 0) {
            notifyAndRedirect("error", "Invalid Request", "Could not remove item.", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }

        try {
            $removeItem = $conn->prepare("DELETE FROM product_items_table WHERE product_item_id = ? AND product_id = ?");
            $removeItem->bind_param("ii", $productItemId, $productId);
            $removeItem->execute();

            notifyAndRedirect("success", "Item Removed", "Item removed from product successfully!", "../admin/dashboard.php?page=edit-product&productId=$encodedId");

        } catch (Throwable $e) {
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }
    }

    // ----------------------------------------------------
    // 6. UPDATE PRODUCT PHOTO
    // ----------------------------------------------------
    else if (isset($_POST["updateProductPhoto"])) {
        $productId = intval($_POST["productId"] ?? 0);
        $encodedId = base64_encode((string)$productId);

        $file          = $_FILES["productImage"] ?? null;
        $fileName      = $file["name"] ?? '';
        $fileTmp       = $file["tmp_name"] ?? '';
        $fileError     = $file["error"] ?? UPLOAD_ERR_NO_FILE;
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($productId <= 0 || $fileError !== UPLOAD_ERR_OK || !in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            notifyAndRedirect("error", "Upload Error", "Please upload a valid image file (JPG, PNG, WEBP, GIF).", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }

        try {
            $uploadDir = "../uploads/product-images/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uniqueFileName = "product_image_" . time() . "_" . uniqid() . "." . $fileExtension;
            $uploadPath     = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $updatePhoto = $conn->prepare("UPDATE products_table SET product_image = ? WHERE product_id = ?");
                $updatePhoto->bind_param("si", $uniqueFileName, $productId);
                $updatePhoto->execute();

                notifyAndRedirect("success", "Photo Updated", "Product image updated successfully!", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
            } else {
                notifyAndRedirect("error", "Upload Failed", "Failed to save the image file to target directory.", "../admin/dashboard.php?page=edit-product&productId=$encodedId");
            }

        } catch (Throwable $e) {
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=edit-product&productId=$encodedId");
        }
    }

    // ----------------------------------------------------
    // 7. DELETE PRODUCT
    // ----------------------------------------------------
    else if (isset($_POST["deleteProduct"])) {
        $productId = intval($_POST["productId"] ?? 0);

        if (empty($userId) || $productId <= 0) {
            notifyAndRedirect("error", "Invalid Input", "Invalid request!", "../admin/dashboard.php?page=products-list");
        }

        try {
            $conn->begin_transaction();

            // Explicitly delete items mapping first to ensure integrity
            $deleteItems = $conn->prepare("DELETE FROM product_items_table WHERE product_id = ?");
            $deleteItems->bind_param("i", $productId);
            $deleteItems->execute();

            // Delete product parent entry
            $deleteProduct = $conn->prepare("DELETE FROM products_table WHERE product_id = ?");
            $deleteProduct->bind_param("i", $productId);
            $deleteProduct->execute();

            $conn->commit();
            notifyAndRedirect("success", "Product Deleted", "Product deleted successfully!", "../admin/dashboard.php?page=products-list");

        } catch (Throwable $e) {
            $conn->rollback();
            notifyAndRedirect("error", "Database Error", $e->getMessage(), "../admin/dashboard.php?page=products-list");
        }
    }
}