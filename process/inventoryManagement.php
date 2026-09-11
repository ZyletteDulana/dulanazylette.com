<?php
    require_once "../config/databaseConnector.php";
    require_once "../config/functions.php";
    require_once "../config/inputValidators.php";
    require_once "../config/notifications.php";
    require_once "../config/formatter.php";
    require_once "../includes/activityLogger.php";

    require_once "../config/file-upload/vendor/autoload.php";

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addNewItem"])) {

        $userId = $_SESSION["userId"];
        $itemName = sanitizeNames($_POST["itemName"]);
        $itemStock = trim($_POST["itemStock"]);

        if(empty($userId) || empty($itemName)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-inventory");
            exit();
        }

        else if(!empty($itemStock) && !validateAmount($itemStock)) {
            displayNotification("error", "Invalid Stock", "Invalid item stock! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-inventory");
            exit();
        }

        else {
            $itemStock = ($itemStock !== '') ? (int)$itemStock : 0;

            try {
                $conn->begin_transaction();

                $itemValidator = strtolower($itemName);

                $validateItem = $conn->prepare("SELECT * FROM inventory_table WHERE LOWER(item_name) = ? LIMIT 1");
                $validateItem->bind_param("s", $itemValidator);
                $validateItem->execute();

                $queryResult = $validateItem->get_result();

                if($queryResult->num_rows === 1) {
                    $itemData = $queryResult->fetch_object();
                    $itemId = $itemData->item_id;
                    $currentItemStock = $itemData->item_stock;

                    $newStock = $itemStock + (int)$currentItemStock;

                    $updateItemStock = $conn->prepare("UPDATE inventory_table SET item_stock = ? WHERE item_id = ?");
                    $updateItemStock->bind_param("ii", $newStock, $itemId);
                    $updateItemStock->execute();

                    addUserActivity($userId, "Updated Item", "Updated item stock: " . $itemName);
                }

                else {
                    $addNewItem = $conn->prepare("INSERT INTO inventory_table(item_name, item_stock, added_by) VALUES(?, ?, ?)");
                    $addNewItem->bind_param("sii", $itemName, $itemStock, $userId);
                    $addNewItem->execute();

                    addUserActivity($userId, "Add Item", "Added a new item: " . $itemName);
                }

                $conn->commit();

                displayNotification("success", "Item Added", "Item added to inventory successfully!");
                header("Location: ../admin/dashboard.php?page=inventory-list");
                exit();
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occured", "Something went wrong: " . $e->getMessage());
                header("Location: ../admin/dashboard.php?page=add-inventory");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addMultipleItems"])) {

        $userId = $_SESSION["userId"];
        $inventoryFile = $_FILES["inventoryFile"]["name"];

        if(empty($userId) || empty($inventoryFile)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-inventory");
            exit();
        }

        $fileTmpName = $_FILES["inventoryFile"]["tmp_name"];
        $fileSize = $_FILES["inventoryFile"]["size"];
        $isError = $_FILES["inventoryFile"]["error"];

        $fileExtension = strtolower(pathinfo($inventoryFile, PATHINFO_EXTENSION));

        if(!validateExcelFile($fileExtension) || $isError !== 0) {
            displayNotification("error", "Invalid File", "Invalid or corrupted file! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-inventory");
            exit();
        }

        else {
            $uniqueFileName = "InventoryStock_" . time() . "_" . uniqid() .  "." . $fileExtension;
            $fileSizeInMb = convertToMB($fileSize);

            $headerAliases = [
                "item" => ["item", "item name", "item:", "item name:", "name", "item_name", "itemName", "itemname"],
                "stock" => ["stocks", "item stocks", "stocks:", "item stocks", "item_stocks", "itemStocks", "itemstocks", "stock", "item stock", "stock:", "item stock:", "item_stock", "itemStock"]
            ];

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmpName);
            $data = $spreadsheet->getActiveSheet()->toArray();

            if(count($data) < 1) {
                displayNotification("error", "Empty File", "File has no content! Please try again.");
                header("Location: ../admin/dashboard.php?page=add-inventory");
                exit();
            }

            $headerRows = array_map(function($h) {
                return strtolower(trim($h));
            }, $data[0]);

            $headers = [];

            foreach($headerRows as $index => $header) {
                foreach($headerAliases as $key => $possibleNames) {
                    if(in_array($header, $possibleNames)) {
                        $headers[$key] = $index;
                    }
                }
            }

            foreach($headerAliases as $key => $value) {
                if(!isset($headers[$key])) {
                    displayNotification("error", "Missing Header", "Missing required header! Please try again.");
                    header("Location: ../admin/dashboard.php?page=add-inventory");
                    exit();
                }
            }
            
            try {
                $conn->begin_transaction();
                
                foreach($data as $index => $row) {
                    if($index === 0) continue;
                    
                    $itemName = trim($row[$headers["item"]] ?? "");
                    $rawStock = trim($row[$headers["stock"]] ?? "");
                    $itemStock = ($rawStock !== '') ? (int)$rawStock : 0;

                    $itemValidator = strtolower($itemName);

                    $validateItem = $conn->prepare("SELECT * FROM inventory_table WHERE LOWER(item_name) = ? LIMIT 1");
                    $validateItem->bind_param("s", $itemValidator);
                    $validateItem->execute();

                    $queryResult = $validateItem->get_result();

                    if($queryResult->num_rows === 1) {
                        $itemData = $queryResult->fetch_object();
                        $itemId = $itemData->item_id;
                        $currentItemStock = $itemData->item_stock;

                        $newStock = $itemStock + (int)$currentItemStock;

                        $updateItemStock = $conn->prepare("UPDATE inventory_table SET item_stock = ? WHERE item_id = ?");
                        $updateItemStock->bind_param("ii", $newStock, $itemId);
                        $updateItemStock->execute();
                    }
                    else {
                        $addNewItem = $conn->prepare("INSERT INTO inventory_table(item_name, item_stock, added_by) VALUES(?, ?, ?)");
                        $addNewItem->bind_param("sii", $itemName, $itemStock, $userId);
                        $addNewItem->execute();
                    }
                }

                $uploadExcelFile = $conn->prepare("INSERT INTO uploaded_files_table(file_name, file_size, uploaded_by) VALUES(?, ?, ?)");
                $uploadExcelFile->bind_param("sdi", $uniqueFileName, $fileSizeInMb, $userId);
                $uploadExcelFile->execute();

                $filePath = "../uploads/uploaded-files/" . $uniqueFileName;
                move_uploaded_file($fileTmpName, $filePath);

                addUserActivity($userId, "Add Item", "Added items via upload file");

                $conn->commit();

                displayNotification("success", "Item Added", "Item added to inventory successfully!");
                header("Location: ../admin/dashboard.php?page=inventory-list");
                exit();
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occurred", "Error occurred: " . $e->getMessage());
                header("Location: ../admin/dashboard.php?page=add-inventory");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateItem"])) {

        $userId = $_SESSION["userId"];
        $itemId = htmlspecialchars($_POST["itemId"]);
        $itemName = sanitizeNames($_POST["itemName"]);
        $itemStock = trim($_POST["itemStock"]);

        if(empty($userId) || empty($itemId) || empty($itemName)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again");
            header("Location: ../admin/dashboard.php?page=inventory-list");
            exit();
        }

        else if(!empty($itemStock) && !validateAmount($itemStock)) {
            displayNotification("error", "Invalid Amount", "Invalid stock amount! Please try again.");
            header("Location: ../admin/dashboard.php?page=inventory-list");
            exit();
        }

        else {
            $itemStock = ($itemStock !== '') ? (int)$itemStock : 0;

            try {
                $conn->begin_transaction();

                $itemValidator = strtolower($itemName);
                $validateItem = $conn->prepare("SELECT * FROM inventory_table WHERE LOWER(item_name) = ? LIMIT 1");
                $validateItem->bind_param("s", $itemValidator);
                $validateItem->execute();

                $queryResult = $validateItem->get_result();

                if($queryResult->num_rows === 1) {
                    $itemData = $queryResult->fetch_object();
                    $fetchedItemId = $itemData->item_id;

                    $updateItem = $conn->prepare("UPDATE inventory_table SET item_name = ?, item_stock = ? WHERE item_id = ?");
                    $updateItem->bind_param("sii", $itemName, $itemStock, $fetchedItemId);
                }

                else {
                    $updateItem = $conn->prepare("UPDATE inventory_table SET item_name = ?, item_stock = ? WHERE item_id = ?");
                    $updateItem->bind_param("sii", $itemName, $itemStock, $itemId);
                }

                $updateItem->execute();

                addUserActivity($userId, "Update Item", "Updated an item: " . $itemName);

                $conn->commit();

                displayNotification("success", "Item Updated", "Item Updated Successfully!");
                header("Location: ../admin/dashboard.php?page=inventory-list");
                exit();
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();

                displayNotification("error", "Error Occurred", "Something went wrong! Please try again.");
                header("Location: ../admin/dashboard.php?page=inventory-list");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteItem"])) {
        
        $userId = $_SESSION["userId"];
        $itemId = htmlspecialchars($_POST["itemId"]);
        $itemName = sanitizeNames($_POST["itemName"]);

        if(empty($userId) || empty($itemId) || empty($itemName)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=inventory-list");
            exit();
        }

        else {
            try {
                $conn->begin_transaction();

                $itemValidator = strtolower($itemName);

                $validateItem = $conn->prepare("SELECT * FROM inventory_table WHERE LOWER(item_name) = ? AND item_id = ? LIMIT 1");
                $validateItem->bind_param("si", $itemValidator, $itemId);
                $validateItem->execute();

                $queryResult = $validateItem->get_result();

                if($queryResult->num_rows === 1) {

                    $deleteItem = $conn->prepare("DELETE FROM inventory_table WHERE item_id = ?");
                    $deleteItem->bind_param("i", $itemId);
                    $deleteItem->execute();

                    addUserActivity($userId, "Delete Item", "Deleted an item: " . $itemName);

                    $conn->commit();

                    displayNotification("success", "Item Deleted", "Item deleted successfully!");
                    header("Location: ../admin/dashboard.php?page=inventory-list");
                    exit();
                }

                else {
                    $conn->rollback();

                    displayNotification("error", "Invalid Item", "Invalid item! Please try again.");
                    header("Location: ../admin/dashboard.php?page=inventory-list");
                    exit();
                }
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();

                displayNotification("error", "Error Occured", "Something went wrong! Please try again.");
                header("Location: ../admin/dashboard.php?page=inventory-list");
                exit();
            }
        }
    }

    else {
        header("Location: ../index.php");
        exit();
    }
?>