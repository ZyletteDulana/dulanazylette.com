<?php
    header("Content-Type: application/json");

    require_once "../config/databaseConnector.php";

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["getMonthlyTotals"])) {

        $year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");

        try {
            $getMonthlyRevenue = $conn->prepare("SELECT
                                                    YEAR(transaction_date) AS year,
                                                    DATE_FORMAT(transaction_date, '%M') AS month,
                                                    MONTH(transaction_date) AS month_number,
                                                    COALESCE(SUM(total_amount), 0) AS total_revenue
                                                FROM transactions_table
                                                WHERE YEAR(transaction_date) = ?
                                                GROUP BY MONTH(transaction_date)
                                                ORDER BY MONTH(transaction_date)");

            $getMonthlyRevenue->bind_param("i", $year);
            $getMonthlyRevenue->execute();

            $queryResult = $getMonthlyRevenue->get_result();

            $data = [];

            while ($row = $queryResult->fetch_assoc()) {
                $data[] = [
                    "year" => (int)$row["year"],
                    "month" => $row["month"],
                    "month_number" => (int)$row["month_number"],
                    "total_revenue" => (float)$row["total_revenue"]
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

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["getMonthlyTotalsTable"])) {

        $year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");

        try {
            $getMonthlyRevenue = $conn->prepare("
                                                    SELECT
                                                        YEAR(t.transaction_date) AS year,
                                                        MONTH(t.transaction_date) AS month_number,
                                                        DATE_FORMAT(t.transaction_date, '%M') AS month,

                                                        SUM(t.total_amount) AS total_revenue,
                                                        COALESCE(SUM(ti.total_qty), 0) AS total_product_solds

                                                    FROM transactions_table t

                                                    LEFT JOIN (
                                                        SELECT 
                                                            transaction_id, 
                                                            SUM(product_quantity) AS total_qty
                                                        FROM transaction_items_table
                                                        GROUP BY transaction_id
                                                    ) ti ON t.transaction_id = ti.transaction_id

                                                    WHERE t.transaction_date >= CONCAT(?, '-01-01')
                                                    AND t.transaction_date < CONCAT(? + 1, '-01-01')

                                                    GROUP BY YEAR(t.transaction_date), MONTH(t.transaction_date)

                                                    ORDER BY month_number
                                                ");

            $getMonthlyRevenue->bind_param("ii", $year, $year);
            $getMonthlyRevenue->execute();

            $queryResult = $getMonthlyRevenue->get_result();

            $data = [];

            while ($row = $queryResult->fetch_assoc()) {
                $data[] = [
                    "year" => (int)$row["year"],
                    "month" => $row["month"],
                    "month_number" => (int)$row["month_number"],
                    "total_revenue" => (float)$row["total_revenue"],
                    "product_solds" => (int)$row["total_product_solds"]
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

    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["getDailyRevenue"])) {

        $month = isset($_GET["month"]) ? (int)$_GET["month"] : date("m");
        $year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");

        try {
            $getDailyRevenueTotal = $conn->prepare("
                                                   SELECT
                                                        DATE_FORMAT(t.transaction_date, '%b. %d, %Y') AS sale_date,
                                                        DAYNAME(t.transaction_date) AS day_name,
                                                        SUM(ti.product_quantity) AS total_solds,
                                                        SUM(ti.product_quantity * p.product_price) AS revenue
                                                    FROM transactions_table t
                                                    JOIN transaction_items_table ti
                                                        ON ti.transaction_id = t.transaction_id
                                                    JOIN products_table p
                                                        ON ti.product_id = p.product_id
                                                    WHERE t.transaction_date >= DATE_FORMAT(CONCAT(?, '-', ?, '-01'), '%Y-%m-%d')
                                                    AND t.transaction_date < DATE_ADD(
                                                            DATE_FORMAT(CONCAT(?, '-', ?, '-01'), '%Y-%m-%d'),
                                                            INTERVAL 1 MONTH
                                                        )
                                                    GROUP BY DATE(t.transaction_date)
                                                    ORDER BY t.transaction_date DESC;
                                                ");

            $getDailyRevenueTotal->bind_param("iiii", $year, $month, $year, $month);
            $getDailyRevenueTotal->execute();

            $queryResult = $getDailyRevenueTotal->get_result();

            $data = [];

            while ($row = $queryResult->fetch_assoc()) {
                $data[] = [
                    "sale_date" => $row["sale_date"],
                    "day_name" => $row["day_name"],
                    "total_solds" => (int)$row["total_solds"],
                    "total_revenue" => (float)$row["revenue"],
                ];
            }

            echo json_encode([
                "success" => true,
                "message" => "Daily revenue fetched successfully",
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