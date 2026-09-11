<?php
    require_once "../config/databaseConnector.php";
    require_once "../config/functions.php";
    require_once "../config/inputValidators.php";
    require_once "../config/notifications.php";
    require_once "../config/formatter.php";
    require_once "../includes/activityLogger.php";

    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateInformation"])) {;

        $userId = $_SESSION["userId"];
        $firstName = sanitizeInputs($_POST["firstName"]);
        $middleName = sanitizeInputs($_POST["middleName"]);
        $lastName = sanitizeInputs($_POST["lastName"]);
        $gender = sanitizeInputs($_POST["gender"]);
        $phoneNumber = sanitizeInputs($_POST["phoneNumber"]);
        $address = htmlspecialchars($_POST["address"]);
        
        if(empty($userId) || empty($firstName) || empty($lastName) || empty($gender) || empty($phoneNumber) || empty($address)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else if(!validateGender($gender)) {
            displayNotification("error", "Invalid Gender", "Invalid gender! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else if(!validatePhoneNumber($phoneNumber)) {
            displayNotification("error", "Invalid Number Format", "Invalid number format! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else {
            try {

                $conn->begin_transaction();

                $updateProfile = $conn->prepare("UPDATE users_table
                                                SET first_name = ?,
                                                middle_name = ?,
                                                last_name = ?,
                                                gender = ?,
                                                phone_number = ?,
                                                user_address = ?
                                            WHERE user_id = ?
                                            ");
                $updateProfile->bind_param("ssssssi", $firstName, $middleName, $lastName, $gender, $phoneNumber, $address, $userId);
                $updateProfile->execute();

                addUserActivity($userId, "Update Profile", "Update profile information");

                $conn->commit();

                displayNotification("success", "Profile Updated", "Profile updated successfully!");
                header("Location: ../admin/dashboard.php?page=profile");
                exit();

            }

            catch(mysqli_sql_exception $e) {

                $conn->rollback();

                displayNotification("error", "Error Occurred", "Something went wrong! Please try again.");
                header("Location: ../admin/dashboard.php?page=profile");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateProfilePicture"])) {

        $userId = $_SESSION["userId"];

        $fileName = $_FILES["profileImage"]["name"];
        $fileTmpName = $_FILES["profileImage"]["tmp_name"];
        $fileError = $_FILES["profileImage"]["error"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(empty($userId) || empty($fileName)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else if(!validateFileType($fileExtension) || $fileError !== 0) {
            displayNotification("error", "Invalid File", "Invalid or corrupted file! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else {
            try {
                $conn->begin_transaction();

                $uniqueFileName = "image_" . time() . "_" . uniqid() . "." . $fileExtension;

                $updateProfilePicture = $conn->prepare("UPDATE users_table
                                                        SET profile_picture = ?
                                                    WHERE user_id = ?");
                $updateProfilePicture->bind_param("si", $uniqueFileName, $userId);

                $uploadPath = "../uploads/user-images/" . $uniqueFileName;

                if(move_uploaded_file($fileTmpName, $uploadPath)) {
                    $updateProfilePicture->execute();

                    addUserActivity($userId, "Update Profile", "Updated profile picture");

                    $conn->commit();

                    displayNotification("success", "Profile Updated", "Profile pictured updated successfully!");
                    header("Location: ../admin/dashboard.php?page=profile");
                    exit();

                }

                else {
                    $conn->rollback();
                    displayNotification("error", "Upload Error", "Error uploading photo! Please try again.");
                    header("Location: ../admin/dashboard.php?page=profile");
                    exit();
                }
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occured", "Something went wrong! Please try again.");
                header("Location: ../admin/dashboard.php?page=profile");
                exit();
            }
        }
    }

    else if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updatePassword"])) {
        $userId = $_SESSION["userId"];

        $currentPassword = sanitizeInputs($_POST["currentPassword"]);
        $newPassword = sanitizeInputs($_POST["newPassword"]);
        $confirmPassword = sanitizeInputs($_POST["confirmPassword"]);

        if(empty($userId) || empty($currentPassword) || empty($confirmPassword) || empty($confirmPassword)) {
            displayNotification("error", "Invalid Input", "Invalid input! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else if(!validatePassword($newPassword)) {
            displayNotification("error", "Invalid Format", "Invalid password format! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else if($newPassword !== $confirmPassword) {
            displayNotification("error", "Invalid Password", "Passwords don't match! Please try again.");
            header("Location: ../admin/dashboard.php?page=profile");
            exit();
        }

        else {
            try {
                $conn->begin_transaction();

                $validateAccount = $conn->prepare("SELECT * FROM users_table WHERE user_id = ? LIMIT 1");
                $validateAccount->bind_param("i", $userId);
                $validateAccount->execute();

                $queryResult = $validateAccount->get_result();

                if($queryResult->num_rows === 1) {
                    $userData = $queryResult->fetch_object();

                    if(password_verify($currentPassword, $userData->password)) {
                        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                        $updatePassword = $conn->prepare("UPDATE users_table
                                                        SET password = ? WHERE user_id = ?");
                        $updatePassword->bind_param("si", $hashedPassword, $userId);
                        $updatePassword->execute();

                        addUserActivity($userId, "Update Profile", "Updated password");

                        $conn->commit();

                        displayNotification("success", "Profile Updated", "Password updated successfully! ");
                        header("Location: ../admin/dashboard.php?page=profile");
                        exit();
                    }

                    else {
                        $conn->rollback();
                        displayNotification("error", "Invalid Password", "Invalid password! Please try again.");
                        header("Location: ../admin/dashboard.php?page=profile");
                        exit();
                    }
                }

                else {
                    $conn->rollback();
                    displayNotification("error", "Invalid Account", "Account not found! Please try again.");
                    header("Location: ../admin/dashboard.php?page=profile");
                    exit();
                }
            }

            catch(mysqli_sql_exception $e) {
                $conn->rollback();
                displayNotification("error", "Error Occured", "Something went wrong! Please try again.");
                header("Location: ../admin/dashboard.php?page=profile");
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