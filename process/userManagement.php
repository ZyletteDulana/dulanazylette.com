<?php
    require_once "../config/databaseConnector.php";
    require_once "../config/functions.php";
    require_once "../config/emailSender.php";
    require_once "../config/inputValidators.php";
    require_once "../config/notifications.php";
    require_once "../config/formatter.php";
    require_once "../includes/activityLogger.php";

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addNewUser"])) {

        $userId = $_SESSION["userId"];
        $firstName = sanitizeNames($_POST["firstName"]);
        $middleName = sanitizeNames($_POST["middleName"]);
        $lastName = sanitizeNames($_POST["lastName"]);
        $gender = sanitizeNames($_POST["gender"]);
        $emailAddress = filter_var($_POST["emailAddress"], FILTER_SANITIZE_EMAIL);
        $userType = sanitizeNames($_POST["userType"]);
        $userAddress = htmlspecialchars($_POST["userAddress"]);
        $phoneNumber = htmlspecialchars($_POST["phoneNumber"]);

        // Generated
        $userPassword = strtoupper($lastName) . substr($phoneNumber, -4);

        if(empty($userId) || empty($firstName) || empty($lastName) || empty($emailAddress) || empty($phoneNumber)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-user");
            exit();
        }

        else if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            displayNotification("error", "Invalid Email", "Invalid email address format! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-user");
            exit();
        }

        else if(!validateGender($gender)) {
            displayNotification("error", "Invalid Gender", "Invalid gender! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-user");
            exit();
        }

        else if(!validateUserType($userType)) {
            displayNotification("error", "Invalid User", "Invalid user type! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-user");
            exit();
        }

        else if(!validatePhoneNumber($phoneNumber)) {
            displayNotification("error", "Invalid Format", "Invalid phone number format! Please try again.");
            header("Location: ../admin/dashboard.php?page=add-user");
            exit();
        }

        else {
            try {

                $conn->begin_transaction();

                $firstNameValidator = strtolower($firstName);
                $lastNameValidator = strtolower($lastName);

                $validateEmailAddress = $conn->prepare("SELECT * FROM users_table WHERE email_address = ? LIMIT 1");
                $validateEmailAddress->bind_param("s", $emailAddress);
                $validateEmailAddress->execute();

                $emailAddressResult = $validateEmailAddress->get_result();

                if($emailAddressResult->num_rows <= 0) {
                    $validateName = $conn->prepare("SELECT * FROM users_table WHERE LOWER(first_name) = ? AND LOWER(last_name) = ? LIMIT 1");
                    $validateName->bind_param("ss", $firstNameValidator, $lastNameValidator);
                    $validateName->execute();
                    
                    $nameResult = $validateName->get_result();

                    if($nameResult->num_rows <= 0) {

                        $hashedPassword = PASSWORD_HASH($userPassword, PASSWORD_BCRYPT);

                        $addNewUser = $conn->prepare("INSERT INTO users_table(first_name, middle_name, last_name, gender, email_address, password, phone_number, user_address, user_type)
                                                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $addNewUser->bind_param("sssssssss", $firstName, $middleName, $lastName, $gender, $emailAddress, $hashedPassword, $phoneNumber, $userAddress, $userType);
                        $addNewUser->execute();    

                        notifyUserCreation($emailAddress, $firstName . " " . $lastName, $userPassword, $userType);
                        addUserActivity($userId, "Add New User", "Added a user: " . $firstName . " " . $lastName);

                        $conn->commit();

                        displayNotification("success", "User Added", "User added successfully!");
                        header("Location: ../admin/dashboard.php?page=users-list");
                        exit();
                    }

                    else {
                        $conn->rollback();
                        displayNotification("error", "Account Exists", "This account already exists! Please try again.");
                        header("Location: ../admin/dashboard.php?page=add-user");
                        exit();
                    }
                }

                else {
                    $conn->rollback();
                    displayNotification("error", "Account Exists", "This account already exists! Please try again.");
                    header("Location: ../admin/dashboard.php?page=add-user");
                    exit();
                }

            }

            catch(mysqli_sql_exception $e) {
                
                $conn->rollback();

                displayNotification("error", "Error Occured", "Something went wrong! Please try again." . $e->getMessage());
                header("Location: ../admin/dashboard.php?page=add-user");
                exit();
            }
        }

        
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["deleteUser"])) {

        $userId = $_SESSION["userId"];
        $userToDeleteName = sanitizeNames($_POST["userName"]);
        $userToDeleteId = htmlspecialchars($_POST["deleteUserId"]);

        if(empty($userId) || empty($userToDeleteId) || empty($userToDeleteId)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=users-list");
            exit();
        }

        else {
            try {
                $conn->begin_transaction();

                $nameValidator = strtolower($userToDeleteName);

                $validateUser = $conn->prepare("SELECT * FROM users_table WHERE LOWER(CONCAT(first_name, ' ', last_name)) = ? AND user_id = ? LIMIT 1");
                $validateUser->bind_param("si", $nameValidator, $userToDeleteId);
                $validateUser->execute();

                $queryResult = $validateUser->get_result();

                if($queryResult->num_rows === 1) {

                    $deleteUser = $conn->prepare("DELETE FROM users_table WHERE user_id = ?");
                    $deleteUser->bind_param("i", $userToDeleteId);
                    $deleteUser->execute();

                    addUserActivity($userId, "Delete User", "Deleted a user: " . $userToDeleteName);

                    $conn->commit();

                    displayNotification("success", "User Deleted", "User deleted successfully!");
                    header("Location: ../admin/dashboard.php?page=users-list");
                    exit();
                }

                else {
                    $conn->rollback();
                    displayNotification("error", "Invalid User", "User not found! Please try again.");
                    header("Location: ../admin/dashboard.php?page=users-list");
                    exit();
                }
            }   

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occured", "Something went wrong! Please try again." . $e->getMessage());
                header("Location: ../admin/dashboard.php?page=users-list");
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