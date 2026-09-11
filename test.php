<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="toast-container position-fixed top-0 end-0 p-3">

        <div id="lowStockToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">

            <div class="toast-header">
                <img src="assets/custom/images/web-image-1.png" class="rounded me-2" width="50" height="50" alt="warning">
                <strong class="me-auto"> Low Stock Alert </strong>
                <small class="text-muted"> Apr. 13, 2026, 07:27 PM </small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>

            <div class="toast-body" id="toastBody">
                <!-- dynamic message here -->
            </div>

        </div>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</body>

</html>

