<?php
    require_once "../config/databaseConnector.php";
    require_once "../config/functions.php";
    require_once "../config/inputValidators.php";
    require_once "../config/notifications.php";
    require_once "../config/formatter.php";
    require_once "../includes/activityLogger.php";

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addNewTransaction"])) {
        
        $userId = $_SESSION["userId"];
        $productIds = $_POST["productId"];
        $productsQuantities = $_POST["quantity"];

        if(empty($userId) || !$productIds || !$productsQuantities) {
            displayNotification("error", "Invalid Transaction", "Invalid transaction data! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-transaction");
            exit();
        }

        if(count($productIds) <= 0 || count($productsQuantities) <= 0) {
            displayNotification("error", "Invalid Transaction", "Invalid transaction data! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-transaction");
            exit();
        }

        try {
            $conn->begin_transaction();

            $grandTotal = 0;

            // Validate if products exists
            foreach($productIds as $productId) {
                $validateProduct = $conn->prepare("SELECT * FROM products_table WHERE product_id = ? LIMIT 1");
                $validateProduct->bind_param("i", $productId);
                $validateProduct->execute();

                $productQueryResult = $validateProduct->get_result();

                if($productQueryResult->num_rows !== 1) {
                    displayNotification("error", "Invalid Product", "Product not found! Please try again.");
                    header("Location: ../admin/dashboard.php?page=add-transaction");
                    exit();
                }

                else {
                    $productQuantity = isset($productsQuantities[$productId]) && $productsQuantities[$productId] > 0
                                        ? (int)$productsQuantities[$productId]
                                        : 1;

                    $productData = $productQueryResult->fetch_array();
                    $productPrice = $productData["product_price"];

                    $subTotal = $productPrice * $productQuantity;
                    $grandTotal += $subTotal;

                    $getProductItems = $conn->prepare("SELECT item_id, quantity FROM product_items_table WHERE product_id = ?");
                    $getProductItems->bind_param("i", $productId);
                    $getProductItems->execute();

                    $itemsResult = $getProductItems->get_result();

                    while($itemData = $itemsResult->fetch_array()) {

                        $itemId = $itemData["item_id"];
                        $itemQtyPerProduct = $itemData["quantity"];

                        $totalStockDeduction = $itemQtyPerProduct * $productQuantity;

                        $checkItemStock = $conn->prepare("SELECT item_name, item_stock FROM inventory_table WHERE item_id = ?");
                        $checkItemStock->bind_param("i", $itemId);
                        $checkItemStock->execute();

                        $itemStockData = $checkItemStock->get_result()->fetch_array();

                        if($itemStockData["item_stock"] < $totalStockDeduction) {
                            $conn->rollback();
                            displayNotification("error", "Insufficient Stock", "Transaction error! Not enough stock for: " . $itemStockData["item_name"]);
                            header("Location: ../admin/dashboard.php?page=add-transaction");
                            exit();
                        }

                        $updateItemStock = $conn->prepare("UPDATE inventory_table
                                                        SET item_stock = item_stock - ? 
                                                        WHERE item_id = ?");
                        $updateItemStock->bind_param("ii", $totalStockDeduction, $itemId);
                        $updateItemStock->execute();
                    }

                    $updateProductSolds = $conn->prepare("UPDATE products_table
                                                            SET product_solds = product_solds + ?
                                                            WHERE product_id = ?");
                    $updateProductSolds->bind_param("ii", $productQuantity, $productId);
                    $updateProductSolds->execute();
                }
            }

            $transactionId = "TXN-" . date("Ymd") . "-" . rand(1000,9999);
            
            $addNewTransaction = $conn->prepare("INSERT INTO transactions_table(transaction_id, total_amount, processed_by)
                                                VALUES(?, ?, ?)");
            $addNewTransaction->bind_param("sdi", $transactionId, $grandTotal, $userId);
            $addNewTransaction->execute();

            foreach($productIds as $productId) {
                $productQuantity = isset($productsQuantities[$productId]) && $productsQuantities[$productId] > 0
                                        ? (int)$productsQuantities[$productId]
                                        : 1;

                $validateProduct = $conn->prepare("SELECT * FROM products_table WHERE product_id = ? LIMIT 1");
                $validateProduct->bind_param("i", $productId);
                $validateProduct->execute();

                $productQueryResult = $validateProduct->get_result();

                $productData = $productQueryResult->fetch_array();
                $productPrice = $productData["product_price"];

                $subTotal = $productPrice * $productQuantity;

                $addTransactionItem = $conn->prepare("INSERT INTO transaction_items_table(transaction_id, product_id, product_quantity, total_price)
                                                    VALUES(?, ?, ?, ?)");
                $addTransactionItem->bind_param("siid", $transactionId, $productId, $productQuantity, $subTotal);
                $addTransactionItem->execute();
            }

            addUserActivity($userId, "Add Transaction", "Added a new transaction: " . $transactionId);

            $conn->commit();

            displayNotification("success", "Transaction Added", "Transaction Added Successfully!");
            header("Location: ../admin/dashboard.php?page=add-transaction");
            exit();
        }

        catch(mysqli_sql_exception $e) {
            $conn->rollback();
            displayNotification("error", "Error Occured", "Something went wrong: " . $e->getMessage());
            header("Location: ../admin/dashboard.php?page=add-transaction");
            exit();
        }
    }

    else {
        // Prevent Accessing file via manual input link
        header("Location: ../index.php");
        exit();
    }
?>