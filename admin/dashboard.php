<?php
ob_start();

require_once "../config/databaseConnector.php";
require_once "../config/notifications.php";
require_once "../config/formatter.php";

require_once "../includes/page-titles.php";

$pageName = isset($_GET["page"]) ? $_GET["page"] : "main";

if (!isset($_SESSION["userId"]) || empty($_SESSION["userId"])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$profilePictureFolderPath = "../uploads/user-images/";
$productsPictureFolderPath = "../uploads/product-images/";

$loggedInUser = htmlspecialchars($_SESSION["userId"]);

try {
    $getUserData = $conn->prepare("SELECT * FROM users_table WHERE user_id = ? LIMIT 1");
    $getUserData->bind_param("i", $loggedInUser);
    $getUserData->execute();

    $queryResult = $getUserData->get_result();

    if ($queryResult->num_rows === 1) {
        $userData = $queryResult->fetch_object();

        $firstName = $userData->first_name;
        $middleName = $userData->middle_name ?? "";
        $lastName = $userData->last_name;
        $gender = $userData->gender;
        $emailAddress = $userData->email_address;
        $userType = $userData->user_type;
        $phoneNumber = $userData->phone_number;
        $address = $userData->user_address;
        $profilePicture = $userData->profile_picture;
        $updatedAt = formatTimestamp($userData->updated_at);

        if (empty($profilePicture) || $profilePicture === null || !file_exists($profilePictureFolderPath . $profilePicture)) {
            $profilePicture = "default-photo.png";
        }

        $getTransactionsToday = $conn->prepare("SELECT COUNT(*) AS 'transactions_today' FROM transactions_table WHERE DATE(transaction_date) = CURDATE()");
        $getTransactionsToday->execute();

        $transactionsToday = $getTransactionsToday->get_result()->fetch_array()["transactions_today"];

        $getRevenueToday = $conn->prepare("SELECT SUM(total_amount) AS 'revenue_today' FROM transactions_table WHERE DATE(transaction_date) = CURDATE()");
        $getRevenueToday->execute();

        $revenueToday = $getRevenueToday->get_result()->fetch_array()["revenue_today"];
    } else {
        displayNotification("error", "Invalid Account", "Invalid account! Please login to continue.");
        header("Location: ../index.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    session_destroy();
    displayNotification("error", "Invalid Account", "Something went wrong: " . $e->getMessage());
    header("Location: ../index.php");
    exit();
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php
    include_once "../includes/websiteName.php";
    ?>
    
    <!-- CDN Converted to Offline -->
    <link rel="stylesheet" href="../includes/cdn/bootstrap-icons.min.css">
    <!-- <link rel="stylesheet" href="../includes/cdn/bootstrap.min.css"> -->

    
    <!-- CDN Converted to Offline -->

    <?php
    include_once "../includes/css-files.php";
    ?>

    
    <style>
        .custom-table th,
        .custom-table td {
            width: 200px;
        }

        .custom-table td {
            font-size: 13px;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class=" ">

    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center">
        </div>
    </div>
    <!-- loader END -->

    <div style="position: fixed; top: 0; right: 0; padding: 16px; z-index: 9999; display: none;" id="toastWrapper">

        <div id="lowStockToast" role="alert" aria-live="assertive" aria-atomic="true"
            style="min-width: 300px; background-color: #fff; border-radius: 8px; 
               box-shadow: 0 4px 12px rgba(0,0,0,0.15); overflow: hidden;">

            <div style="display: flex; align-items: center; padding: 10px 15px; border-bottom: 1px solid #ddd;">

                <img src="../assets/custom/images/web-image-1.png"
                    style="border-radius: 6px; margin-right: 10px;"
                    width="50" height="50" alt="warning">

                <strong style="flex-grow: 1; font-size: 14px;"> Low Stock Alert </strong>

                <br>

                <small style="color: #6c757d; margin-right: 10px;" id="alertDate">
                    
                </small>

                <button type="button" onclick="this.parentElement.parentElement.style.display='none'"
                    style="background: none; border: none; font-size: 18px; cursor: pointer;">
                    &times;
                </button>

            </div>

            <div id="toastBody" style="padding: 12px 15px; font-size: 14px;">
                <!-- dynamic message here -->
            </div>

        </div>

    </div>

    <!-- Wrapper Start -->
    <div class="wrapper">


        <?php
        include_once "../includes/sidebar.php";
        ?>

        <?php
        include_once "../includes/topNavbar.php";
        ?>

        <?php
        $pagePath = "pages/$pageName.php";

        if (file_exists($pagePath)) {
            include_once $pagePath;
        } else {
            $pageName = "main";
            include_once "pages/main.php";
        }
        ?>

    </div>
    <!-- Wrapper End-->

    <?php
    include_once "../includes/footer.php";
    ?>

    <?php

    // Offline CDN 
    //include_once "../includes/cdn-files.php";

    include_once "../includes/script-files.php";
    include_once "../includes/sweetalert.php";
    include_once "../includes/stockChecker.php";
    ?>

    <!-- CDN Converted to Offline -->
     <!-- SweetAlert -->
    <script src="../includes/cdn/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert -->
    <script src="../includes/cdn/sweetalert.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="../includes/cdn/buttons.dataTables.min.css">
    <script src="../includes/cdn/dataTables.buttons.min.js"></script>
    <script src="../includes/cdn/buttons.html5.min.js"></script>
    <script src="../includes/cdn/buttons.print.min.js"></script>

    <!-- For Excel -->
    <script src="../includes/cdn/jszip.min.js"></script>

    <!-- Charts -->
    <script src="../includes/cdn/chart.min.js"></script>
    <script src="../includes/cdn/chartjs-plugin-datalabels.min.js"></script>
    <script src="../assets/custom/js/chart-generator.js"></script>

    <!-- CDN Converted to Offline -->

</body>

</html>

<?php
ob_end_flush();
?>