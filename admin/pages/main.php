<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-4">

                <div class="card card-transparent card-block card-stretch card-height border-none">

                    <div class="card-body p-0 mt-lg-2 mt-0">

                        <h3 class="mb-3"> Hi, <?php echo htmlspecialchars($firstName); ?>! </h3>
                        <p class="mb-0 mr-4">
                            Your dashboard gives you views of key performance or business process.
                        </p>

                    </div>

                </div>

            </div>

            <div class="col-lg-8">

                <div class="row">

                    <div class="col-lg-6 col-md-6">

                        <div class="card card-block p-2 border-1 border-light shadow-sm rounded-3">

                            <div class="card-body pb-0">

                                <div class="d-flex align-items-center mb-4 card-total-sale">

                                    <div class="icon iq-icon-box-2 bg-info-light me-3">
                                        <i class="bi bi-cart fs-2 text-warning"></i>
                                    </div>

                                    <div>
                                        <p class="mb-1 text-muted"> Transactions Today </p>
                                        <h4 class="mb-0">
                                            <?php echo htmlspecialchars($transactionsToday); ?>
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
                                        <i class="bi bi-cash-stack fs-2 text-warning"></i>
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

                </div>

            </div>

            <!-- <div class="col-lg-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Overview</h4>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001"
                                            data-toggle="dropdown">
                                            This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-right shadow-none"
                                            aria-labelledby="dropdownMenuButton001">
                                            <a class="dropdown-item" href="#">Year</a>
                                            <a class="dropdown-item" href="#">Month</a>
                                            <a class="dropdown-item" href="#">Week</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="layout1-chart1"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Revenue Vs Cost</h4>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton002"
                                            data-toggle="dropdown">
                                            This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-right shadow-none"
                                            aria-labelledby="dropdownMenuButton002">
                                            <a class="dropdown-item" href="#">Yearly</a>
                                            <a class="dropdown-item" href="#">Monthly</a>
                                            <a class="dropdown-item" href="#">Weekly</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="layout1-chart-2" style="min-height: 360px;"></div>
                            </div>
                        </div>
                    </div> -->

            <div class="col-lg-8">

                <div class="card card-block">

                    <div class="card-header d-flex align-items-center justify-content-between">

                        <div class="header-title">
                            <h4 class="card-title"> Top Products this Month </h4>
                        </div>

                    </div>

                    <div class="card-body">

                        <ul class="list-unstyled row top-product mb-0">

                            <?php
                            $getMonthsTopSales = $conn->prepare("SELECT 
                                                                        p.*,
                                                                        SUM(ti.product_quantity) AS 'total_solds'
                                                                    FROM products_table p
                                                                    LEFT JOIN transaction_items_table ti
                                                                    ON p.product_id = ti.product_id
                                                                    LEFT JOIN transactions_table t
                                                                    ON ti.transaction_id = t.transaction_id
                                                                    WHERE MONTH(t.transaction_date) = MONTH(CURRENT_DATE())
                                                                    AND YEAR(t.transaction_date) = YEAR(CURRENT_DATE())
                                                                    AND p.product_type NOT IN('Add-ons')
                                                                    GROUP BY p.product_id, p.product_name
                                                                    ORDER BY total_solds DESC
                                                                    LIMIT 5");
                            $getMonthsTopSales->execute();

                            $topSalesResult = $getMonthsTopSales->get_result();

                            if ($topSalesResult->num_rows === 5) {
                                while ($salesData = $topSalesResult->fetch_array()) {
                                    $productImage = $salesData["product_image"] ?? "default-product-image.png";
                            ?>

                                    <li class="col-lg-3 d-flex">
                                        <div class="card card-block card-stretch card-height mb-0 flex-fill d-flex flex-column">

                                            <div class="card-body flex-grow-1 d-flex flex-column">

                                                <!-- Image container with fixed height -->
                                                <div class="rounded d-flex align-items-center justify-content-center"
                                                    style="height: 150px;">
                                                    <img src="<?php echo htmlspecialchars($productsPictureFolderPath . $productImage); ?>"
                                                        class="img-fluid"
                                                        alt="Product Image"
                                                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                </div>

                                                <!-- Product info -->
                                                <div class="style-text text-left mt-3">
                                                    <h5 class="mb-1 text-truncate" title="<?= htmlspecialchars($salesData['product_name']); ?>">
                                                        <?= htmlspecialchars($salesData["product_name"]); ?>
                                                    </h5>

                                                    <small class="mb-0">
                                                        Solds: <?= htmlspecialchars($salesData["total_solds"]); ?>
                                                    </small>
                                                </div>

                                            </div>
                                        </div>
                                    </li>

                                <?php
                                }
                            } else {
                                ?>
                                <li class="col-lg-3">

                                    <div class="card card-block card-stretch card-height mb-0">

                                        <div class="card-body">

                                            <div class="rounded">

                                                <img src="../assets/custom/images/web-image-1.png"
                                                    class="style-img img-fluid m-auto p-3" alt="Product Image">

                                            </div>

                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1"> No Products </h5>
                                            </div>
                                        </div>
                                    </div>

                                </li>

                                <li class="col-lg-3">

                                    <div class="card card-block card-stretch card-height mb-0">

                                        <div class="card-body">

                                            <div class="rounded">

                                                <img src="../assets/custom/images/web-image-1.png"
                                                    class="style-img img-fluid m-auto p-3" alt="Product Image">

                                            </div>

                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1"> No Products </h5>
                                            </div>
                                        </div>
                                    </div>

                                </li>

                                <li class="col-lg-3">

                                    <div class="card card-block card-stretch card-height mb-0">

                                        <div class="card-body">

                                            <div class="rounded">

                                                <img src="../assets/custom/images/web-image-1.png"
                                                    class="style-img img-fluid m-auto p-3" alt="Product Image">

                                            </div>

                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1"> No Products </h5>
                                            </div>
                                        </div>
                                    </div>

                                </li>

                                <li class="col-lg-3">

                                    <div class="card card-block card-stretch card-height mb-0">

                                        <div class="card-body">

                                            <div class="rounded">

                                                <img src="../assets/custom/images/web-image-1.png"
                                                    class="style-img img-fluid m-auto p-3" alt="Product Image">

                                            </div>

                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1"> No Products </h5>
                                            </div>
                                        </div>
                                    </div>

                                </li>

                                <li class="col-lg-3">

                                    <div class="card card-block card-stretch card-height mb-0">

                                        <div class="card-body">

                                            <div class="rounded">

                                                <img src="../assets/custom/images/web-image-1.png"
                                                    class="style-img img-fluid m-auto p-3" alt="Product Image">

                                            </div>

                                            <div class="style-text text-left mt-3">
                                                <h5 class="mb-1"> No Products </h5>
                                            </div>
                                        </div>
                                    </div>

                                </li>
                            <?php
                            }
                            ?>

                        </ul>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <!-- Header -->
                <div class="card card-transparent card-block card-stretch mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between p-0">
                        <div class="header-title">
                            <h4 class="card-title mb-0"> Best Item All Time </h4>
                        </div>
                    </div>
                </div>

                <?php
                $getBestProducts = $conn->prepare("
                                                SELECT * FROM products_table 
                                                WHERE product_type NOT IN('Add-ons')
                                                ORDER BY product_solds DESC 
                                                LIMIT 2
                                            ");
                $getBestProducts->execute();
                $bestProductsResult = $getBestProducts->get_result();

                if ($bestProductsResult->num_rows === 2) {
                    while ($bestProductData = $bestProductsResult->fetch_assoc()) {
                        $productImage = $bestProductData["product_image"] ?? "default-product-image.png";
                ?>

                        <div class="card card-block card-stretch mb-3 shadow-sm">

                            <div class="card-body">

                                <div class="d-flex align-items-center">

                                    <!-- Image Container -->
                                    <div class="rounded d-flex align-items-center justify-content-center me-3"
                                        style="width: 100px; height: 100px; flex-shrink: 0;">

                                        <img src="<?php echo htmlspecialchars($productsPictureFolderPath . $productImage); ?>"
                                            alt="Product Image"
                                            style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex-grow-1 mx-3" style="min-width: 0;">

                                        <h6 class="mb-1 text-truncate"
                                            style="max-width: 100%;"
                                            title="<?php echo htmlspecialchars($bestProductData['product_name']); ?>">
                                            <?php echo htmlspecialchars($bestProductData["product_name"]); ?>
                                        </h6>

                                        <small class="text-muted d-block text-truncate"
                                            title="Solds: <?php echo htmlspecialchars($bestProductData['product_solds']); ?>">
                                            Solds: <?php echo htmlspecialchars($bestProductData["product_solds"]); ?>
                                        </small>

                                        <small class="text-success fw-bold d-block text-truncate"
                                            title="₱<?php echo htmlspecialchars(amountFormatter(
                                                        $bestProductData["product_price"] * $bestProductData["product_solds"]
                                                    )); ?>">
                                            Revenue: ₱<?php echo htmlspecialchars(amountFormatter(
                                                    $bestProductData["product_price"] * $bestProductData["product_solds"]
                                                )); ?>
                                        </small>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php
                    }
                } else {
                    ?>

                    <!-- Empty State -->
                    <div class="card card-block card-stretch shadow-sm">
                        <div class="card-body text-center">

                            <div class="bg-light d-flex align-items-center justify-content-center rounded mb-3"
                                style="width: 100px; height: 100px; margin: auto;">
                                <img src="../assets/custom/images/web-image-1.png"
                                    class="img-fluid"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>

                            <h6 class="mb-0">No Product</h6>

                        </div>
                    </div>

                    <div class="card card-block card-stretch shadow-sm">
                        <div class="card-body text-center">

                            <div class="bg-light d-flex align-items-center justify-content-center rounded mb-3"
                                style="width: 100px; height: 100px; margin: auto;">
                                <img src="../assets/custom/images/web-image-1.png"
                                    class="img-fluid"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>

                            <h6 class="mb-0">No Product</h6>

                        </div>
                    </div>

                <?php } ?>

            </div>

            <div class="col-lg-12">

                <div class="col-sm-12">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">

                            <div class="header-title d-flex justify-content-between align-items center">
                                <h4 class="card-title"> Transactions Today </h4>
                            </div>

                            <a href="dashboard.php?page=add-transaction" class="btn btn-success add-list"><i class="las la-plus mr-3"></i> Add Transaction </a>

                        </div>

                        <div class="card-body">

                            <div class="col-lg-12">

                                <div class="table-responsive rounded mb-3">

                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table transactionsTable">

                                        <thead class="bg-white text-uppercase">

                                            <tr class="ligth ligth-data">

                                                <th> Transaction ID </th>
                                                <th> Num. of Items </th>
                                                <th> Total Amount </th>
                                                <th> Processed By </th>
                                                <th> Time </th>

                                            </tr>

                                        </thead>

                                        <tbody class="light-body text-center">

                                            <?php
                                            $getTransactionsList = $conn->prepare("SELECT
                                                                                    t.transaction_id, t.transaction_date,
                                                                                    SUM(ti.product_quantity) AS 'total_items',
                                                                                    t.total_amount,
                                                                                    u.first_name AS 'processed_by_name'
                                                                                FROM transactions_table t
                                                                                LEFT JOIN transaction_items_table ti
                                                                                ON t.transaction_id = ti.transaction_id
                                                                                LEFT JOIN products_table p
                                                                                ON ti.product_id = p.product_id
                                                                                LEFT JOIN users_table u
                                                                                ON t.processed_by = u.user_id
                                                                                WHERE date(t.transaction_date) = CURDATE()
                                                                                GROUP BY t.transaction_id
                                                                                ORDER BY t.transaction_id DESC
                                                                                ");
                                            $getTransactionsList->execute();

                                            $transactionsListResult = $getTransactionsList->get_result();

                                            while ($transactionData = $transactionsListResult->fetch_array()) {
                                            ?>
                                                <tr>

                                                    <td class="fw-bold">
                                                        <?php echo htmlspecialchars($transactionData["transaction_id"]); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($transactionData["total_items"]); ?>
                                                    </td>

                                                    <td class="export-currency">
                                                        ₱<?php echo htmlspecialchars($transactionData["total_amount"]); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($transactionData["processed_by_name"]); ?>
                                                    </td>

                                                    <td class="export-date">
                                                        <span class="d-none">
                                                            <?php echo $transactionData["transaction_date"]; ?>
                                                        </span>
                                                        <?php echo htmlspecialchars(formatTime($transactionData["transaction_date"])); ?>
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

            <div class="col-lg-12">

                <div class="col-sm-12">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">

                            <div class="header-title d-flex justify-content-between align-items center">
                                <h4 class="card-title"> User Activities Today </h4>
                            </div>

                        </div>

                        <div class="card-body">

                            <div class="col-lg-12">

                                <div class="table-responsive rounded mb-3">

                                    <table class="data-tables table mb-0 tbl-server-info text-center custom-table userActivitiesTable">

                                        <thead class="bg-white text-uppercase">

                                            <tr class="ligth ligth-data">

                                                <th> User </th>
                                                <th> Activity </th>
                                                <th> Activity Description </th>
                                                <th> Time </th>

                                            </tr>

                                        </thead>

                                        <tbody class="light-body text-center">

                                            <?php
                                            
                                            $getActivitiesList = $conn->prepare("SELECT
                                                                                    u.first_name AS 'user',
                                                                                    ul.*
                                                                                FROM user_logs_table ul
                                                                                LEFT JOIN users_table u
                                                                                ON ul.user_id = u.user_id
                                                                                WHERE 
                                                                                    DATE(ul.activity_date) = CURDATE()
                                                                                    AND (? = 'Admin' OR ul.user_id = ?)
                                                                                ORDER BY TIME(ul.activity_date) DESC
                                                                                ");
                                            $getActivitiesList->bind_param("si", $userType, $loggedInUser);
                                            
                                            $getActivitiesList->execute();

                                            $activitiesListResult = $getActivitiesList->get_result();

                                            while ($activityData = $activitiesListResult->fetch_array()) {
                                            ?>
                                                <tr>

                                                    <td class="fw-bold">
                                                        <?php echo htmlspecialchars($activityData["user"]); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($activityData["activity"]); ?>
                                                    </td>

                                                    <td>
                                                        <?php echo htmlspecialchars($activityData["activity_description"]); ?>
                                                    </td>

                                                    <td class="export-date">
                                                        <span class="d-none">
                                                            <?php echo $activityData["activity_date"]; ?>
                                                        </span>
                                                        <?php echo htmlspecialchars(formatTime($activityData["activity_date"])); ?>
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
        <!-- Page end  -->
    </div>
    
</div>