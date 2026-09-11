<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
                        </div>

                        <?php if($userType === "Admin"): ?>
                            <a href="dashboard.php?page=add-inventory" class="btn btn-success add-list"><i class="las la-plus mr-3"></i> Add Item </a>
                        <?php endif;?>

                    </div>

                    <div class="card-body">

                        <div class="col-lg-12">

                            <div class="table-responsive rounded mb-3">

                                <table class="data-tables table mb-0 tbl-server-info text-center custom-table inventoryTable">

                                    <thead class="bg-white text-uppercase">

                                        <tr class="ligth ligth-data">

                                            <th> Item </th>
                                            <th> Stock </th>
                                            <th> Item Status </th>
                                            <th> Added At </th>
                                            <th> Added By </th>

                                            <?php if($userType === "Admin"): ?>
                                                <th class="export-ignore"> Action </th>
                                            <?php endif; ?>

                                        </tr>

                                    </thead>

                                    <tbody class="light-body text-center">

                                        <?php
                                        $getInventoryItems = $conn->prepare("SELECT 
                                                                                    i.*,
                                                                                    u.first_name as 'added_by_name' 
                                                                                FROM inventory_table i
                                                                                LEFT JOIN users_table u
                                                                                ON i.added_by = u.user_id
                                                                                ORDER BY i.item_stock ASC");
                                        $getInventoryItems->execute();

                                        $inventoryItemsQueryResult = $getInventoryItems->get_result();

                                        while ($inventoryData = $inventoryItemsQueryResult->fetch_array()) {
                                        ?>
                                            <tr>

                                                <td class="fw-bold">
                                                    <?php echo htmlspecialchars($inventoryData["item_name"]); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($inventoryData["item_stock"]); ?>
                                                </td>

                                                <td>
                                                    <?php
                                                    if ($inventoryData["item_stock"] >= 50) {
                                                    ?>
                                                        <span class="badge bg-success w-50">
                                                            Normal
                                                        </span>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <span class="badge bg-danger w-50">
                                                            Low Stock
                                                        </span>
                                                    <?php
                                                    }
                                                    ?>

                                                </td>

                                                <td class="export-date">
                                                    <?php echo htmlspecialchars(formatTimestamp($inventoryData["added_at"])); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($inventoryData["added_by_name"]); ?>
                                                </td>

                                                <?php if($userType === "Admin"): ?>
                                                    <td class="export-ignore">
                                                        <div class="d-flex align-items-center justify-content-center">

                                                            <div>
                                                                <button 
                                                                class="btn btn-warning btn-sm px-2 mr-2"
                                                                data-toggle="tooltip"
                                                                data-placement="top"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#updateItem<?php echo htmlspecialchars($inventoryData["item_id"]); ?>"
                                                                title=""
                                                                data-original-title="Edit Item"
                                                                >
                                                                    <i class="bi bi-pencil square mr-0"></i>
                                                                </button>
                                                            </div>

                                                            <div>
                                                                <form action="../process/inventoryManagement.php" method="POST" id="deleteItemForm">
                                                                    
                                                                    <input type="hidden" name="deleteItem" value="1">
                                                                    <input type="hidden" name="itemName" value="<?php echo htmlspecialchars($inventoryData["item_name"]); ?>">
                                                                    <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($inventoryData["item_id"]); ?>">

                                                                    <button
                                                                    type="submit"
                                                                    class="btn btn-danger btn-sm px-2 mr-2"
                                                                    data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title=""
                                                                    data-original-title="Delete Item"
                                                                    onclick="confirmAction(
                                                                        event,
                                                                        this.form,
                                                                        'deleteItemForm',
                                                                        'Delete Item: <?php echo htmlspecialchars($inventoryData['item_name']); ?>?',
                                                                        'warning',
                                                                        'Are you sure you want to delete this item?',
                                                                        'Delete Item',
                                                                        '#E08DB4'
                                                                    )"
                                                                    >
                                                                        <i class="ri-delete-bin-line mr-0"></i>      
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>

                                            <div class="modal fade" id="updateItem<?php echo htmlspecialchars($inventoryData["item_id"]); ?>" tabindex="-1" aria-labelledby="addMultipleInventoryModal" aria-hidden="true">

                                                <div class="modal-dialog modal-md modal-dialog-centered">

                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="updateItem<?php echo htmlspecialchars($inventoryData["item_id"]); ?>"> Update Item: <?php echo htmlspecialchars($inventoryData["item_name"]); ?> </h5>

                                                            <button type="button" class="btn-close bg-transparent border border-0" data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </div>

                                                        <form action="../process/inventoryManagement.php" id="inventoryUploadForm" method="POST" data-toggle="validator">

                                                            <input type="hidden" name="itemId" value="<?php echo htmlspecialchars($inventoryData["item_id"]); ?>">
                                                            
                                                            <div class="modal-body">

                                                                <div class="row">

                                                                    <div class="col-md-12">

                                                                        <div class="form-group">

                                                                            <label> Item Name </label>

                                                                            <input
                                                                                type="text"
                                                                                name="itemName"
                                                                                class="form-control"
                                                                                placeholder="Enter Item Name"
                                                                                data-errors="Please Enter Item Name."
                                                                                value="<?php echo htmlspecialchars($inventoryData["item_name"]); ?>"
                                                                                required />

                                                                            <div class="help-block with-errors"></div>

                                                                        </div>

                                                                    </div>

                                                                    <div class="col-md-12">

                                                                        <div class="form-group">

                                                                            <label> Item Stock </label>

                                                                            <input
                                                                                type="number"
                                                                                name="itemStock"
                                                                                class="form-control"
                                                                                placeholder="Enter Item Stock"
                                                                                data-errors="Please Enter Item Stock."
                                                                                value="<?php echo htmlspecialchars($inventoryData["item_stock"]); ?>"
                                                                                required />

                                                                            <div class="help-block with-errors"></div>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"> Cancel </button>
                                                                <button type="submit" class="btn btn-primary" name="updateItem"> 
                                                                    <i class="bi bi-pencil"></i>
                                                                    Update Item 
                                                                </button>
                                                            </div>

                                                        </form>

                                                    </div>

                                                </div>

                                            </div>
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