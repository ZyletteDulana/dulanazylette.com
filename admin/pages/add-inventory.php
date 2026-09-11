<?php
    include_once "../includes/accessChecker.php";
?>

<div class="content-page">

    <div class="container-fluid add-form-list">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
                        </div>

                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMultipleInventoryModal">
                            <i class="bi bi-plus-lg"></i> Add Multiple Inventory
                        </button>

                    </div>

                    <div class="card-body">

                        <form action="../process/inventoryManagement.php" method="POST" autocomplete="off" data-toggle="validator">

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> Item Name <span class="text-danger">*</span> </label>

                                        <input
                                            type="text"
                                            name="itemName"
                                            class="form-control"
                                            placeholder="Enter Item Name"
                                            data-errors="Please Enter Item Name."
                                            required />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label> Item Stock <span class="text-danger">*</span> </label>

                                        <input
                                            type="number"
                                            name="itemStock"
                                            class="form-control"
                                            placeholder="Enter Item Stock"
                                            data-errors="Please Enter Item Stock."
                                            required />

                                        <div class="help-block with-errors"></div>

                                    </div>

                                </div>

                            </div>

                            <button type="submit" class="btn btn-success mr-2" name="addNewItem">
                                <i class="bi bi-plus-lg"></i>
                                Add Item
                            </button>

                            <button type="reset" class="btn btn-danger"> 
                                <i class="bi bi-x-lg"></i>
                                Clear
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>
        <!-- Page end  -->

    </div>

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> Inventory Activities </h4>
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
                                            <th> Activity Date </th>

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
                                                                                WHERE ul.activity IN('Add Item', 'Update Item', 'Delete Item')
                                                                                ORDER BY ul.activity_date DESC
                                                                                ");
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
                                                    <?php echo htmlspecialchars(formatTimestamp($activityData["activity_date"])); ?>
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

<div class="modal fade" id="addMultipleInventoryModal" tabindex="-1" aria-labelledby="addMultipleInventoryModal" aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addMultipleInventoryModal"> Add Multiple Inventory </h5>

                <button type="button" class="btn-close bg-transparent border border-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form action="../process/inventoryManagement.php" id="inventoryUploadForm" method="POST" enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="mb-4">

                        <label for="inventoryFile" class="form-label fw-semibold"> Upload Excel File: </label>

                        <div class="input-group">

                            <div class="input-group-prepend">
                                <span class="input-group-text"> Upload </span>
                            </div>

                            <div class="custom-file">
                                <input
                                    type="file"
                                    class="custom-file-input"
                                    id="inputGroupFile01"
                                    name="inventoryFile"
                                    accept=".xls,.xlsx"
                                    required>

                                <div class="input-group-prepend">
                                    <label class="custom-file-label" for="inputGroupFile01" id="fileName"> Choose File </label>
                                </div>
                                
                            </div>

                        </div>

                        <div class="form-text text-muted"> Only Excel files are allowed (.xls, .xlsx). </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" name="addMultipleItems"> 
                        <i class="bi bi-upload"></i>
                        Upload File 
                    </button>

                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"> 
                        <i class="bi bi-x-lg"></i>
                        Cancel 
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

