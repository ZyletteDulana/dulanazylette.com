<?php
    header("Content-Type: application/json");

    require_once "../config/databaseConnector.php";

    try {
        $checkStock = $conn->prepare("SELECT
                                        COUNT(*) AS 'low_stock_count'
                                    FROM inventory_table
                                    WHERE item_stock < 50");

        $checkStock->execute();

        $queryResult = $checkStock->get_result();

        $lowStockData = $queryResult->fetch_assoc();

        echo json_encode([
            "success" => true,
            "message" => "Data fetched successfully",
            "data" => (int)$lowStockData["low_stock_count"]
        ]);
    }

    catch(mysqli_sql_exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error fetching data: " . $e->getMessage()
        ]);
    }
?>