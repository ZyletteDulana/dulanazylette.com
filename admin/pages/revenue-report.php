<?php
include_once "../includes/accessChecker.php";

// Revenue Today
$getRevenueToday = $conn->prepare("SELECT
                                        COALESCE(SUM(total_amount), 0.00) AS 'revenue_today'
                                    FROM transactions_table
                                    WHERE transaction_date >= CURDATE()
                                    AND transaction_date < CURDATE() + INTERVAL 1 DAY
                                    ");
$getRevenueToday->execute();
$revenueTodayResult = $getRevenueToday->get_result();

$revenueToday = $revenueTodayResult->fetch_assoc()["revenue_today"];

// Revenue this Month
// Revenue Today
$getRevenueThisMonth = $conn->prepare("SELECT
                                        COALESCE(SUM(total_amount), 0.00) AS 'revenue_this_month'
                                    FROM transactions_table
                                    WHERE transaction_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                                    AND transaction_date < DATE_FORMAT(CURDATE(), '%Y-%m-01') + INTERVAL 1 MONTH;
                                    ");
$getRevenueThisMonth->execute();
$revenueThisMonthResult = $getRevenueThisMonth->get_result();

$revenueThisMonth = $revenueThisMonthResult->fetch_assoc()["revenue_this_month"];

// Total Revenue
$getTotalRevenue = $conn->prepare("SELECT
                                        COALESCE(SUM(total_amount), 0) AS 'total_revenue'
                                    FROM transactions_table
                                    ");
$getTotalRevenue->execute();
$totalRevenueResult = $getTotalRevenue->get_result();

$totalRevenue = $totalRevenueResult->fetch_assoc()["total_revenue"];

// Avg. Daily Revenue
$getAvgDailyRevenue = $conn->prepare("SELECT 
                                            COALESCE(SUM(total_amount) / COUNT(DISTINCT DATE(transaction_date)), 0) 
                                            AS 'avg_daily_revenue'
                                        FROM transactions_table;");
$getAvgDailyRevenue->execute();

$avgRevenueResult = $getAvgDailyRevenue->get_result();

$avgDailyRevenue = $avgRevenueResult->fetch_assoc()["avg_daily_revenue"];
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

                                    <div class="col-lg-6 col-md-6">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-cash-coin fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Revenue Today </p>
                                                        <h4 class="mb-0">
                                                            ₱<?php echo htmlspecialchars(amountFormatter($revenueToday)); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-calendar-month fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Revenue this Month </p>
                                                        <h4 class="mb-0">
                                                            ₱<?php echo htmlspecialchars(amountFormatter($revenueThisMonth)); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-wallet2 fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Total Revenue </p>
                                                        <h4 class="mb-0">
                                                            ₱<?php echo htmlspecialchars(amountFormatter($totalRevenue)); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6">

                                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                                            <div class="card-body pb-0">

                                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                                    <div class="icon iq-icon-box-2 bg-success-light me-3">
                                                        <i class="bi bi-bar-chart-line fs-2 text-warning"></i>
                                                    </div>

                                                    <div>
                                                        <p class="mb-1 text-muted"> Avg. Daily Revenue </p>
                                                        <h4 class="mb-0">
                                                            ₱<?php echo htmlspecialchars(amountFormatter($avgDailyRevenue)); ?>
                                                        </h4>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-12">

                                <div class="col-sm-12">

                                    <div class="card">

                                        <div class="card-header d-flex justify-content-between align-items-center">

                                            <div class="header-title">
                                                <h4 class="card-title mb-0"> Total Monthly Transactions </h4>
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

                                        <div class="card-body">

                                            <div class="col-lg-12">

                                                <!-- CHART -->
                                                <canvas id="totalMonthlyTranscationsChart" height="500"></canvas>

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
                                                <h4 class="card-title mb-0"> Total Monthly Transactions </h4>
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

                                        <div class="card-body">

                                            <div class="col-lg-12">

                                                <div class="table-responsive rounded mb-3">

                                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table monthlyRevenueTable">

                                                        <thead class="bg-white text-uppercase">

                                                            <tr class="ligth ligth-data">

                                                                <th> Month </th>
                                                                <th> Year </th>
                                                                <th> Total Revenue </th>
                                                                <th> Total Products Sold </th>

                                                            </tr>

                                                        </thead>

                                                        <tbody class="light-body text-center" id="monthlyTransactionTable">

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
                                                <h4 class="card-title mb-0"> Daily Sales & Revenue Overview </h4>
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
                                                            <label class="input-group-text" for="yearDailyRevenue">
                                                                Filter Year
                                                            </label>
                                                        </div>

                                                        <select id="yearDailyRevenue" class="custom-select" name="year">

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

                                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table dailyRevenueTable">

                                                        <thead class="bg-white text-uppercase">

                                                            <tr class="ligth ligth-data">

                                                                <th> Date </th>
                                                                <th> Day </th>
                                                                <th> Total Solds </th>
                                                                <th> Total Revenue </th>

                                                            </tr>

                                                        </thead>

                                                        <tbody class="light-body text-center" id="dailyRevenueTableBody">

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

        function updateMonthlyChart() {

            const year = document.getElementById('year').value;
            const chartContainer = document.getElementById("totalMonthlyTranscationsChart");

            fetch(`/api/monthlyTransactionTotals.php?getMonthlyTotals=1&year=${year}`)
                .then(res => res.json())
                .then(result => {
                    if (!result.success) return;

                    const sorted = result.data.sort((a, b) => a.month_number - b.month_number);

                    const labels = sorted.map(i => i.month);
                    const data = sorted.map(i => i.total_revenue);

                    createChart({
                        canvasId: 'totalMonthlyTranscationsChart',
                        type: 'line',
                        labels,
                        datasets: [{
                            label: `Revenue (${year})`,
                            data,
                            fill: true,
                            tension: 0,
                            format: 'currency',
                        }],
                        title: `Total Monthly Revenue for ${year}`,
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

        document.getElementById('year').addEventListener('change', updateMonthlyChart);

        updateMonthlyChart()

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function updateMonthlyTable() {

            const year = document.getElementById('yearTable').value;
            const tbody = document.getElementById("monthlyTransactionTable");

            if ($.fn.DataTable.isDataTable('.monthlyRevenueTable')) {
                $('.monthlyRevenueTable').DataTable().destroy();
            }

            tbody.innerHTML = ""

           fetch(`/api/monthlyTransactionTotals.php?getMonthlyTotalsTable=1&year=${year}`)
                .then(res => res.json())
                .then(result => {

                    if (!result.success) return;

                    if(result.data.length === 0) {
                        initDataTable(".monthlyRevenueTable", {
                            order: [
                                [0, "asc"]
                            ],
                            title: "Monthly Revenue",
                            filenamePrefix: "Monthly_Revenue",
                            subtitle: "Monthly Revenue as of"
                        });

                        return;
                    }

                    const sorted = result.data.sort((a, b) => a.month_number - b.month_number);

                    sorted.forEach(d => {
                         const row = `
                            <tr>
                                <td>
                                    <span class="d-none">${d.month_number}</span>
                                    ${d.month}
                                </td>
                                <td>${d.year}</td>
                                <td>₱${Number(d.total_revenue).toLocaleString('en-PH')}</td>
                                <td>${d.product_solds ?? 0}</td>
                            </tr>
                        `;

                        tbody.insertAdjacentHTML("beforeend", row);
                    });

                    initDataTable(".monthlyRevenueTable", {
                        order: [
                            [0, "asc"]
                        ],
                        title: "Monthly Revenue",
                        filenamePrefix: "Monthly_Revenue",
                        subtitle: "Monthly Revenue as of"
                    });
                    
                })
                .catch(error => {
                    console.error("Error fetching monthly revenue data:", error);
                });
        }

        document.getElementById('yearTable').addEventListener('change', updateMonthlyTable);

        updateMonthlyTable()

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        function updateDailyRevenue() {

            const month = document.getElementById("monthTable").value;
            const year = document.getElementById("yearDailyRevenue").value;
            const tbody = document.getElementById("dailyRevenueTableBody");

            if ($.fn.DataTable.isDataTable('.dailyRevenueTable')) {
                $('.dailyRevenueTable').DataTable().destroy();
            }

            tbody.innerHTML = ""

            fetch(`/api/monthlyTransactionTotals.php?getDailyRevenue=1&month=${month}&year=${year}`)
                .then(res => res.json())
                .then(result => {

                    if (!result.success) return;

                    const data = result.data || [];

                    if (data.length === 0) {
                        
                        initDataTable(".dailyRevenueTable", {
                            order: [[0, "desc"]],
                            title: "Daily Revenue",
                            filenamePrefix: "Daily_Revenue",
                        });

                        return;
                    }


                    data.forEach(d => {
                        const row = `
                            <tr>
                                <td class="export-date">
                                    <span class="d-none">${d.sale_date}</span>
                                    ${d.sale_date}
                                </td>

                                <td>${d.day_name}</td>

                                <td>${d.total_solds ?? 0}</td>
            
                                <td class="export-currency">₱${Number(d.total_revenue).toLocaleString('en-PH')}</td>

                            </tr>
                        `;

                        tbody.insertAdjacentHTML("beforeend", row);
                   });

                    initDataTable(".dailyRevenueTable", {
                        order: [
                            [0, "desc"]
                        ],
                        title: `Daily Revenue of ${data[0].sale_date}`,
                        filenamePrefix: "Daily_Revenue",
                    });

                })
                .catch(error => {
                    console.error("Error fetching monthly revenue data:", error);
                });
        }

        document.getElementById('monthTable').addEventListener('change', updateDailyRevenue);
        document.getElementById('yearDailyRevenue').addEventListener('change', updateDailyRevenue);

        updateDailyRevenue()

    });
</script>