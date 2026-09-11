<?php
    session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Instambay FoodHub | Forgot Password </title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/custom/images/web-image-1.png" />
    
    <link rel="stylesheet" href="assets/css/backend-plugin.min.css">
    <link rel="stylesheet" href="assets/css/backend.css?v=1.0.0">
    <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">
</head>

<body class="">

    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <!-- loader END -->

    <div class="wrapper">

        <section class="login-content">

            <div class="container">

                <div class="row align-items-center justify-content-center height-self-center">

                    <div class="col-lg-8">

                        <div class="card auth-card shadow-lg border border-light rounded-4">

                            <div class="card-body p-0">

                                <div class="d-flex align-items-center auth-content">

                                    <!-- LEFT SIDE -->
                                    <div class="col-lg-7 align-self-center">

                                        <div class="p-4">

                                            <h2 class="mb-2 fw-bold"> Reset Password </h2>
                                            <p class="text-muted mb-4">
                                                Enter your email address and we'll send you instructions to reset your password.
                                            </p>

                                            <form method="POST" action="process/userAuthentication.php" autocomplete="off">

                                                <div class="row">

                                                    <div class="col-lg-12">
                                                        <div class="floating-label form-group mb-3">
                                                            <input class="floating-input form-control"
                                                                type="email"
                                                                name="emailAddress"
                                                                placeholder=" "
                                                                required>
                                                            <label>Email Address</label>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-3 mx-1">
                                                    <a href="index.php" class="text-primary">
                                                        Back to Login
                                                    </a>
                                                </div>

                                                <button type="submit" name="submitEmailAddress" class="btn btn-dark custom-primary-button w-50 fw-semibold">
                                                    Send Reset Link
                                                </button>

                                            </form>
                                        </div>
                                    </div>

                                    <!-- RIGHT SIDE -->
                                    <div class="col-lg-5 content-right d-flex align-items-center justify-content-center rounded-end-4">
                                        
                                        <div class="text-center p-3">
                                            <img src="assets/custom/images/web-image-1.png" class="img-fluid image-right mb-3" alt="InstambayHub">
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    </div>

    <!-- SweetAlert -->
    <script src="includes/cdn/sweetalert.min.js"></script>

    <?php
      include_once "includes/sweetalert.php";
    ?>

    <!-- Backend Bundle JavaScript -->
    <script src="assets/js/backend-bundle.min.js"></script>
    <script src="assets/js/table-treeview.js"></script>
    <script src="assets/js/customizer.js"></script>
    <script async src="assets/js/chart-custom.js"></script>
    <script src="assets/js/app.js"></script>

</body>

</html>