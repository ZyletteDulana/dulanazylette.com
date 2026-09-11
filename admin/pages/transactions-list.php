<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
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
                                            <th> Items </th>
                                            <th> Total Amount </th>
                                            <th> Processed By </th>
                                            <th> Transaction Date </th>

                                        </tr>

                                    </thead>

                                    <tbody class="light-body text-center">

                                        <?php
                                        $getTransactionsList = $conn->prepare("SELECT
                                                                                    t.transaction_id, t.transaction_date,
                                                                                    GROUP_CONCAT(CONCAT(p.product_name, ' - ', ti.product_quantity, 'x') ORDER BY ti.transaction_item_id ASC SEPARATOR ', ') AS 'products_list',
                                                                                    t.total_amount,
                                                                                    u.first_name AS 'processed_by_name'
                                                                                FROM transactions_table t
                                                                                LEFT JOIN transaction_items_table ti
                                                                                ON t.transaction_id = ti.transaction_id
                                                                                LEFT JOIN products_table p
                                                                                ON ti.product_id = p.product_id
                                                                                LEFT JOIN users_table u
                                                                                ON t.processed_by = u.user_id
                                                                                GROUP BY t.transaction_id
                                                                                ORDER BY t.transaction_date DESC
                                                                                ");
                                        $getTransactionsList->execute();

                                        $transactionsListResult = $getTransactionsList->get_result();

                                        while ($transactionData = $transactionsListResult->fetch_array()) {
                                        ?>
                                            <tr>

                                                <td class="fw-bold">
                                                    <?php echo htmlspecialchars($transactionData["transaction_id"]); ?>
                                                </td>

                                                <td class="text-truncate" 
                                                    style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                                    data-bs-toggle="tooltip" 
                                                    data-bs-placement="top" 
                                                    title="<?php echo htmlspecialchars($transactionData['products_list']) ?>">
                                                    <?php echo htmlspecialchars($transactionData['products_list']) ?>
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
                                                    <?php echo htmlspecialchars(formatTimestamp($transactionData["transaction_date"])); ?>
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