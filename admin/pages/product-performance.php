<?php
include_once "../includes/accessChecker.php";

// Revenue Today
$getProductSalesToday = $conn->prepare("SELECT
                                            COALESCE(SUM(ti.product_quantity), 0) AS 'product_solds'
                                        FROM transaction_items_table ti
                                        LEFT JOIN transactions_table t
                                        ON ti.transaction_id = t.transaction_id
                                        WHERE t.transaction_date >= CURDATE()
                                        AND t.transaction_date < CURDATE() + INTERVAL 1 DAY
                                    ");
$getProductSalesToday->execute();
$salesTodayResult = $getProductSalesToday->get_result();

$salesToday = $salesTodayResult->fetch_assoc()["product_solds"];

// Revenue this Month
$getSalesThisMonth = $conn->prepare("SELECT
                                            COALESCE(SUM(ti.product_quantity), 0) AS 'sales_this_month'
                                        FROM transactions_table t
                                        JOIN transaction_items_table ti
                                            ON ti.transaction_id = t.transaction_id
                                        WHERE t.transaction_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                        AND t.transaction_date < DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH
                                    ");
$getSalesThisMonth->execute();
$salesThisMonthResult = $getSalesThisMonth->get_result();

$salesThisMonth = $salesThisMonthResult->fetch_assoc()["sales_this_month"];

// Total Revenue
$getTotalSales = $conn->prepare("SELECT
                                    COALESCE(SUM(product_quantity), 0) AS 'total_sales'
                                FROM transaction_items_table
                                    ");
$getTotalSales->execute();
$totalSalesResult = $getTotalSales->get_result();

$totalSales = $totalSalesResult->fetch_assoc()["total_sales"];

?>

<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-4 col-md-12">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-bag-check-fill fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Product Solds Today </p>
                                                        <h4 class="mb-0">
                                                            <?php echo htmlspecialchars($salesToday); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-12">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-calendar-event-fill fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Product Solds this Month </p>
                                                        <h4 class="mb-0">
                                                            <?php echo htmlspecialchars($salesThisMonth); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-4 col-md-12">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-box-seam fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Total Product Solds </p>
                                                        <h4 class="mb-0">
                                                            <?php echo htmlspecialchars($totalSales); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- Bar Chart -->
                            <div class="col-lg-12">

                                <div class="col-sm-12">

                                    <div class="card">

                                        <div class="card-header d-flex justify-content-between align-items-center">

                                            <div class="header-title">
                                                <h4 class="card-title mb-0"> Top Product Solds Each Month </h4>
                                            </div>

                                            <div class="d-flex align-items-center">

                                                <div class="d-flex align-items-center gap-2 mr-2">

                                                    <div class="input-group">

                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="month">
                                                                Filter Month
                                                            </label>
                                                        </div>

                                                        <select id="month" class="custom-select" name="month">

                                                            <?php
                                                            $getMonths = $conn->prepare("
                                                                                        SELECT DISTINCT 
                                                                                            MONTH(transaction_date) AS month_number,
                                                                                            DATE_FORMAT(transaction_date, '%M') AS month
                                                                                        FROM transactions_table
                                                                                        ORDER BY month_number ASC;
                                                                                    ");

                                                            $getMonths->execute();
                                                            $monthsResult = $getMonths->get_result();

                                                            $currentMonth = date("m");

                                                            while ($monthData = $monthsResult->fetch_assoc()) {
                                                                $monthValue = $monthData["month_number"];
                                                            ?>

                                                                <option value="<?= htmlspecialchars($monthValue) ?>"
                                                                    <?= ($monthValue == $currentMonth) ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($monthData["month"]) ?>
                                                                </option>

                                                            <?php } ?>

                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="d-flex align-items-center gap-2">

                                                    <div class="input-group">

                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="year">
                                                                Filter Year
                                                            </label>
                                                        </div>

                                                        <select id="year" class="custom-select" name="year">

                                                            <?php
                                                            $getYears = $conn->prepare("
                                                                                        SELECT DISTINCT YEAR(transaction_date) AS year
                                                                                        FROM transactions_table
                                                                                        ORDER BY year DESC
                                                                                    ");

                                                            $getYears->execute();
                                                            $yearsResult = $getYears->get_result();

                                                            $currentYear = date("Y");

                                                            while ($yearData = $yearsResult->fetch_assoc()) {
                                                                $yearValue = $yearData["year"];
                                                            ?>

                                                                <option value="<?= htmlspecialchars($yearValue) ?>"
                                                                    <?= ($yearValue == $currentYear) ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($yearValue) ?>
                                                                </option>

                                                            <?php } ?>

                                                        </select>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="card-body">

                                            <div class="col-lg-12">

                                                <!-- CHART -->
                                                <canvas id="monthlyTopSalesChart" height="500"></canvas>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- Pie Chart -->
                            <div class="col-lg-12">

                                <div class="col-sm-12">

                                    <div class="card">

                                        <div class="card-header d-flex justify-content-between align-items-center">

                                            <div class="header-title">
                                                <h4 class="card-title mb-0"> Sales Distribution by Product Type </h4>
                                            </div>

                                            <div class="d-flex align-items-center">

                                                <div class="d-flex align-items-center gap-2 mr-2">

                                                    <div class="input-group">

                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="monthPieChart">
                                                                Filter Month
                                                            </label>
                                                        </div>

                                                        <select id="monthPieChart" class="custom-select" name="month">

                                                            <?php
                                                            $getMonths = $conn->prepare("
                                                                                        SELECT DISTINCT 
                                                                                            MONTH(transaction_date) AS month_number,
                                                                                            DATE_FORMAT(transaction_date, '%M') AS month
                                                                                        FROM transactions_table
                                                                                        ORDER BY month_number ASC;
                                                                                    ");

                                                            $getMonths->execute();
                                                            $monthsResult = $getMonths->get_result();

                                                            $currentMonth = date("m");

                                                            while ($monthData = $monthsResult->fetch_assoc()) {
                                                                $monthValue = $monthData["month_number"];
                                                            ?>

                                                                <option value="<?= htmlspecialchars($monthValue) ?>"
                                                                    <?= ($monthValue == $currentMonth) ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($monthData["month"]) ?>
                                                                </option>

                                                            <?php } ?>

                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="d-flex align-items-center gap-2">

                                                    <div class="input-group">

                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="yearPieChart">
                                                                Filter Year
                                                            </label>
                                                        </div>

                                                        <select id="yearPieChart" class="custom-select" name="year">

                                                            <?php
                                                            $getYears = $conn->prepare("
                                                                                        SELECT DISTINCT YEAR(transaction_date) AS year
                                                                                        FROM transactions_table
                                                                                        ORDER BY year DESC
                                                                                    ");

                                                            $getYears->execute();
                                                            $yearsResult = $getYears->get_result();

                                                            $currentYear = date("Y");

                                                            while ($yearData = $yearsResult->fetch_assoc()) {
                                                                $yearValue = $yearData["year"];
                                                            ?>

                                                                <option value="<?= htmlspecialchars($yearValue) ?>"
                                                                    <?= ($yearValue == $currentYear) ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($yearValue) ?>
                                                                </option>

                                                            <?php } ?>

                                                        </select>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="card-body">

                                            <div class="col-lg-12">

                                                <!-- CHART -->
                                                <canvas id="categoriesPieChart" height="500"></canvas>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- Table -->
                            <div class="col-lg-12">

                                <div class="col-sm-12">

                                    <div class="card">

                                        <div class="card-header d-flex justify-content-between align-items-center">

                                            <div class="header-title">
                                                <h4 class="card-title mb-0"> Total Product Sales Per Month </h4>
                                            </div>

                                            <div class="d-flex align-items-center">

                                                <div class="d-flex align-items-center gap-2 mr-2">

                                                    <div class="input-group">

                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="monthTable">
                                                                Filter Month
                                                            </label>
                                                        </div>

                                                        <select id="monthTable" class="custom-select" name="month">

                                                            <?php
                                                            $getMonths = $conn->prepare("
                                                                                        SELECT DISTINCT 
                                                                                            MONTH(transaction_date) AS month_number,
                                                                                            DATE_FORMAT(transaction_date, '%M') AS month
                                                                                        FROM transactions_table
                                                                                        ORDER BY month_number ASC;
                                                                                    ");

                                                            $getMonths->execute();
                                                            $monthsResult = $getMonths->get_result();

                                                            $currentMonth = date("m");

                                                            while ($monthData = $monthsResult->fetch_assoc()) {
                                                                $monthValue = $monthData["month_number"];
                                                            ?>

                                                                <option value="<?= htmlspecialchars($monthValue) ?>"
                                                                    <?= ($monthValue == $currentMonth) ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($monthData["month"]) ?>
                                                                </option>

                                                            <?php } ?>

                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="d-flex align-items-center gap-2">

                                                    <div class="input-group">

                                                        <div class="input-group-prepend">
                                                            <label class="input-group-text" for="year">
                                                                Filter Year
                                                            </label>
                                                        </div>

                                                        <select id="yearTable" class="custom-select" name="year">

                                                            <?php
                                                            $getYears = $conn->prepare("
                                                                                        SELECT DISTINCT YEAR(transaction_date) AS year
                                                                                        FROM transactions_table
                                                                                        ORDER BY year DESC
                                                                                    ");

                                                            $getYears->execute();
                                                            $yearsResult = $getYears->get_result();

                                                            $currentYear = date("Y");

                                                            while ($yearData = $yearsResult->fetch_assoc()) {
                                                                $yearValue = $yearData["year"];
                                                            ?>

                                                                <option value="<?= htmlspecialchars($yearValue) ?>"
                                                                    <?= ($yearValue == $currentYear) ? "selected" : "" ?>>
                                                                    <?= htmlspecialchars($yearValue) ?>
                                                                </option>

                                                            <?php } ?>

                                                        </select>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="card-body">

                                            <div class="col-lg-12">

                                                <div class="table-responsive rounded mb-3">

                                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table monthlySalesTable">

                                                        <thead class="bg-white text-uppercase">

                                                            <tr class="ligth ligth-data">

                                                                <th> Month </th>
                                                                <th> Year </th>
                                                                <th> Product Image </th>
                                                                <th> Product Name</th>
                                                                <th> Product Type </th>
                                                                <th> Solds </th>
                                                                <th> Revenue </th>
                                                                <th> Contribution </th>

                                                            </tr>

                                                        </thead>

                                                        <tbody class="light-body text-center" id="monthlySalesTableBody">

                                                        </tbody>

                                                    </table>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- Table -->
                            <div class="col-lg-12">

                                <div class="col-sm-12">

                                    <div class="card">

                                        <div class="card-header d-flex justify-content-between align-items-center">

                                            <div class="header-title">
                                                <h4 class="card-title mb-0"> 
                                                    Product Sales & Revenue Contribution
                                                </h4>
                                            </div>


                                        </div>

                                        <div class="card-body">

                                            <div class="col-lg-12">

                                                <div class="table-responsive rounded mb-3">

                                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table productSalesTable">

                                                        <thead class="bg-white text-uppercase">

                                                            <tr class="ligth ligth-data">
 
                                                                <th> Product Image </th>
                                                                <th> Product Name</th>
                                                                <th> Product Type </th>
                                                                <th> Solds </th>
                                                                <th> Revenue </th>
                                                                <th> Contribution </th>

                                                            </tr>

                                                        </thead>

                                                        <tbody class="light-body text-center">

                                                            <?php
                                                                $getProductSales = $conn->prepare("
                                                                                                 SELECT
                                                                                                    p.product_id,
                                                                                                    p.product_name,
                                                                                                    p.product_type,
                                                                                                    COALESCE(p.product_image, 'default-product-image.png') AS product_image,

                                                                                                    COALESCE(SUM(ti.product_quantity), 0) AS total_solds,
                                                                                                    COALESCE(SUM(ti.product_quantity * p.product_price), 0) AS revenue,

                                                                                                    ROUND(
                                                                                                        COALESCE(SUM(ti.product_quantity), 0) 
                                                                                                        / total.total_solds * 100,
                                                                                                        2
                                                                                                    ) AS solds_percentage,

                                                                                                    ROUND(
                                                                                                        COALESCE(SUM(ti.product_quantity * p.product_price), 0)
                                                                                                        / total.total_revenue * 100,
                                                                                                        2
                                                                                                    ) AS revenue_percentage

                                                                                                FROM products_table p

                                                                                                LEFT JOIN transaction_items_table ti
                                                                                                    ON ti.product_id = p.product_id

                                                                                                LEFT JOIN transactions_table t
                                                                                                    ON t.transaction_id = ti.transaction_id

                                                                                                CROSS JOIN (
                                                                                                    SELECT
                                                                                                        COALESCE(SUM(ti2.product_quantity), 0) AS total_solds,
                                                                                                        COALESCE(SUM(ti2.product_quantity * p2.product_price), 0) AS total_revenue
                                                                                                    FROM transaction_items_table ti2
                                                                                                    JOIN products_table p2
                                                                                                        ON ti2.product_id = p2.product_id
                                                                                                ) total

                                                                                                GROUP BY p.product_id

                                                                                                ORDER BY total_solds DESC");
                                                                $getProductSales->execute();
                                                                $productSalesResult = $getProductSales->get_result();

                                                                while($salesData = $productSalesResult->fetch_array()) {
                                                            ?>

                                                                    <tr>
                                                                        <td>
                                                                            <img src="<?php echo htmlspecialchars($productsPictureFolderPath . $salesData["product_image"]); ?>" alt="Product Image" class="image-fluid" width="50" height="50">
                                                                        </td>

                                                                        <td>
                                                                            <?php echo htmlspecialchars($salesData["product_name"]); ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php echo htmlspecialchars($salesData["product_type"]); ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php echo htmlspecialchars($salesData["total_solds"]); ?>
                                                                        </td>

                                                                        <td class="export-currency">
                                                                            ₱<?php echo htmlspecialchars($salesData["revenue"]); ?>
                                                                        </td>

                                                                        <td>
                                                                            <?php echo htmlspecialchars($salesData["revenue_percentage"]); ?>%
                                                                        </td>
                                                                    </tr>   
                                                            <?php
                                                                }
                                                            ?>

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

    </div>
    <!-- Page end  -->
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function updateTopSalesChart() {

            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;
            const chartContainer = document.getElementById("monthlyTopSalesChart");

           fetch(`../api/productMonthlySolds.php?getMonthlyTopSales=1&month=${month}&year=${year}`)
                .then(res => res.json())
                .then(result => {
                    if (!result.success) return;

                    const resultData = result.data || [];

                    if (!Array.isArray(resultData) || resultData.length === 0) {
                        console.warn("No chart data available");

                        if (chartInstances['monthlyTopSalesChart']) {
                            chartInstances['monthlyTopSalesChart'].destroy();
                        }

                        return;
                    }

                    const labels = resultData.map(i => i.product_name);
                    const data = resultData.map(i => i.total_solds);

                    createChart({
                        canvasId: 'monthlyTopSalesChart',
                        type: 'bar',
                        labels,
                        datasets: [{
                            label: `Top Product Sales (${resultData[0].month}, ${year})`,
                            data,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)', // fill color
                            borderColor: 'rgba(54, 162, 235, 1)', // border color
                            borderWidth: 1,
                            fill: true,
                            tension: 0.8,
                            format: "solds",
                        }],
                        title: `Top Product Sales for ${resultData[0].month}, ${year}`,
                        options: {
                            titleFontSize: 22,
                            legendFontSize: 16,
                            axisFontSize: 14
                        }
                    });
                })
                .catch(error => {
                    console.error("Error fetching monthly chart data:", error);
                });
        }

        document.getElementById('month').addEventListener('change', updateTopSalesChart);
        document.getElementById('year').addEventListener('change', updateTopSalesChart);

        updateTopSalesChart();

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function updateSalesTable() {

            const month = document.getElementById("monthTable").value;
            const year = document.getElementById('yearTable').value;
            const tbody = document.getElementById("monthlySalesTableBody");

            if ($.fn.DataTable.isDataTable('.monthlySalesTable')) {
                $('.monthlySalesTable').DataTable().destroy();
            }

            tbody.innerHTML = ""

           fetch(`/api/productMonthlySolds.php?getMonthlyTopSales=1&month=${month}&year=${year}`)
                .then(res => res.json())
                .then(result => {

                    if (!result.success) return;

                    const data = result.data || [];

                    if(result.data.length === 0) {
                        initDataTable(".monthlySalesTable", {
                            order: [
                                [5, "desc"]
                            ],
                            title: "Top Sales",
                            filenamePrefix: "Top_Sales",
                            subtitle: "Top Sales as of"
                        });

                        return;
                    }

                    data.forEach(d => {
                        const row = `
                            <tr>
                                <td>
                                    ${d.month}
                                </td>

                                <td>${d.year}</td>

                                <td>
                                    <img src="../uploads/product-images/${d.product_image}" width="50" height="50" alt="Product Image" class="image-fluid">
                                </td>

                                <td>${d.product_name}</td>

                                <td>${d.product_type}</td>

                                <td>${d.total_solds ?? 0}</td>
            
                                <td class="export-currency">₱${Number(d.total_revenue).toLocaleString('en-PH')}</td>

                                <td>${d.contribution_percentage ?? 0}%</td>
                            </tr>
                        `;

                        tbody.insertAdjacentHTML("beforeend", row);
                    });

                    initDataTable(".monthlySalesTable", {
                        order: [
                            [5, "desc"]
                        ],
                        title: "Top Sales",
                        filenamePrefix: "Top_Sales",
                        subtitle: "Top Sales as of"
                    });

                })
                .catch(error => {
                    console.error("Error fetching monthly revenue data:", error);
                });
        }

        document.getElementById('monthTable').addEventListener('change', updateSalesTable);
        document.getElementById('yearTable').addEventListener('change', updateSalesTable);

        updateSalesTable()

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function updateCategoriesDistribution() {

            const month = document.getElementById('monthPieChart').value;
            const year = document.getElementById('yearPieChart').value;
            const chartContainer = document.getElementById("monthlyTopSalesChart");

           fetch(`../api/productMonthlySolds.php?getMonthlyTypeContribution=1&month=${month}&year=${year}`)
                .then(res => res.json())
                .then(result => {
                    if (!result.success) return;

                    const resultData = result.data || [];

                    if (!Array.isArray(resultData) || resultData.length === 0) {

                        if (chartInstances['categoriesPieChart']) {
                            chartInstances['categoriesPieChart'].destroy();
                        }

                        return;
                    }

                    const labels = resultData.map(i => i.product_type);
                    const data = resultData.map(i => i.revenue_percentage);

                    createChart({
                        canvasId: 'categoriesPieChart',
                        type: 'pie',
                        labels: labels,
                        datasets: [{
                            label: "Revenue Share",
                            data: data,
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#f740c0',
                            ],
                            format: "percent"
                        }],
                        title: `Sales Distribution by Product Type (${resultData[0].month}, ${resultData[0].year})`
                    });
                })
                .catch(error => {
                    console.error("Error fetching monthly chart data:", error);
                });
        }

        document.getElementById('monthPieChart').addEventListener('change', updateCategoriesDistribution);
        document.getElementById('yearPieChart').addEventListener('change', updateCategoriesDistribution);

        updateCategoriesDistribution();

    });
</script>