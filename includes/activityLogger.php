<?php
    function addUserActivity($userId, $activity, $description) {
        global $conn;

        try {

            $conn->begin_transaction();

            $addNewLog = $conn->prepare("INSERT INTO user_logs_table(user_id, activity, activity_description)
                                        VALUES(?, ?, ?)");
            $addNewLog->bind_param("iss", $userId, $activity, $description);
            $addNewLog->execute();

            $conn->commit();

            return [
                "status" => "success",
                "message" => "Log added successfully!"
            ];
        }

        catch(mysqli_sql_exception $e) {
            $conn->rollback();

            return [
                "status" => "error",
                "message" => "Something went wrong: " . $e->getMessage()
            ];
        }
    }
?>