<!-- Backend Bundle JavaScript -->
<script src="../assets/js/backend-bundle.min.js"></script>

<!-- Table Treeview JavaScript -->
<script src="../assets/js/table-treeview.js"></script>

<!-- Chart Custom JavaScript -->
<script src="../assets/js/customizer.js"></script>

<!-- Chart Custom JavaScript -->
<script async src="../assets/js/chart-custom.js"></script>

<!-- app JavaScript -->
<script src="../assets/js/app.js"></script>

<!-- Others -->
<script src="../assets/custom/js/show-passwords.js"></script>
<script src="../assets/custom/js/image-preview.js"></script>

<!-- DataTables and Table Exports -->
<script src="../assets/custom/js/export-tables.js"></script>

<!-- Bootstrap 5 JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->

<!-- Export -->

<!-- Tool Tips -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<!-- Image Preview -->
<script>
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        let fileName = e.target.files[0].name;
        document.getElementById('fileName').innerText = fileName;
    });
</script>

<!-- Transactions -->
<script>
    $(document).ready(function() {
        $(document).ready(function() {

            initDataTable(".transactionsTable", {
                order: [
                    [4, "desc"]
                ],
                title: "Transactions Report",
                filenamePrefix: "Transactions_Report",
                subtitle: "Transactions as of",
            });

        });
    })
</script>

<!-- Inventory -->
<script>
    $(document).ready(function() {
        $(document).ready(function() {

            initDataTable(".inventoryTable", {
                order: [
                    [1, "asc"]
                ],
                title: "Inventory List Report",
                filenamePrefix: "Inventory_List_Report",
                subtitle: "Inventory List as of"
            });
        });

    });
</script>

<!-- Products -->
<script>
    $(document).ready(function() {
        $(document).ready(function() {

            initDataTable(".productsTable", {
                order: [
                    [6, "desc"]
                ],
                title: "Producst List",
                filenamePrefix: "Products_List_Report",
                subtitle: "Products as of"
            });
        });

    });
</script>

<!-- Logs -->
<script>
    $(document).ready(function() {
        $(document).ready(function() {

            initDataTable(".userActivitiesTable", {
                order: [
                    [3, "desc"]
                ],
                title: "User Activity Logs",
                filenamePrefix: "Activity_Logs",
                subtitle: "Activity Logs as of"
            });
        });

    });
</script>

<script>
    $(document).ready(function() {
        $(document).ready(function() {

            initDataTable(".userLogsTable", {
                order: [
                    [2, "desc"]
                ],
                title: "User Activity Logs",
                filenamePrefix: "Activity_Logs",
                subtitle: "Activity Logs as of"
            });
        });

    });
</script>

<!-- Sales -->
<script>
    $(document).ready(function() {
        $(document).ready(function() {

            initDataTable(".productSalesTable", {
                order: [
                    [3, "desc"]
                ],
                title: "Product Sales & Revenue Contribution",
                filenamePrefix: "Product_Sales",
                subtitle: "Product Sales & Revenue Contribution as of"
            });
        });

    });
</script>
