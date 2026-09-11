<?php
session_start();
?>

<!doctype html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

   <title>
      Instambay FoodHub | Login
   </title>

   <!-- Favicon -->
   <link rel="shortcut icon" type="image/x-icon" href="assets/custom/images/web-image-1.png" />

   <link rel="stylesheet" href="assets/css/backend-plugin.min.css">
   <link rel="stylesheet" href="assets/css/backend.css?v=1.0.0">
   <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
   <link rel="stylesheet" href="assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css">
   <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css">

   <link rel="stylesheet" href="assets/custom/css/styles.css">

</head>

<body class=" ">

   <!-- loader Start -->
   <div id="loading">
      <div id="loading-center">
      </div>
   </div>
   <!-- loader END -->

   <div class="wrapper">

      <section class="login-content">

         <div class="container">

            <div class="row align-items-center justify-content-center height-self-center">

               <div class="col-lg-8">

                  <div class="card auth-card shadow-lg border rounded-4 border-light">

                     <div class="card-body p-0">

                        <div class="d-flex align-items-center auth-content">

                           <!-- LEFT SIDE -->
                           <div class="col-lg-7 align-self-center">

                              <div class="p-4">

                                 <h2 class="mb-2 fw-bold"> InstambayHub </h2>
                                 <p class="text-muted mb-4">
                                    Admin Dashboard Access — manage orders, products, and daily operations.
                                 </p>

                                 <form method="POST" action="process/userAuthentication.php" autocomplete="off">

                                    <div class="row">

                                       <div class="col-lg-12">
                                          <div class="floating-label form-group mb-3">
                                             <input class="floating-input form-control" type="email" id="emailAddress"
                                                name="emailAddress" placeholder="" value="<?php echo $_SESSION["inputtedEmailAddress"] ?? ""; ?>" required>
                                             <label for="emailAddress"> Email Address </label>
                                          </div>
                                       </div>

                                       <div class="col-lg-12">
                                          <div class="floating-label form-group mb-3">
                                             <input class="floating-input form-control password-input-field"
                                                type="password" name="password" id="password" placeholder="" required>
                                             <label for="password"> Password </label>
                                          </div>
                                       </div>

                                       <div class="col-lg-6 d-flex align-items-center">
                                          <div class="custom-control custom-checkbox mb-0">
                                             <input type="checkbox" class="custom-control-input" id="customCheck1"
                                                onclick="showPasswords()">
                                             <label class="custom-control-label control-label-1" for="customCheck1">
                                                Show Password
                                             </label>
                                          </div>
                                       </div>

                                       <div class="col-lg-6 d-flex justify-content-end mb-2">
                                          <a href="forgot-password.php" class="text-primary">
                                             Forgot Password?
                                          </a>
                                       </div>

                                    </div>

                                    <button type="submit" class="btn btn-dark custom-primary-button w-50 fw-semibold" name="submitLoginData">
                                       Login to Dashboard
                                    </button>

                                    <p class="text-muted small mt-3">
                                       Authorized personnel only.
                                    </p>

                                 </form>

                              </div>

                           </div>

                           <!-- RIGHT SIDE -->
                           <div class="col-lg-5 content-right d-flex align-items-center justify-content-center">

                              <div class="text-center p-3">
                                 <img src="assets/custom/images/web-image-1.png" class="img-fluid image-right mb-3"
                                    alt="IstnambayHub">
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

   <script src="assets/custom/js/show-passwords.js"></script>

   <!-- SweetAlert -->
   <script src="includes/cdn/sweetalert.min.js"></script>

   <?php
   include_once "includes/sweetalert.php";
   ?>

   <!-- Backend Bundle JavaScript -->
   <script src="assets/js/backend-bundle.min.js"></script>
   <!-- Table Treeview JavaScript -->
   <script src="assets/js/table-treeview.js"></script>
   <!-- Chart Custom JavaScript -->
   <script src="assets/js/customizer.js"></script>
   <!-- Chart Custom JavaScript -->
   <script async src="assets/js/chart-custom.js"></script>
   <!-- app JavaScript -->
   <script src="assets/js/app.js"></script>

   <script>
      const alertSound = new Audio("assets/custom/audio/warning-sound.mp3");

      let soundUnlocked = false;

      function unlockAudio() {
         alertSound.play().then(() => {

            alertSound.pause();
            alertSound.currentTime = 0;

            localStorage.setItem("audioUnlocked", "true");
         }).catch(() => {});
      }

      document.addEventListener("click", unlockAudio, { once: true })
   </script>

</body>

</html>