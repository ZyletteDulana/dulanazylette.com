<div class="iq-sidebar sidebar-default ">

    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">

        <a href="dashboard.php?page=main" class="header-logo">
            <img src="../assets/custom/images/web-image-1.png" class="img-fluid rounded-normal light-logo" alt="Website Logo" style="height: 100px; width: 100px;">
        </a>

        <div class="iq-menu-bt-sidebar ml-0">
            <i class="las la-bars wrapper-menu"></i>
        </div>

    </div>

    <div class="data-scrollbar" data-scroll="1">

        <nav class="iq-sidebar-menu">

            <ul id="iq-sidebar-toggle" class="iq-menu">

                <li class="nav-item active active-page">
                    <a href="dashboard.php?page=main" class="nav-link d-flex align-items-center fs-1">
                        <i class="bi bi-grid"></i>
                        <span class="nav-text ml-3"> Dashboard </span>
                    </a>
                </li>

                <?php
                    $transactionPages = [
                        "add-transaction",
                        "transactions-list"
                    ];

                    $activeTransactionPage = in_array($pageName, $transactionPages);
                ?>

                <li class="nav-item <?php echo $activeTransactionPage ? "active" : ""; ?>">

                    <a href="#transactions" class="collapse <?php echo $activeTransactionPage ? "active-page" : ""; ?>" data-toggle="collapse" aria-expanded="false">

                        <i class="bi bi-receipt"></i>

                        <span class="ml-4"> Transactions </span>

                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>

                    </a>

                    <ul id="transactions" class="iq-submenu collapse <?php echo $activeTransactionPage ? "show" : ""; ?>" data-parent="#iq-sidebar-toggle">

                        <li class="<?php echo $pageName === "add-transaction" ? "active active-page" : ""; ?>">
                            <a href="dashboard.php?page=add-transaction" class="nav-link">
                                <i class="bi bi-plus-circle"></i>
                                Add Transaction
                            </a>
                        </li>

                        <li class="<?php echo $pageName === "transactions-list" ? "active active-page" : ""; ?>">
                            <a href="dashboard.php?page=transactions-list" class="nav-link">
                                <i class="bi bi-list-check"></i>
                                View Transactions
                            </a>
                        </li>

                    </ul>

                </li>

                <?php
                    $inventoryPages = [
                        "add-inventory",
                        "inventory-list"
                    ];

                    $activeInventoryPage = in_array($pageName, $inventoryPages);
                ?>

                <li class="nav-item <?php echo $activeInventoryPage ? "active" : ""; ?>">

                    
                    <a href="#inventory" class="collapsed <?php echo $activeInventoryPage ? "active-page" : ""; ?>" data-toggle="collapse" aria-expanded="false">

                        <i class="bi bi-box-seam"></i>
                        <span class="ml-4"> Inventory </span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>

                    <ul id="inventory" class="iq-submenu collapse <?php echo $activeInventoryPage ? "show" : ""; ?>" data-parent="#iq-sidebar-toggle">

                        <?php if($userType === "Admin"): ?>
                            <li class="<?php echo $pageName === "add-inventory" ? "active active-page" : ""; ?>">
                                <a href="dashboard.php?page=add-inventory" class="nav-link">
                                    <i class="bi bi-plus-circle"></i>
                                    Add Inventory
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="<?php echo $pageName === "inventory-list" ? "active active-page" : ""; ?>">
                            <a href="dashboard.php?page=inventory-list" class="nav-link">
                                <i class="bi bi-list"></i>
                                View Inventory
                            </a>
                        </li>

                    </ul>
                </li>

                <?php
                    $productPages = [
                        "add-product",
                        "products-list"
                    ];

                    $activeProductsPage = in_array($pageName, $productPages);
                ?>

                <li class="nav-item <?php echo $activeProductsPage ? "active" : ""; ?>">

                    <a href="#products" class="collapsed <?php echo $activeProductsPage ? "active active-page" : ""; ?>" data-toggle="collapse" aria-expanded="false">

                        <i class="bi bi-basket"></i>

                        <span class="ml-4"> Products </span>

                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>

                        </svg>

                    </a>

                    <ul id="products" class="iq-submenu collapse <?php echo $activeProductsPage ? "show" : ""; ?>" data-parent="#iq-sidebar-toggle">

                        <?php if($userType === "Admin"): ?>
                            <li class="<?php echo $pageName === "add-product" ? "active active-page" : ""; ?>">
                                <a href="dashboard.php?page=add-product" class="nav-link">
                                    <i class="bi bi-plus-circle"></i>
                                    Add Product
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="<?php echo $pageName === "products-list" || $pageName === "product-details" ? "active active-page" : ""; ?>">
                            <a href="dashboard.php?page=products-list" class="nav-link">
                                <i class="bi bi-bag"></i>
                                View Products
                            </a>
                        </li>

                    </ul>
                </li>

                <?php
                    $reportsPages = [
                        "revenue-report",
                        "staff-performance",
                        "user-activities"
                    ];

                    $activeReportsPage = in_array($pageName, $reportsPages);
                ?>

                <li class="nav-item <?php echo $activeReportsPage ? "active" : ""; ?>">

                    <a href="#reports" class="collapsed <?php echo $activeReportsPage ? "active active-page" : ""; ?>" data-toggle="collapse" aria-expanded="false">

                        <i class="bi bi-bar-chart-line"></i>

                        <span class="ml-4"> Reports </span>

                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>

                    </a>

                    <ul id="reports" class="iq-submenu collapse <?php echo $activeReportsPage ? "show" : ""; ?>" data-parent="#iq-sidebar-toggle">

                        <?php if($userType === "Admin"): ?>
                            <li class="<?php echo htmlspecialchars($pageName === "revenue-report" ? "active active-page" : ""); ?>">
                                <a href="dashboard.php?page=revenue-report" class="nav-link">
                                    <i class="bi bi-cash-stack"></i>
                                    Revenue Report
                                </a>
                            </li>

                            <li class="<?php echo htmlspecialchars($pageName === "product-performance" ? "active active-page" : ""); ?>">
                                <a href="dashboard.php?page=product-performance" class="nav-link">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    Product Performance
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="<?php echo htmlspecialchars($pageName === "user-activities" ? "active active-page" : ""); ?>">
                            <a href="dashboard.php?page=user-activities" class="nav-link">
                                <i class="bi bi-clock-history"></i>
                                User Activity
                            </a>
                        </li>

                    </ul>

                </li>

                <?php
                    $usersPage = [
                        "add-user",
                        "users-list", "user-info"
                    ];

                    $activeUsersPage = in_array($pageName, $usersPage);
                ?>

                <?php if($userType === "Admin"): ?>

                    <li class="nav-item <?php echo $activeUsersPage ? "active" : ""; ?>">

                        <a href="#users" class="collapsed <?php echo $activeUsersPage ? "active active-page" : ""; ?>" data-toggle="collapse" aria-expanded="false">

                            <i class="bi bi-people"></i>
                            <span class="ml-4"> Users </span>
                            <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="10 15 15 20 20 15"></polyline>
                                <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                            </svg>
                        </a>

                        <ul id="users" class="iq-submenu collapse <?php echo $activeUsersPage ? "show" : ""; ?>" data-parent="#iq-sidebar-toggle">

                            <li class="<?php echo $pageName === "add-user" ? "active active-page" : ""; ?>">
                                <a href="dashboard.php?page=add-user" class="nav-link">
                                    <i class="bi bi-plus-circle"></i>
                                    Add User
                                </a>
                            </li>

                            <li class="<?php echo $pageName === "users-list" || $pageName === "user-info" ? "active active-page" : ""; ?>">
                                <a href="dashboard.php?page=users-list" class="nav-link">
                                    <i class="bi bi-people"></i>
                                    View Users
                                </a>
                            </li>

                        </ul>
                    </li>
                    
                <?php endif; ?>

                <li class="nav-item <?php echo htmlspecialchars($pageName === "profile" ? "active active-page" : ""); ?>">
                    <a href="dashboard.php?page=profile" class="nav-link d-flex align-items-center">
                        <i class="bi bi-person"></i>
                        <span class="nav-text ml-3"> My Profile </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="../logout.php" class="nav-link d-flex align-items-center">
                        <i class="bi bi-box-arrow-left"></i>
                        <span class="nav-text ml-3"> Sign Out </span>
                    </a>
                </li>

            </ul>
        </nav>

    </div>

</div>