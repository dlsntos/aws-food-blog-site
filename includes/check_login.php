<?php // Start the session
if (!isset($_SESSION["user_id"])) {
    header("Location: ../public/login.php"); // Redirect to login page
    exit(); // Ensure no further code is executed
}

