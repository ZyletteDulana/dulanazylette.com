<div class="content-page">

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-12">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div class="header-title d-flex justify-content-between align-items center">
                            <h4 class="card-title"> <?php echo htmlspecialchars($pageTitles[$pageName]); ?> </h4>
                        </div>

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-hover align-middle text-center data-tables">
                            <thead class="table-light">
                                <tr>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $displayProducts = $conn->prepare("SELECT * FROM products_table ORDER BY product_name ASC");
                                $displayProducts->execute();

                                $productsQueryResult = $displayProducts->get_result();

                                while ($productData = $productsQueryResult->fetch_array()) {
                                    $productImage = $productData["product_image"] ?? "default-product-image.png";
                                ?>

                                    <tr>
                                        <!-- Image -->
                                        <td>
                                            <img src="<?php echo htmlspecialchars($productsPictureFolderPath . $productImage); ?>"
                                                style="height:50px; width:auto; object-fit:cover;"
                                                alt="Product Image">
                                        </td>

                                        <!-- Name -->
                                        <td class="text-truncate" style="max-width:200px;">
                                            <?php echo htmlspecialchars($productData['product_name']); ?>
                                        </td>

                                        <!-- Price -->
                                        <td>
                                            ₱<?php echo amountFormatter($productData['product_price']); ?>
                                        </td>

                                        <!-- Action -->
                                        <td>
                                            <button class="btn btn-sm btn-success select-product"
                                                data-id="<?php echo $productData['product_id']; ?>"
                                                data-name="<?php echo htmlspecialchars($productData['product_name']); ?>"
                                                data-price="<?php echo $productData['product_price']; ?>">
                                                Select
                                            </button>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>
                        </table>

                    </div>

                </div>

            </div>


            <div class="col-sm-12">

                <form action="../process/transactionManagement.php" method="POST">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">

                            <div class="header-title d-flex justify-content-between align-items center">
                                <h4 class="card-title"> Transaction Details </h4>
                            </div>

                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table mb-0 tbl-server-info text-center custom-table" id="transactionTable">

                                    <thead class="table-light">

                                        <tr>

                                            <th> Product Name </th>
                                            <th> Product Price </th>
                                            <th> Product Quantity </th>
                                            <th> Subtotal </th>
                                            <th> Action </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                    </tbody>

                                    <tfoot>

                                        <tr>
                                            <th colspan="3"></th>
                                            <th id="totalAmount">
                                                Total:
                                                <span class="text-dark" id="totalAmount"> ₱0.00 </span>
                                            </th>

                                            <th>
                                                <button class="btn btn-primary btn-sm" type="submit" name="addNewTransaction">
                                                    Add Transaction
                                                </button>
                                            </th>
                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
    const transactionTable = document.querySelector('#transactionTable tbody');
    const totalAmountEl = document.getElementById('totalAmount');

    function updateTotal() {

        let total = 0;

        transactionTable.querySelectorAll('tr').forEach(row => {
            const price = parseFloat(row.dataset.price);

            let qty = parseInt(row.querySelector('input.qty-input').value);
            if (isNaN(qty) || qty < 1) qty = 1;

            const subtotal = price * qty;
            row.querySelector('.subtotal').innerText = '₱' + subtotal.toFixed(2);
            total += subtotal;
        });

        totalAmountEl.innerText = '₱' + total.toFixed(2);
    }

    // Add product to table
    document.querySelectorAll('.select-product').forEach(btn => {

        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = parseFloat(btn.dataset.price);

            // Prevent duplicate product
            if (transactionTable.querySelector(`tr[data-id='${id}']`)) return;

            const row = document.createElement('tr');
            row.dataset.id = id;
            row.dataset.price = price;
            row.innerHTML = `
                <td>
                    ${name}<input type="hidden" name="productId[]" value="${id}">
                </td>

                <td>
                    ₱${price.toFixed(2)}
                </td>

                <td>
                    <input type="number" name="quantity[${id}]" value="1" min="1" class="form-control form-control-sm qty-input">
                </td>

                <td class="subtotal">
                    ₱${price.toFixed(2)}
                </td>

                <td>
                    <button type="button" class="btn text-danger remove-product">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;

            transactionTable.appendChild(row);
            updateTotal();

        });
    });

    // Remove product
    transactionTable.addEventListener('click', e => {
        if (e.target.closest('.remove-product')) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    });

    // Update subtotal when quantity changes
    transactionTable.addEventListener('input', e => {
        if (e.target.classList.contains('qty-input')) {
            // Ensure quantity is numeric and >= 1
            if (e.target.value === '' || parseInt(e.target.value) < 1) e.target.value = 1;
            updateTotal();
        }
    });
</script>