<?php
    if($userType !== "Admin") {
        displayNotification("error", "Invalid Access", "You are not allowed to access this page!");
        header("Location: dashboard.php?page=main");
        exit();
    }
?>