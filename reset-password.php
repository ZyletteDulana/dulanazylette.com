<?php
   require_once "config/databaseConnector.php";
   require_once "config/inputValidators.php";
   require_once "config/notifications.php";
   require_once "config/formatter.php";

   if(!isset($_GET["reset-password-token"])) {
      header("Location: index.php");
      exit();
   }

   else {
      try {
         $resetPasswordToken = sanitizeInputs($_GET["reset-password-token"]);

         // Validate reset password token
         $validateToken = $conn->prepare("SELECT * FROM users_table WHERE reset_password_token = ? LIMIT 1");
         $validateToken->bind_param("s", $resetPasswordToken);
         $validateToken->execute();
         
         $queryResult = $validateToken->get_result();

         if($queryResult->num_rows === 1) {
            $resetTokenDetails = $queryResult->fetch_object();

            if(strtotime($resetTokenDetails->reset_token_expiry) > time()) {

               $userId = $resetTokenDetails->user_id;
               $fullName = $resetTokenDetails->first_name . " " . $resetTokenDetails->last_name;
               $tokenExpiry = $resetTokenDetails->reset_token_expiry;
            }

            else {
               // Token Expired
                displayNotification("error", "Invalid Token", "Reset password token expired! Please try again.");
               header("Location: index.php");
               exit();
            }
         }

         else {
            // Invalid Token
            displayNotification("error", "Invalid Token", "Invalid reset password token!");
            header("Location: index.php");
            exit();
         }
      }

      catch(mysqli_sql_exception $e) {
         header("Location: index.php");
         exit();
      }
   }
?>

<!doctype html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title> Instambay FoodHub | Reset Password </title>

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
                  <div class="card auth-card shadow-lg border-0 rounded-4">
                     <div class="card-body p-0">

                        <div class="d-flex align-items-center auth-content">

                           <!-- LEFT PANEL -->
                           <div class="col-lg-7 align-self-center">
                              <div class="p-4">
                                 <h2 class="mb-2 fw-bold"> Reset Password </h2>
                                 <p class="text-muted mb-1">
                                    Hello, <b><?php echo htmlspecialchars($fullName); ?></b>! Please enter your new password.
                                 </p>
                                 <p class="text-muted mb-4">
                                    This link will expire in <b><?php echo htmlspecialchars(formatTimestamp($tokenExpiry)); ?></b>
                                 </p>

                                 <form method="POST" action="process/userAuthentication.php" autocomplete="off">

                                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars(base64_encode($userId)); ?>">
                                    <input type="hidden" name="resetPasswordToken" value="<?php echo htmlspecialchars($resetPasswordToken); ?>">

                                    
                                    <div class="row">

                                       <div class="col-lg-12">
                                          <div class="floating-label form-group mb-3">
                                             <input class="floating-input form-control password-input-field"
                                                type="password"
                                                name="newPassword"
                                                id="newPassword"
                                                placeholder=""
                                                required
                                                data-bs-toggle="tooltip"
                                                data-bs-html="true"
                                                data-bs-placement="bottom"
                                                data-bs-title='
                                                <span style="font-weight:bold;"> Your <b>Password</b> Must have:</span>
                                                <ul class="fw-bold mb-0" style="margin:0; list-style-type:disc;">
                                                   <li>At least 1 uppercase letter</li>
                                                   <li>At least 1 lowercase letter</li>
                                                   <li>At least 1 digit</li>
                                                   <li>At least 1 special character (!@#$%^&*)</li>
                                                   <li>8-16 characters in total</li>
                                                </ul>
                                             '
                                                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,16}$">
                                             <label for="newPassword">New Password</label>
                                          </div>
                                       </div>

                                       <div class="col-lg-12">
                                          <div class="floating-label form-group mb-3">
                                             <input class="floating-input form-control password-input-field"
                                                type="password"
                                                name="confirmPassword"
                                                id="confirmPassword"
                                                placeholder=""
                                                required>
                                             <label for="confirmPassword">Confirm Password</label>
                                          </div>
                                       </div>

                                       <div class="col-lg-6 d-flex align-items-center mb-3">
                                          <div class="custom-control custom-checkbox mb-0">
                                             <input type="checkbox" class="custom-control-input" id="customCheck1"
                                                onclick="showPasswords()">
                                             <label class="custom-control-label control-label-1" for="customCheck1">
                                                Show Password
                                             </label>
                                          </div>
                                       </div>

                                    </div>

                                    <button type="submit"
                                       class="btn btn-dark custom-primary-button w-50 fw-semibold" name="submitNewPassword">
                                       Reset Password
                                    </button>

                                 </form>

                              </div>
                           </div>

                           <!-- RIGHT PANEL -->
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

   <script src="assets/custom/js/show-passwords.js"></script>

   <!-- SweetAlert -->
   <script src="includes/cdn/sweetalert.min.js"></script>
   
   <?php
      include_once "includes/sweetalert.php";
   ?>

   <script src="assets/custom/js/show-passwords.js"></script>
   <script src="assets/js/backend-bundle.min.js"></script>
   <script src="assets/js/table-treeview.js"></script>
   <script src="assets/js/customizer.js"></script>
   <script async src="assets/js/chart-custom.js"></script>
   <script src="assets/js/app.js"></script>

   <!-- Bootstrap 5 JS -->
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Tool Tips -->
   <script>
      document.addEventListener("DOMContentLoaded", function() {
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
         var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
               return new bootstrap.Tooltip(tooltipTriggerEl);
         });
      });
</script>

</body>

</html>