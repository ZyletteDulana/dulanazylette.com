<?php
    header("Content-Type: application/json");

    require_once "../config/databaseConnector.php";

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["getMonthlyTopSales"])) {

        $month = isset($_GET["month"]) ? (int)$_GET["month"] : date("m");
        $year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");

        try {
            $monthlyTopSales = $conn->prepare("SELECT
                                                DATE_FORMAT(t.transaction_date, '%M') AS month,
                                                YEAR(t.transaction_date) AS year,
                                                p.product_id,
                                                p.product_price AS price_each,
                                                p.product_name,
                                                p.product_type,
                                                COALESCE(p.product_image, 'default-product-image.png') AS product_image,

                                                COALESCE(SUM(ti.product_quantity * p.product_price), 0.00) AS revenue,
                                                COALESCE(SUM(ti.product_quantity), 0) AS total_solds,

                                                ROUND(
                                                    COALESCE(SUM(ti.product_quantity * p.product_price), 0.00)
                                                    / (
                                                        SELECT 
                                                            SUM(ti2.product_quantity * p2.product_price)
                                                        FROM transactions_table t2
                                                        JOIN transaction_items_table ti2 
                                                            ON t2.transaction_id = ti2.transaction_id
                                                        JOIN products_table p2 
                                                            ON ti2.product_id = p2.product_id
                                                        WHERE MONTH(t2.transaction_date) = ?
                                                        AND YEAR(t2.transaction_date) = ?
                                                        AND p2.product_type NOT IN ('Add-ons')
                                                    ) * 100,
                                                    2
                                                ) AS contribution_percentage

                                            FROM transactions_table t
                                            LEFT JOIN transaction_items_table ti
                                                ON t.transaction_id = ti.transaction_id
                                            LEFT JOIN products_table p
                                                ON ti.product_id = p.product_id

                                            WHERE MONTH(t.transaction_date) = ?
                                            AND YEAR(t.transaction_date) = ?
                                            AND p.product_type NOT IN ('Add-ons')

                                            GROUP BY 
                                                p.product_id, 
                                                p.product_name, 
                                                price_each, 
                                                month, 
                                                year

                                            ORDER BY total_solds DESC
                                            LIMIT 5;
                                                    ");

            $monthlyTopSales->bind_param("iiii", $month, $year, $month, $year);
            $monthlyTopSales->execute();

            $queryResult = $monthlyTopSales->get_result();

            $data = [];

            while ($row = $queryResult->fetch_assoc()) {
                $data[] = [
                    "month" => $row["month"],
                    "year" => (int)$row["year"],
                    "product_name" => $row["product_name"],
                    "price_each" => $row["price_each"],
                    "product_type" => $row["product_type"],
                    "total_revenue" => $row["revenue"],
                    "total_solds" => $row["total_solds"],
                    "contribution_percentage" => $row["contribution_percentage"],
                    "product_image" => $row["product_image"]
                ];
            }

            echo json_encode([
                "success" => true,
                "message" => "Monthly revenue fetched successfully",
                "data" => $data
            ]);
        }

        catch (mysqli_sql_exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error fetching data: " . $e->getMessage()
            ]);
        }
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["getMonthlyTypeContribution"])) {

        $month = isset($_GET["month"]) ? (int)$_GET["month"] : date("m");
        $year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");

        try {
            $getProductTypeDistribution = $conn->prepare("
                                                    SELECT 
                                                        DATE_FORMAT(t.transaction_date, '%M') AS month,
                                                        YEAR(t.transaction_date) AS year,
                                                        p.product_type,

                                                        SUM(ti.product_quantity * p.product_price) AS total_revenue,

                                                        ROUND(
                                                            SUM(ti.product_quantity * p.product_price)
                                                            / (
                                                                SELECT SUM(ti2.product_quantity * p2.product_price)
                                                                FROM transaction_items_table ti2
                                                                JOIN products_table p2 
                                                                    ON ti2.product_id = p2.product_id
                                                                JOIN transactions_table t2
                                                                    ON ti2.transaction_id = t2.transaction_id
                                                                WHERE MONTH(t2.transaction_date) = ?
                                                                AND YEAR(t2.transaction_date) = ?
                                                            ) * 100,
                                                            2
                                                        ) AS revenue_percentage

                                                    FROM transaction_items_table ti
                                                    JOIN products_table p 
                                                        ON ti.product_id = p.product_id
                                                    JOIN transactions_table t
                                                        ON ti.transaction_id = t.transaction_id

                                                    WHERE MONTH(t.transaction_date) = ?
                                                    AND YEAR(t.transaction_date) = ?

                                                    GROUP BY p.product_type;
                                                ");

            $getProductTypeDistribution->bind_param("iiii", $month, $year, $month, $year);
            $getProductTypeDistribution->execute();

            $queryResult = $getProductTypeDistribution->get_result();

            $data = [];

            while ($row = $queryResult->fetch_assoc()) {
                $data[] = [
                    "year" => (int)$row["year"],
                    "month" => $row["month"],
                    "product_type" => $row["product_type"],
                    "total_revenue" => (float)$row["total_revenue"],
                    "revenue_percentage" => (float)$row["revenue_percentage"]
                ];
            }

            echo json_encode([
                "success" => true,
                "message" => "Data fetched successfully",
                "data" => $data
            ]);
        }

        catch (mysqli_sql_exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error fetching data: " . $e->getMessage()
            ]);
        }
    }

    
?>