<?php
    require_once "../config/databaseConnector.php";
    require_once "../config/functions.php";
    require_once "../config/inputValidators.php";
    require_once "../config/emailSender.php";
    require_once "../config/notifications.php";
    require_once "../includes/activityLogger.php";

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submitLoginData"])) {
        
        // Sanitize Inputs
        $emailAddress = filter_var($_POST["emailAddress"], FILTER_SANITIZE_EMAIL);
        $password = sanitizeInputs($_POST["password"]);

        // Error Handling: Check if any of the inputs are empty
        if(empty($emailAddress) || empty($password)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../index.php");
            exit();
        }

        // Error Handling: Check if the Email Address is invalid format
        else if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            displayNotification("error", "Invalid Format", "Invalid email address format! Please try again.");
            header("Location: ../index.php");
            exit();
        }

        else {

            try {
                $conn->begin_transaction();

                // Validate Existing Account in Database
                $validateAccount = $conn->prepare("SELECT * FROM users_table WHERE email_address = ? LIMIT 1");
                $validateAccount->bind_param("s", $emailAddress);
                $validateAccount->execute();

                $queryResult = $validateAccount->get_result();
                
                // Check if there's an account existing
                if($queryResult->num_rows === 1) {
                    $accountData = $queryResult->fetch_object();

                    // Validate hashed password from the database
                    if(password_verify($password, $accountData->password)) {
                        $userId = $accountData->user_id;

                        session_regenerate_id(true);

                        unset($_SESSION["inputtedEmailAddress"]);
                        
                        // Set session
                        $_SESSION["userId"] = $userId;

                        // Add user activity log to track activities
                        addUserActivity($userId, "Log in", "Logged in");

                        $conn->commit();

                        // Redirect to dashboard page
                        header("Location: ../admin/dashboard.php?page=main");
                        exit();
                    }

                    else {
                        $_SESSION["inputtedEmailAddress"] = $emailAddress;
                        // Error Handling: Invalid password
                        $conn->rollback();
                        displayNotification("error", "Invalid Account", "Invalid email address or password!");
                        header("Location: ../index.php");
                        exit();
                    }
                }

                else {
                    $_SESSION["inputtedEmailAddress"] = $emailAddress;
                    // Error Handling: No account existing
                    $conn->rollback();
                    displayNotification("error", "Invalid Account", "Invalid email address or password!");
                    header("Location: ../index.php");
                    exit();
                }
            }

            catch(mysqli_sql_exception $e) {
                $_SESSION["inputtedEmailAddress"] = $emailAddress;
                $conn->rollback();
                displayNotification("error", "Error Occured", "Something went wrong: " . $e->getMessage());
                header("Location: ../index.php");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submitEmailAddress"])) {
        
        // Sanitize Inputs
        $emailAddress = filter_var($_POST["emailAddress"], FILTER_SANITIZE_EMAIL);

        // Error Handling: Check if any of the inputs are empty
        if(empty($emailAddress)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../forgot-password.php");
            exit();
        }

        // Error Handling: Check if the Email Address is invalid format
        else if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            displayNotification("error", "Invalid Format", "Invalid email address format! Please try again.");
            header("Location: ../forgot-password.php");
            exit();
        }

        else {

            try {
                $conn->begin_transaction();

                // Validate Existing Account in Database
                $validateAccount = $conn->prepare("SELECT * FROM users_table WHERE email_address = ? LIMIT 1");
                $validateAccount->bind_param("s", $emailAddress);
                $validateAccount->execute();

                $queryResult = $validateAccount->get_result();
                
                // Check if there's an account existing
                if($queryResult->num_rows === 1) {
                    $userData = $queryResult->fetch_object();

                    // Generate Password Token and Expiry
                    $resetPasswordToken = generateVerificationToken();
                    $passwordTokenExpiry = generateExpiryTime(5);

                    $fullName = $userData->first_name . " " . $userData->last_name;

                    $updatePasswordToken = $conn->prepare("UPDATE users_table
                                                        SET reset_password_token = ?,
                                                        reset_token_expiry = ?
                                                        WHERE email_address = ?");
                    $updatePasswordToken->bind_param("sss", $resetPasswordToken, $passwordTokenExpiry, $emailAddress);
                    $updatePasswordToken->execute();

                    $resetPasswordLink = "http://localhost/instambay_foodhub/reset-password.php?reset-password-token=$resetPasswordToken";

                    sendResetPasswordLink($emailAddress, $fullName, $resetPasswordLink);

                    $conn->commit();

                    displayNotification("success", "Email Verified", "A reset password link has been sent to your email address: " . $emailAddress . " Please check it now.");
                    header("Location: ../index.php");
                    exit();
                }

                else {
                    // Error Handling: No account existing
                    $conn->rollback();
                    displayNotification("error", "Invalid Account", "Invalid email address!");
                    header("Location: ../forgot-password.php");
                    exit();
                }
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occured", "Something went wrong: " . $e->getMessage());
                header("Location: ../forgot-password.php");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submitNewPassword"])) {
        
        // Sanitize Inputs
        $userId = htmlspecialchars(base64_decode($_POST["userId"]));
        $resetPasswordToken = htmlspecialchars($_POST["resetPasswordToken"]);
        $newPassword = sanitizeInputs($_POST["newPassword"]);
        $confirmPassword = sanitizeInputs($_POST["confirmPassword"]);

        if(empty($userId) || empty($resetPasswordToken) || empty($newPassword) || empty($confirmPassword)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../reset-password.php");
            exit();
        }

        else if(!validatePassword($newPassword)) {
            displayNotification("error", "Invalid Password", "Invalid password format! try again.");
            header("Location: ../reset-password.php?reset-password-token=$resetPasswordToken");
            exit();
        }

        else if($newPassword !== $confirmPassword) {
            displayNotification("error", "Invalid Passwords", "Passwords don't match! Please try again.");
            header("Location: ../reset-password.php?reset-password-token=$resetPasswordToken");
            exit();
        }

        else {
            try {
                $conn->begin_transaction();

                $validateAccount = $conn->prepare("SELECT * FROM users_table WHERE user_id = ? AND reset_password_token = ? LIMIT 1");
                $validateAccount->bind_param("is", $userId, $resetPasswordToken);
                $validateAccount->execute();

                $queryResult = $validateAccount->get_result();

                if($queryResult->num_rows === 1) {
                    // Hash password for enhanced security
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                    // Update Account Data
                    $updateAccount = $conn->prepare("UPDATE users_table
                                                    SET password = ?,
                                                    reset_password_token = null, reset_token_expiry = null
                                                    WHERE user_id = ?");
                    $updateAccount->bind_param("si", $hashedPassword, $userId);
                    $updateAccount->execute();

                    addUserActivity($userId, "Profile Update", "Changed password");

                    $conn->commit();

                    displayNotification("success", "Password Updated", "Change password complete! Please login to continue.");
                    header("Location: ../index.php");
                    exit();
                }

                else {
                    displayNotification("error", "Invalid Account", "Account not found! Please try again.");
                    header("Location: ../reset-password.php?reset-password-token=$resetPasswordToken");
                    exit();
                }
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occurred", "Something went wrong: " . $e->getMessage());
                header("Location: ../reset-password.php?reset-password-token=$resetPasswordToken");
                exit();
            }
        }
    }

    else {
        // Prevent Accessing file via manual input link
        header("Location: ../index.php");
        exit();
    }
?>